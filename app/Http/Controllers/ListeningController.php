<?php

namespace App\Http\Controllers;

use App\Helpers\UploadHelper;
use App\Http\Requests\ListeningRequest;
use App\Listening;
use App\Services\ListeningService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ListeningController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        $listening = (new ListeningService($request))->getParentlistening();

        return response()->json($listening);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(ListeningRequest $request)
    {
        $data = $request->except('audio_file');

        if ($request->hasFile('audio_file')) {
            $data['audio_file'] = UploadHelper::fileUpload($request->file('audio_file'), 'audios');
        }

        Listening::create($data);

        return response()->json(['msg' => 'Listening created successfully.']);
    }

    /**
     * Display the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        $listening = Listening::findOrFail($id);

        return response()->json($listening);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(ListeningRequest $request, $id)
    {
        $listening = Listening::findOrFail($id);
        $data = $request->except('audio_file');

        if ($request->hasFile('audio_file')) {
            if ($listening->audio_file) {
                Storage::delete('public/' . $listening->audio_file);
            }

            $data['audio_file'] = UploadHelper::fileUpload($request->file('audio_file'), 'audios');
        }

        $listening->fill($data);
        $listening->save();

        return response()->json(['msg' => 'Listening updated successfully.']);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        $listening = Listening::findOrFail($id);

        if ($listening->audio_file) {
            Storage::delete('public/' . $listening->audio_file);
        }

        $listening->delete();

        return response()->json(['msg' => 'Listening deleted successfully.']);
    }
}
