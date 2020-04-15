<?php

namespace Biigle\Http\Requests;

use Biigle\Volume;
use Biigle\Rules\VolumeUrl;
use Illuminate\Foundation\Http\FormRequest;

class UpdateVolume extends FormRequest
{
    /**
     * The volume to update.
     *
     * @var Volume
     */
    public $volume;

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        $this->volume = Volume::findOrFail($this->route('id'));

        return $this->user()->can('update', $this->volume);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name' => 'filled|max:512',
            'media_type_id' => 'filled|id|exists:media_types,id',
            'url' => ['filled', new VolumeUrl],
        ];
    }
}
