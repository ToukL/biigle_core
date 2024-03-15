<?php

namespace Biigle;

use Biigle\Traits\HasMetadataFile;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Http\UploadedFile;
use Storage;

class PendingVolume extends Model
{
    use HasFactory, HasMetadataFile;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'media_type_id',
        'user_id',
        'project_id',
        'metadata_file_path',
        'volume_id',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'metadata_file_path',
    ];

    protected static function booted(): void
    {
        static::$metadataFileDisk = config('volumes.pending_metadata_storage_disk');

        static::deleting(function (PendingVolume $pv) {
            $pv->deleteMetadata(true);
        });
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }
}
