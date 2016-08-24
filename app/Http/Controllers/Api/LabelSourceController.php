<?php

namespace Dias\Http\Controllers\Api;

use Dias\LabelSource;
use Illuminate\Http\Request;

class LabelSourceController extends Controller
{
    /**
     * Find labels from a label source
     *
     * @api {get} label-sources/:id/find Find labels from external sources
     * @apiGroup Label Trees
     * @apiName FondLabelTreesLabelSources
     * @apiDescription Returns an array with one object for each matching label. The label
     * objects may contain arbitrary data, depending on the label source.
     *
     * @apiParam {Number} id The label source ID
     *
     * @param Request $request
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function find(Request $request, $id)
    {
        $source = LabelSource::findOrFail($id);
        $this->validate($request, LabelSource::$findRules);

        return $source->getAdapter()->find($request->input('query'));
    }
}
