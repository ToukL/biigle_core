<?php

namespace Biigle\Http\Controllers\Api;

use Exception;
use Biigle\Project;
use Biigle\Volume;
use Illuminate\Http\Request;
use Illuminate\Contracts\Auth\Guard;

class ProjectVolumeController extends Controller
{
    /**
     * Shows a list of all volumes belonging to the specified project..
     *
     * @api {get} projects/:id/volumes Get all volumes
     * @apiGroup Projects
     * @apiName IndexProjectVolumes
     * @apiPermission projectMember
     *
     * @apiParam {Number} id The project ID.
     *
     * @apiSuccessExample {json} Success response:
     * [
     *    {
     *       "id": 1,
     *       "name": "volume 1",
     *       "media_type_id": 3,
     *       "creator_id": 7,
     *       "created_at": "2015-02-19 14:45:58",
     *       "updated_at":"2015-02-19 14:45:58",
     *       "url": "/vol/volumes/1"
     *    }
     * ]
     *
     * @param int $id Project ID
     * @return \Illuminate\Http\Response
     */
    public function index($id)
    {
        $project = Project::findOrFail($id);
        $this->authorize('access', $project);

        return $project->volumes;
    }

    /**
     * Creates a new volume associated to the specified project.
     *
     * @api {post} projects/:id/volumes Create a new volume
     * @apiGroup Volumes
     * @apiName StoreProjectVolumes
     * @apiPermission projectAdmin
     *
     * @apiParam {Number} id The project ID.
     *
     * @apiParam (Required attributes) {String} name The name of the new volume.
     * @apiParam (Required attributes) {String} url The base URL ot the image files. Can be a local path like `/vol/volumes/1` or a remote path like `https://example.com/volumes/1`.
     * @apiParam (Required attributes) {Number} media_type_id The ID of the media type of the new volume.
     * @apiParam (Required attributes) {String} images List of image file names of the images that can be found at the base URL, formatted as comma separated values. With the base URL `/vol/volumes/1` and the image `1.jpg`, the local file `/vol/volumes/1/1.jpg` will be used.
     *
     * @apiParamExample {String} Request example:
     * name: 'New volume'
     * url: '/vol/volumes/test-volume'
     * media_type_id: 1
     * images: '1.jpg,2.jpg,3.jpg'
     *
     * @apiSuccessExample {json} Success response:
     * {
     *    "id": 2,
     *    "name": "New volume",
     *    "media_type_id": 1,
     *    "creator_id": 2,
     *    "created_at": "2015-02-19 16:10:17",
     *    "updated_at": "2015-02-19 16:10:17",
     *    "url": "/vol/volumes/test-volume"
     * }
     *
     * @param Request $request
     * @param Guard $auth
     * @param int $id Project ID
     * @return Volume
     */
    public function store(Request $request, Guard $auth, $id)
    {
        $project = Project::findOrFail($id);
        $this->authorize('update', $project);
        $this->validate($request, Volume::$createRules);

        $volume = new Volume;
        $volume->name = $request->input('name');
        $volume->url = $request->input('url');
        $volume->setMediaTypeId($request->input('media_type_id'));
        $volume->creator()->associate($auth->user());

        try {
            $volume->validateUrl();
        } catch (Exception $e) {
            return $this->buildFailedValidationResponse($request, [
                'url' => $e->getMessage(),
            ]);
        }

        $images = Volume::parseImagesQueryString($request->input('images'));

        try {
            $volume->validateImages($images);
        } catch (Exception $e) {
            return $this->buildFailedValidationResponse($request, [
                'images' => $e->getMessage(),
            ]);
        }

        // save first, so the volume gets an ID for associating with images
        $volume->save();

        try {
            $volume->createImages($images);
        } catch (\Exception $e) {
            $volume->delete();

            return response($e->getMessage(), 400);
        }

        // it's important that this is done *after* all images were added
        $volume->handleNewImages();

        $project->volumes()->attach($volume);

        if (static::isAutomatedRequest($request)) {
            // media type shouldn't be returned
            unset($volume->media_type);

            return $volume;
        } else {
            return redirect()->route('home')
                ->with('message', 'Volume '.$volume->name.' created')
                ->with('messageType', 'success');
        }
    }

    /**
     * Attaches the existing specified volume to the existing specified
     * project.
     *
     * @api {post} projects/:pid/volumes/:tid Attach a volume
     * @apiGroup Projects
     * @apiName AttachProjectVolumes
     * @apiPermission projectAdmin
     * @apiDescription This endpoint attaches an existing volume to another existing project. The volume then will belong to multiple projects. The user performing this operation needs to be project admin in both the project, the volume initially belongs to, and the project, the volume should be attached to.
     *
     * @apiParam {Number} pid ID of the project that should get the annotation.
     * @apiParam {Number} tid ID of the existing volume to attach to the project.
     *
     * @param Request $request
     * @param int $projectId
     * @param int $volumeId
     * @return \Illuminate\Http\Response
     */
    public function attach(Request $request, $projectId, $volumeId)
    {
        // user must be able to admin the volume *and* the project it should
        // be attached to
        $volume = Volume::findOrFail($volumeId);
        $this->authorize('update', $volume);
        $project = Project::findOrFail($projectId);
        $this->authorize('update', $project);

        if ($project->volumes()->where('id', $volumeId)->exists()) {
            return $this->buildFailedValidationResponse($request, [
                'tid' => 'The volume is already attached to the project.',
            ]);
        }

        $project->volumes()->attach($volume);
    }

    /**
     * Removes the specified volume from the specified project.
     * If it is the last project the volume belongs to, the volume is
     * deleted (if the `force` argument is present in the request).
     *
     * @api {delete} projects/:pid/volumes/:tid Detach/delete a volume
     * @apiGroup Projects
     * @apiName DestroyProjectVolumes
     * @apiPermission projectAdmin
     * @apiDescription Detaches a volume from a project. The volume will no longer belong to the project it was detached from. If the volume belongs only to a single project, it cannot be detached but should be deleted. Use the `force` parameter to delete a volume belonging only to one project.
     *
     * @apiParam {Number} pid The project ID, the volume should be detached from.
     * @apiParam {Number} tid The volume ID.
     *
     * @apiParam (Optional parameters) {Boolean} force If the volume only belongs to a single project, set this parameter to delete it instead of detaching it. Otherwise the volume cannot be removed.
     *
     * @param Request $request
     * @param  int  $projectId
     * @param  int  $volumeId
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $projectId, $volumeId)
    {
        $project = Project::findOrFail($projectId);
        $volume = $project->volumes()->findOrFail($volumeId);
        $this->authorize('destroy', $volume);

        $project->removeVolume($volume, $request->has('force'));

        return response('Removed.', 200);
    }
}