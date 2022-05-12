<?php







namespace App\Http\Controllers\Auth;







use App\Http\Controllers\Controller;



use App\Http\Requests\OneSignalRequest;



use App\Mail\ResetLinkSend;



use App\OneSignalUser;



use App\PasswordReset;



use App\User;



use Carbon\Carbon;



use Illuminate\Http\Request;



use Illuminate\Support\Facades\DB;



use Illuminate\Support\Facades\Mail;



use Illuminate\Support\Str;







class LoginController extends Controller



{



    /*



    |--------------------------------------------------------------------------



    | Login Controller



    |--------------------------------------------------------------------------



    |



    | This controller handles authenticating users for the application and



    | redirecting them to your home screen. The controller uses a trait



    | to conveniently provide its functionality to your applications.



    |



    */







    /**



     * Handle a login request to the application.



     *



     * @param \Illuminate\Http\Request $request



     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\Response|\Illuminate\Http\JsonResponse



     */



    public function login(Request $request)



    {



        // Validate form data



        $validator = validator($request->all(), [



            'username' => 'required|email',



            'password' => 'required|string|min:8',



        ]);







        if ($validator->fails()) {



            return response()->json(['errors' => $validator->getMessageBag()->toarray()], 400);



        }







        try {



            $data = [



                'grant_type' => 'password',



                // 'client_id' => config('services.passport.client_id'),



                // 'client_secret' => config('services.passport.client_secret'),

                'client_id' => '96036019-a3f2-49e6-a20c-dcc03f4b1b58',



                'client_secret' => '2vWB9GSKjL0QCzMDegAKXePVi9lQ3uDks9Vkfn18',



                'username' => $request->username,



                'password' => $request->password,



                'scope' => '*',



            ];




            $request = app('request')->create('/oauth/token', 'POST', $data);



            $response = app('router')->prepareResponse($request, app()->handle($request));



            $response = json_decode($response->getContent(), true);











            if (isset($response['access_token'])) {



                return response()->json($response);



            } else {
                return $request;


                // return response()->json(['errors' => ['credentials' => ['Invalid Credentials. Your credentials are incorrect. Please try again with valid credentials.']]], 401);



            }



        } catch (\Exception $e) {



            if ($e->getCode() === 400) {



                return response()->json('Invalid Request. Please enter username & password.', $e->getCode());



            } elseif ($e->getCode() === 401) {



                return response()->json('Invalid Credentials. Your credentials are incorrect. Please try again with valid credentials.', $e->getCode());



            } else {



                return response()->json('Something went wrong on the server.', $e->getCode());



            }



        }



    }







    /**



     * Log the user out of the application.



     *



     * @return \Illuminate\Http\JsonResponse



     */



    public function logout()



    {



        auth()->user()->tokens->each(function ($token, $key) {



            $token->delete();



        });







        return response()->json('Logged out successfully.');



    }







    /**



     * @param Request $request



     * @return \Illuminate\Http\JsonResponse



     */



    public function sendResetLink(Request $request)



    {







        // $request->validate([



        //     'email' => ['required', 'Email'],







        // ]);







        // $user = User::where('email', $request->email)->first();







        // $password_reset = new PasswordReset();



        // $password_reset->email = $user->email;







        // $password_reset->token = Str::random(16);



        // $password_reset->save();







        // if ($password_reset) {



        //     Mail::to($request->email)->send(new ResetLinkSend($password_reset));



        //     return response()->json(['msg' => 'Reset link send successfully.']);



        // }



        // return response()->json(['msg' => 'Requested Email not Found']);







    }







    /**



     * @param Request $request



     * @return \Illuminate\Http\JsonResponse



     */



    public function veififyToken(Request $request)



    {







        PasswordReset::where('token', $request->token)->whereDate('created_at', '<=', Carbon::now()->addMinutes(10)->toDateString())->where('used', 0)->firstOrFail();



        return response()->json([



            'status' => 'success',



            'token' => 'token found',



        ], 200);



    }







    /**



     * @param Request $request



     * @return \Illuminate\Http\JsonResponse



     */



    public function passwordReset(Request $request)



    {







        $request->validate([



            'password' => ['required', 'min:8'],



            'confirm_password' => ['same:password'],



        ]);







        $user = PasswordReset::where('token', $request->token)->get();







        $user_email = $user[0]->email;







        User::where('email', $user_email)->update(['password' => bcrypt($request->password)]);







        PasswordReset::where('email', $user_email)->update(['used' => 1]);







        return response()->json(['msg' => 'Password Reset Successfully'], 200);











    }







    /**



     * @param Request $request



     * @return \Illuminate\Http\JsonResponse



     */



    public function oneSignalRegister(OneSignalRequest $request)



    {



        $user = OneSignalUser::firstOrCreate(



            [



                'user_id' => request('user_id'),



                'player_id' => request('player_id'),











            ],



            [



                'user_id' => request('user_id'),



                'player_id' => request('player_id'),



                'status' => 'active'











            ]



        );







        return response()->json(['msg' => 'User Register  Successfully'], 200);



    }







}



