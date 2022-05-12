<?php

namespace App\Http\Controllers;

use App\Email;
use App\Mail\GeneralEmail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class EmailController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        $emails = Email::where(function ($q) use($request) {
            $q->where('subject', 'like', "%{$request->keyword}%")
                ->orWhere('body', 'like', "%{$request->keyword}%")
                ->orWhere('to', 'like', "%{$request->keyword}%");
        })->latest()
            ->paginate(10);

        return response()->json($emails);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        // Validate form data
        $request->validate([
            'subject' => 'required|string|max:255',
            'body' => 'required|string',
            'to' => 'required|array',
            'to.*' => 'required|email',
        ]);

        $data = $request->all();
        $data['to'] = json_encode($data['to']);
        $email = Email::create($data);
        Mail::to($request->to)->send(new GeneralEmail($email));

        return response()->json(['msg' => 'Email sent successfully.']);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        Email::findOrFail($id)->delete();

        return response()->json(['msg' => 'Email deleted successfully.']);
    }
}
