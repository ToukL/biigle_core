<?php

namespace Biigle\Modules\Sync\Jobs;

use Biigle\Image;
use Biigle\Jobs\Job;
use Biigle\Jobs\ProcessNewVolumeFiles;
use Biigle\Modules\Largo\Jobs\ProcessAnnotatedImage;
use Biigle\Modules\Largo\Jobs\ProcessAnnotatedVideo;
use Biigle\Video;
use Biigle\Volume;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Collection;

class PostprocessVolumeImport extends Job implements ShouldQueue
{
    use InteractsWithQueue;

    /**
     * IDs of the imported volumes.
     *
     * @var array
     */
    protected $ids;

    /**
     * Create a new job instance.
     *
     * @param Collection $volumes
     *
     * @return void
     */
    public function __construct(Collection $volumes)
    {
        $this->ids = $volumes->pluck('id')->toArray();
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        Volume::whereIn('id', $this->ids)->select('id')->each(function ($volume) {
            ProcessNewVolumeFiles::dispatch($volume);
        });

        if (class_exists(ProcessAnnotatedImage::class)) {
            Image::whereIn('images.volume_id', $this->ids)
                ->whereHas('annotations')
                ->eachById(function ($image) {
                    ProcessAnnotatedImage::dispatch($image)
                        ->onQueue(config('largo.generate_annotation_patch_queue'));
                }, 1000);
        }

        if (class_exists(ProcessAnnotatedVideo::class)) {
            Video::whereIn('videos.volume_id', $this->ids)
                ->whereHas('annotations')
                ->eachById(function ($video) {
                    ProcessAnnotatedVideo::dispatch($video)
                        ->onQueue(config('largo.generate_annotation_patch_queue'));
                }, 1000);
        }
    }
}
