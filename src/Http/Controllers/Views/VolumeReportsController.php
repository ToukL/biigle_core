<?php

namespace Biigle\Modules\Reports\Http\Controllers\Views;

use Biigle\Http\Controllers\Views\Controller;
use Biigle\LabelTree;
use Biigle\Modules\Reports\ReportType;
use Biigle\Modules\Reports\Volume;
use Biigle\Project;
use Biigle\Role;
use Biigle\Volume as BaseVolume;
use Illuminate\Http\Request;

class VolumeReportsController extends Controller
{
    /**
     * Show the volumes reports view.
     *
     * @param Request $request
     * @param int $id Volume ID
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, $id)
    {
        $volume = BaseVolume::findOrFail($id);
        $this->authorize('access', $volume);
        $sessions = $volume->annotationSessions()->orderBy('starts_at', 'desc')->get();
        $types = ReportType::when($volume->isImageVolume(), function ($query) {
                $query->where('name', 'like', 'Image%');
            })
            ->when($volume->isVideoVolume(), function ($query) {
                $query->where('name', 'like', 'Video%');
            })
            ->orderBy('name', 'asc')
            ->get();

        $user = $request->user();

        if ($user->can('sudo')) {
            // Global admins have no restrictions.
            $projectIds = $volume->projects()->pluck('id');
        } else {
            // Array of all project IDs that the user and the volume have in common
            // and where the user is editor, expert or admin.
            $projectIds = Project::inCommon($user, $volume->id, [
                Role::editorId(),
                Role::expertId(),
                Role::adminId(),
            ])->pluck('id');
        }

        // All label trees that are used by all projects in which the user can edit in.
        $labelTrees = LabelTree::select('id', 'name', 'version_id')
            ->with('labels', 'version')
            ->whereIn('id', function ($query) use ($projectIds) {
                $query->select('label_tree_id')
                    ->from('label_tree_project')
                    ->whereIn('project_id', $projectIds);
            })
            ->get();

        $reportPrefix = $volume->isImageVolume() ? 'Image' : 'Video';

        return view('reports::volumeReports', [
            'projects' => $volume->projects,
            'volume' => Volume::convert($volume),
            'annotationSessions' => $sessions,
            'reportTypes' => $types,
            'labelTrees' => $labelTrees,
            'reportPrefix' => $reportPrefix,
        ]);
    }
}
