<?php

namespace App\Http\Controllers;

use App\Helpers\UploadHelper;
use App\Http\Requests\ResourceRequest;
use App\Resource;
use App\ResourceRelations;
use App\Services\ResourceService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ResourceController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $speaking = (new ResourceService($request))->getResource();
        return response()->json($speaking);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(ResourceRequest $request)
    {

        $resource = new Resource();
        $resource->fill($request->all());
        if ($request->hasFile('attachment')) {
            $resource->attachment = UploadHelper::fileUpload($request->file('attachment'), 'avatars');
        }
        $resource->save();
        $assign_role = explode(',', $request->assign_role);
        foreach ($assign_role as $role) {
            $resource_relation = new ResourceRelations();
            $resource_relation->fill(['resource_id' => $resource->id, 'role' => $role]);
            $resource_relation->save();
        }

        return response()->json(['msg' => 'Resource created successfully.'], 200);
    }

    /**
     * Display the specified resource.
     *
     * @param \App\Resource $resource
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {

        $resource = Resource::with('resource_relations')->findOrFail($id);
        $role = array();
        foreach ($resource->resource_relations as $res_relation) {
            $role[] = $res_relation->role;
        }
        $resource->assign_role = $role;
        return response()->json($resource, 200);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param \App\Resource $resource
     * @return \Illuminate\Http\Response
     */
    public function edit(Resource $resource)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Resource $resource
     * @return \Illuminate\Http\Response
     */
    public function update(ResourceRequest $request, $id)
    {
        $resource = Resource::findOrFail($id);
        $resource->fill($request->all());
        if ($request->hasFile('attachment')) {
            $resource->attachment = UploadHelper::fileUpload($request->file('attachment'), 'avatars');
        }
        $resource->save();
        $assign_role = explode(',', $request->assign_role);
        foreach ($assign_role as $role) {
            $resource_relation_check = ResourceRelations::where('resource_id', $id)->where('role', $role)->first();
            if (empty($resource_relation_check)) {
                $resource_relation = new ResourceRelations();
                $resource_relation->fill(['resource_id' => $id, 'role' => $role]);
                $resource_relation->save();
            }

        }
        $resource_relation_check = ResourceRelations::where('resource_id', $id)->get();
        foreach ($resource_relation_check as $relation_role) {
            if (!in_array($relation_role->role, $assign_role)){
                $resource_rel = ResourceRelations::findOrFail($relation_role->id);
                $resource_rel->delete();
            }

        }
        return response()->json(['msg' => 'Resource Updated successfully.'], 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Resource $resource
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $resource = Resource::findOrFail($id);
        if ($resource->attachment) {
            Storage::delete('public/' . $resource->attachment);
        }
        $resource->delete();
        return response()->json(['msg' => 'Resource has been deleted successfully'], 200);
    }
}
