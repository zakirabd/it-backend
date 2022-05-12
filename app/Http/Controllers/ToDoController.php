<?php

namespace App\Http\Controllers;

use App\Http\Requests\ToDoRequest;
use App\ToDo;
use Illuminate\Http\Request;

class ToDoController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        $sql = ToDo::
            where(function ($q) use ($request) {
                $q->where('title', 'like', "%{$request->keyword}%")
                    ->orWhere('note', 'like', "%{$request->keyword}%");
        });

        $toDos = $request->user_id ?
            $sql
            ->where('user_id', $request->user_id)
            ->paginate(10) :
            $sql
                ->with('user')
                ->whereIn('user_id', auth()->user()->company->users->pluck('id'))
                ->get();

        return response()->json($toDos);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(ToDoRequest $request)
    {
        ToDo::create($request->all());

        return response()->json(['msg' => 'To do created successfully.']);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        return response()->json(ToDo::findOrFail($id));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(ToDoRequest $request, $id)
    {
        ToDo::where('id', $id)->update($request->only('title', 'note', 'status'));

        return response()->json(['msg' => 'To do updated successfully.']);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        ToDo::findOrFail($id)->delete();

        return response()->json(['msg' => 'To do deleted successfully.']);
    }
    public function statusChange(Request $request){
        //return $request->id;
        ToDo::where('id',$request->id)->update([

            'status'=>$request->status,
        ]);
        return response()->json(['msg' => 'To do status  updated successfully.']);
    }
}
