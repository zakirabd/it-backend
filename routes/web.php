<?php

use App\EssayAnswer;
use App\Events\CalendarEvent;
use App\Events\OnlineUserEvent;
use App\LiveSession;
use App\User;
use Carbon\Carbon;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Route;
use App\Mail\SendCertificate;
use Illuminate\Support\Facades\Storage;

//use PDF;
use Dompdf\Dompdf;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/


Route::get('/test', function () {
    $event = 'new';
    event(new CalendarEvent($event));
    return 'event send done';
});

Route::get('/online', function () {
    $event = 'new';
    event(new OnlineUserEvent($event));
    return 'event send done';
});



Route::get('/mail', function () {
    $data['name'] = 'Umme H Nisa';
    $data["email"] = "peyas.dev@gmail.com";
    $data["course"] = "Communication English";
    $data["marks"] = "99";
    $data["exam_date"] = "JANUARY 20th, 2021";
    $data["company_name"] = "CELT KHATAI";

    $qr_file_name = Str::random(10) . '.png';

    $pdf_name = Str::random(7) . '.pdf';

    $link = url('/storage/pdf/' . $pdf_name);
    $qr_code = \QrCode::format('png')
        ->size(200)->errorCorrection('H')
        ->generate($link);

    Storage::put('public/qr/' . $qr_file_name, $qr_code);

    $data['qr_path'] = url('/storage/qr/' . $qr_file_name);
//   $data['qr_path'] = public_path('/storage/qr/' . $qr_file_name);

    $pdf = PDF::loadView('emails.Certificate2', $data, ['format' => 'A4-L']);

    Storage::put('public/pdf/' . $pdf_name, $pdf->output());
//    return 'success';
    // work in local
//     $path = public_path('/storage/pdf/'. $pdf_name);

    //work in live
    $path = url('/storage/pdf/' . $pdf_name);

    Mail::send([], [], function ($message) use ($data, $pdf, $path) {
        $message->to($data["email"], $data["email"])
            ->subject('CELT Certificate')
            ->attach($path);
    });
    return 'success';
});
Route::get('email/verify/{id}/{hash}', 'Auth\VerificationController@verify')->name('verification.verify');
Route::get('/', function () {
    return view('welcome');
});
Route::get('mailable', function () {
    $reset_password = \App\PasswordReset::find(1);
    return new \App\Mail\ResetLinkSend($reset_password);
});



