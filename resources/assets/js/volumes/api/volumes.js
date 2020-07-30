/**
 * Resource for volumes.
 *
 * let resource = biigle.$require('api.volumes');
 *
 * Get IDs of all images of the volume that have image labels attached:
 * resource.queryImagesWithImageLabels({id: 1).then(...);
 *
 * Get IDs of all images of the volume that have a certain image label attached:
 * resource.queryImagesWithImageLabel({id: 1, label_id: 123}).then(...);
 *
 * Get IDs of all images of the volume that have image labels attached by a certain user:
 * resource.queryImagesWithImageLabelFromUser({id: 1, user_id: 123}).then(...);
 *
 * Get IDs of all images of the volume that have a filename matching the given pattern:
 * resource.queryImagesWithFilename({id: 1, pattern: '*def.jpg'}).then(...);
 *
 * Get all image labels that were used in the volume:
 * resource.queryImageLabels({id: 1}).then(...);
 *
 * Get all image file names of the volume:
 * resource.queryFilenames({id: 1}).then(...);
 *
 * Get all users that have access to a volume:
 * resource.queryUsers({id: 1}).then(...);
 *
 * Get IDs of all images of the volume:
 * resource.queryImages({id: 1}).then(...);
 *
 * Add images to a volume:
 * resource.saveImages({id: 1}, {images: '1.jpg, 2.jpg'}).then(...);
 *
 * @type {Vue.resource}
 */
export default Vue.resource('api/v1/volumes{/id}', {}, {
    queryImagesWithImageLabels: {
        method: 'GET',
        url: 'api/v1/volumes{/id}/images/filter/labels',
    },
    queryImagesWithImageLabel: {
        method: 'GET',
        url: 'api/v1/volumes{/id}/images/filter/image-label{/label_id}',
    },
    queryImagesWithImageLabelFromUser: {
        method: 'GET',
        url: 'api/v1/volumes{/id}/images/filter/image-label-user{/user_id}',
    },
    queryFilesWithFilename: {
        method: 'GET',
        url: 'api/v1/volumes{/id}/files/filter/filename{/pattern}',
    },
    queryUsedImageLabels: {
        method: 'GET',
        url: 'api/v1/volumes{/id}/image-labels',
    },
    queryFilenames: {
        method: 'GET',
        url: 'api/v1/volumes{/id}/filenames',
    },
    queryFileLabels: {
        method: 'GET',
        url: 'api/v1/volumes{/id}/files/labels',
    },
    queryUsers: {
        method: 'GET',
        url: 'api/v1/volumes{/id}/users',
    },
    queryFiles: {
        method: 'GET',
        url: 'api/v1/volumes{/id}/files',
    },
    saveFiles: {
        method: 'POST',
        url: 'api/v1/volumes{/id}/files',
    },
    queryFilesWithAnnotations: {
        method: 'GET',
        url: 'api/v1/volumes{/id}/files/filter/annotations',
    },
    queryFilesWithAnnotationLabel: {
        method: 'GET',
        url: 'api/v1/volumes{/id}/files/filter/annotation-label{/label_id}',
    },
    queryFilesWithAnnotationFromUser: {
        method: 'GET',
        url: 'api/v1/volumes{/id}/files/filter/annotation-user{/user_id}',
    },
    queryAnnotationLabels: {
        method: 'GET',
        url: 'api/v1/volumes{/id}/annotation-labels',
    },
});
