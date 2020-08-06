<?php

namespace Biigle\Tests\Modules\Reports\Support\Reports\Volumes\VideoLabels;

use App;
use Biigle\Modules\Reports\Support\CsvFile;
use Biigle\Modules\Reports\Support\Reports\Volumes\VideoLabels\CsvReportGenerator;
use Biigle\Tests\VideoLabelTest;
use Biigle\Tests\VideoTest;
use Biigle\Tests\LabelTest;
use Biigle\Tests\LabelTreeTest;
use Biigle\Tests\VolumeTest;
use Mockery;
use TestCase;
use ZipArchive;

class CsvReportGeneratorTest extends TestCase
{
    private $columns = [
        'video_label_id',
        'video_id',
        'filename',
        'user_id',
        'firstname',
        'lastname',
        'label_id',
        'label_name',
        'label_hierarchy',
    ];

    public function testProperties()
    {
        $generator = new CsvReportGenerator;
        $this->assertEquals('CSV video label report', $generator->getName());
        $this->assertEquals('csv_video_label_report', $generator->getFilename());
        $this->assertStringEndsWith('.zip', $generator->getFullFilename());
    }

    public function testGenerateReport()
    {
        $volume = VolumeTest::create();

        $root = LabelTest::create();
        $child = LabelTest::create([
            'parent_id' => $root->id,
            'label_tree_id' => $root->label_tree_id,
        ]);

        $il = VideoLabelTest::create([
            'video_id' => VideoTest::create([
                'volume_id' => $volume->id,
                'filename' => 'foo.mp4',
            ])->id,
            'label_id' => $child->id,
        ]);

        $mock = Mockery::mock();

        $mock->shouldReceive('getPath')
            ->once()
            ->andReturn('abc');

        $mock->shouldReceive('put')
            ->once()
            ->with($this->columns);

        $mock->shouldReceive('put')
            ->once()
            ->with([
                $il->id,
                $il->video_id,
                $il->video->filename,
                $il->user_id,
                $il->user->firstname,
                $il->user->lastname,
                $il->label_id,
                $child->name,
                "{$root->name} > {$child->name}",
            ]);

        $mock->shouldReceive('close')
            ->once();

        App::singleton(CsvFile::class, function () use ($mock) {
            return $mock;
        });

        $mock = Mockery::mock();

        $mock->shouldReceive('open')
            ->once()
            ->andReturn(true);

        $mock->shouldReceive('addFile')->once();
        $mock->shouldReceive('close')->once();

        App::singleton(ZipArchive::class, function () use ($mock) {
            return $mock;
        });

        $generator = new CsvReportGenerator;
        $generator->setSource($volume);
        $generator->generateReport('my/path');
    }

    public function testGenerateReportSeparateLabelTrees()
    {
        $tree1 = LabelTreeTest::create(['name' => 'tree1']);
        $tree2 = LabelTreeTest::create(['name' => 'tree2']);

        $label1 = LabelTest::create(['label_tree_id' => $tree1->id]);
        $label2 = LabelTest::create(['label_tree_id' => $tree2->id]);

        $video = VideoTest::create();

        $il1 = VideoLabelTest::create([
            'video_id' => $video->id,
            'label_id' => $label1->id,
        ]);
        $il2 = VideoLabelTest::create([
            'video_id' => $video->id,
            'label_id' => $label2->id,
        ]);

        $mock = Mockery::mock();
        $mock->shouldReceive('getPath')
            ->twice()
            ->andReturn('abc', 'def');

        $mock->shouldReceive('put')
            ->twice()
            ->with($this->columns);

        $mock->shouldReceive('put')
            ->once()
            ->with([
                $il1->id,
                $video->id,
                $video->filename,
                $il1->user_id,
                $il1->user->firstname,
                $il1->user->lastname,
                $label1->id,
                $label1->name,
                $label1->name,
            ]);

        $mock->shouldReceive('put')
            ->once()
            ->with([
                $il2->id,
                $video->id,
                $video->filename,
                $il2->user_id,
                $il2->user->firstname,
                $il2->user->lastname,
                $label2->id,
                $label2->name,
                $label2->name,
            ]);

        $mock->shouldReceive('close')
            ->twice();

        App::singleton(CsvFile::class, function () use ($mock) {
            return $mock;
        });

        $mock = Mockery::mock();

        $mock->shouldReceive('open')
            ->once()
            ->andReturn(true);

        $mock->shouldReceive('addFile')
            ->once()
            ->with('abc', "{$tree1->id}-{$tree1->name}.csv");

        $mock->shouldReceive('addFile')
            ->once()
            ->with('def', "{$tree2->id}-{$tree2->name}.csv");

        $mock->shouldReceive('close')->once();

        App::singleton(ZipArchive::class, function () use ($mock) {
            return $mock;
        });

        $generator = new CsvReportGenerator([
            'separateLabelTrees' => true,
        ]);
        $generator->setSource($video->volume);
        $generator->generateReport('my/path');
    }

    public function testRestrictToLabels()
    {
        $video = VideoTest::create();
        $il1 = VideoLabelTest::create(['video_id' => $video->id]);
        $il2 = VideoLabelTest::create(['video_id' => $video->id]);

        $generator = new CsvReportGenerator([
            'onlyLabels' => [$il1->label_id],
        ]);
        $generator->setSource($video->volume);
        $results = $generator->query()->get();
        $this->assertCount(1, $results);
        $this->assertEquals($il1->id, $results[0]->video_label_id);
    }
}
