<?php namespace Dias;

use Illuminate\Database\Eloquent\Model;
use Dias\Contracts\BelongsToProject;

/**
 * Annotations consist of one or many of these annotation points, marking
 * a point or a region on an image.
 */
class AnnotationPoint extends Model implements BelongsToProject {

	/**
	 * Don't maintain timestamps for this model.
	 *
	 * @var boolean
	 */
	public $timestamps = false;

	/**
	 * The annotation, this point belongs to.
	 * 
	 * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
	 */
	public function annotation()
	{
		return $this->belongsTo('Dias\Annotation');
	}

	/**
	 * {@inheritdoc}
	 * @return array
	 */
	public function projectIds()
	{
		return $this->annotation->projectIds();
	}
}
