<?php

namespace Biigle\Modules\Reports\Support\Reports\Volumes\Annotations;

use DB;
use Biigle\Label;
use Biigle\LabelTree;
use Biigle\Modules\Reports\Support\CsvFile;

class AbundanceReportGenerator extends AnnotationReportGenerator
{
    /**
     * Name of the report for use in text.
     *
     * @var string
     */
    protected $name = 'abundance annotation report';

    /**
     * Name of the report for use as (part of) a filename.
     *
     * @var string
     */
    protected $filename = 'abundance_annotation_report';

    /**
     * File extension of the report file.
     *
     * @var string
     */
    protected $extension = 'xlsx';

    /**
     * Generate the report.
     *
     * @param string $path Path to the report file that should be generated
     */
    public function generateReport($path)
    {
        $rows = $this->query()->get();

        if ($this->shouldSeparateLabelTrees()) {
            $rows = $rows->groupBy('label_tree_id');
            $trees = LabelTree::whereIn('id', $rows->keys())->pluck('name', 'id');

            foreach ($trees as $id => $name) {
                $rowGroup = $rows->get($id);
                $labels = Label::whereIn('id', $rowGroup->pluck('label_id')->unique())->get();
                $this->tmpFiles[] = $this->createCsv($rowGroup, $name, $labels);
            }
        } else {
            $labels = Label::whereIn('id', $rows->pluck('label_id')->unique())->get();
            $this->tmpFiles[] = $this->createCsv($rows, $this->source->name, $labels);
        }

        $this->executeScript('csvs_to_xlsx', $path);
    }

    /**
     * Assemble a new DB query for the volume of this report.
     *
     * @return \Illuminate\Database\Query\Builder
     */
    protected function query()
    {
        $query = $this->initQuery()
            ->orderBy('images.filename')
            ->select(DB::raw('images.filename, annotation_labels.label_id, count(annotation_labels.label_id) as count'))
            ->groupBy('annotation_labels.label_id', 'images.id');

        if ($this->shouldSeparateLabelTrees()) {
            $query->addSelect('labels.label_tree_id')
                ->groupBy('annotation_labels.label_id', 'images.id', 'labels.label_tree_id');
        }

        return $query;
    }

    /**
     * Create a CSV file for a single sheet of the spreadsheet of this report.
     *
     * @param \Illuminate\Support\Collection $rows The rows for the CSV
     * @param string $title The title to put in the first row of the CSV
     * @param  array $labels
     *
     * @return CsvFile
     */
    protected function createCsv($rows, $title = '', $labels)
    {
        $rows = $rows->groupBy('filename');

        if ($this->shouldAggregateChildLabels()) {
            [$rows, $labels] = $this->aggregateChildLabels($rows, $labels);
        }

        $labels = $labels->sortBy('id');

        $csv = CsvFile::makeTmp();
        $csv->put([$title]);

        $columns = ['image_filename'];
        foreach ($labels as $label) {
            $columns[] = $label->name;
        }
        $csv->put($columns);

        foreach ($rows as $filename => $annotations) {
            $row = [$filename];
            $annotations = $annotations->keyBy('label_id');
            foreach ($labels as $label) {
                if ($annotations->has($label->id)) {
                    $row[] = $annotations[$label->id]->count;
                } else {
                    $row[] = 0;
                }
            }

            $csv->put($row);
        }

        $csv->close();

        return $csv;
    }

    /**
     * Aggregate the number of child labels to the number of the highest parent label
     * and remove the child labels from the list.
     *
     * @param \Illuminate\Support\Collection $rows
     * @param \Illuminate\Support\Collection $labels
     *
     * @return array
     */
    protected function aggregateChildLabels($rows, $labels)
    {
        // Add all possible labels because the parent to which the child labels should
        // be aggregated may not have "own" annotations. Unused labels are filtered
        // later.
        $addLabels = Label::whereIn('label_tree_id', $labels->pluck('label_tree_id')->unique())
            ->whereNotIn('id', $labels->pluck('id'))
            ->when($this->isRestrictedToLabels(), function ($query) {
                $query->whereIn('id', $this->getOnlyLabels());
            })
            ->get();

        $labels = $labels->concat($addLabels);

        $parentIdMap = $labels->pluck('parent_id', 'id')
            ->when($this->isRestrictedToLabels(), function ($labels) {
                $onlyLabels = $this->getOnlyLabels();

                return $labels->map(function ($value) use ($onlyLabels) {
                    // Act as if excluded parent labels do not exist.
                    return in_array($value, $onlyLabels) ? $value : null;
                });
            })
            ->reject(function ($value) {
                return is_null($value);
            });

        // Determine the highest parent label for all child labels.
        do {
            $hoistedParentLabel = false;
            foreach ($parentIdMap as $id => $parentId) {
                if ($parentIdMap->has($parentId)) {
                    $parentIdMap[$id] = $parentIdMap[$parentId];
                    $hoistedParentLabel = true;
                }
            }
        } while ($hoistedParentLabel);

        $presentLabels = collect([]);

        foreach ($rows as $filename => $annotations) {
            // Aggregate the number of annotations of child labels to the number of their
            // parent.
            $annotations = $annotations->keyBy('label_id');
            foreach ($annotations as $labelId => $annotation) {
                $parentId = $parentIdMap->get($labelId);
                if ($parentId) {
                    $presentLabels->push($parentId);
                    if ($annotations->has($parentId)) {
                        $annotations[$parentId]->count += $annotation->count;
                    } else {
                        // Add a new entry for a parent label which has no "own"
                        // annotations.
                        $annotations[$parentId] = (object) [
                            'count' => $annotation->count,
                            'label_id' => $parentId,
                            'filename' => $filename,
                        ];
                    }
                } else {
                    $presentLabels->push($labelId);
                }
            }

            // Remove rows of child labels so they are not counted twice.
            $rows[$filename] = $annotations->values()
                ->reject(function ($annotation) use ($parentIdMap) {
                    return $parentIdMap->has($annotation->label_id);
                });
        }

        // Remove all labels that did not occur (as parent) in the rows.
        $presentLabels = $presentLabels->unique()->flip();
        $labels = $labels->filter(function ($label) use ($presentLabels) {
            return $presentLabels->has($label->id);
        });

        return [$rows, $labels];
    }
}
