<?php

namespace Biigle\Http\Requests;

use Biigle\AnnotationSession;
use Illuminate\Foundation\Http\FormRequest;

class UpdateAnnotationSession extends FormRequest
{
    /**
     * The annotation session that should be updated.
     *
     * @var AnnotationSession
     */
    public $session;

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        $this->session = AnnotationSession::findOrFail($this->route('id'));

        return $this->user()->can('update', $this->session->volume);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name' => 'filled',
            'starts_at' => 'filled|date',
            'ends_at' => 'filled|date',
            'users' => 'filled|array',
            'users.*' => 'distinct|exists:users,id',
            'hide_other_users_annotations' => 'filled|boolean',
            'hide_own_annotations' => 'filled|boolean',
            'force' => 'filled|boolean',
        ];
    }
}
