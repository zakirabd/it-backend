<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\User;
use Illuminate\Auth\Events\Verified;
use Illuminate\Foundation\Auth\VerifiesEmails;
use Illuminate\Http\Request;

class VerificationController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Email Verification Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling email verification for any
    | user that recently registered with the application. Emails may also
    | be re-sent if the user didn't receive the original email message.
    |
    */

    /**
     * Create a new controller instance.
     *
     * @return void
     */
//    public function __construct()
//    {
//        $this->middleware('signed')->only('verify');
//        $this->middleware('throttle:6,1')->only('verify', 'resend');
//    }

    /**
     * Mark the authenticated user's email address as verified.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     *
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function verify(Request $request)
    {
        //return auth()->user();

        $user = User::findOrFail($request->id);


        if (! hash_equals((string) $request->route('id'), (string) $user->id)) {
            return response()->json(['status' => 403, 'msg' => 'Access forbidden.']);
        }

        if (! hash_equals((string) $request->route('hash'), sha1($user->email))) {
            return response()->json(['status' => 403, 'msg' => 'Access forbidden.']);
        }

        if ($user->hasVerifiedEmail()) {
            return  view('mail-verified', compact('user'));
            return response()->json(['status' => 200, 'msg' => 'Email already verified.']);
        }

        if ($user->markEmailAsVerified()) {
            event(new Verified($user));
        }

        return  view('mail-verified', compact('user'));
        return response()->json(['status' => 200, 'msg' => 'Email verified successfully.']);
    }
}
