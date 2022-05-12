<?php

namespace App\Http\Controllers;

use App\Expenditure;
use App\Http\Requests\ExpenditureRequest;
use Illuminate\Http\Request;

class ExpenditureController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        $expenditures = auth()->user()->company->expenditures()
            ->where(function ($q) use ($request) {
                if ($request->keyword) {
                    $q->where('note', 'like', "%{$request->keyword}%")
                        ->orWhere('amount', 'like', "%{$request->keyword}%");
                }
            })->paginate(10);

        return response()->json($expenditures);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(ExpenditureRequest $request)
    {
        $data = $request->all();
        $data['company_id'] = auth()->user()->company_id;

        Expenditure::create($data);

        return response()->json(['msg' => 'Expenditure created successfully.']);
    }

    /**
     * Display the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        return response()->json(Expenditure::findOrFail($id));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(ExpenditureRequest $request, $id)
    {
        Expenditure::where('id', $id)->update($request->only('note', 'amount'));

        return response()->json(['msg' => 'Expenditure updated successfully.']);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        Expenditure::destroy($id);

        return response()->json(['msg' => 'Expenditure deleted successfully.']);
    }
}
