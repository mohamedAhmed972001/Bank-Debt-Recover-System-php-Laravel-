<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Notifications\EmailVerificationNotification;
use App\Notifications\ForgotPassword;
use Exception;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Http\Request;
use Illuminate\Mail\Message;
use Illuminate\Notifications\Notifiable;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Validator;




class AuthController extends Controller
{
    use Notifiable, CanResetPassword;
    /**
     * Create a new AuthController instance.
     *
     * @return void
     */
    public function __construct() {
        $this->middleware('auth:api', ['except' => ['login', 'register']]);
    }

    /**
     * Get a JWT via given credentials.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(Request $request){
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|string|min:6',
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }
        if (! $token = auth()->attempt($validator->validated())) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }
        $user = DB::table('users')->where('email', $request->email)->first();
        if($user->Status==='Active'){

            return $this->createNewToken($token);
        }else
        {
             return response()->json([
            'message' => true,
            'user' => 'usre is incative'
        ], 201);

        }
       // credentials($request);

    }
    /**
     * Register a User.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function register(Request $request) {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|between:2,100',
            'email' => 'required|string|email|max:100|unique:users',
            'password' => 'required|string|confirmed|min:6',
            'roles_name'=>'required|string',
            'Status'=>'required|string|max:10'

        ]);
        if($validator->fails()){
            return response()->json($validator->errors()->toJson(), 400);
        }
        $user = User::create(array_merge(
            $validator->validated(),
            ['password' => bcrypt($request->password),

                'roles_name' => [$request->roles_name],

            ]
        ));
        $role = DB::table('roles')->where('name', $request->roles_name)->first();
        $user->assignRole([$role->id]);
        return response()->json([
            'message' => 'User successfully registered',
            'user' => $user
        ], 201);
    }

    /**
     * Log the user out (Invalidate the token).
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout() {
        auth()->logout();
        return response()->json(['message' => 'User successfully signed out']);
    }

    /**
     * Refresh a token.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function refresh() {
        return $this->createNewToken(auth()->refresh());
    }

    /**
     * Get the authenticated User.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function userProfile() {
        return response()->json(auth()->user());
    }

    /**
     * Get the token array structure.
     *
     * @param  string $token
     *
     * @return \Illuminate\Http\JsonResponse
     */
    protected function createNewToken($token){
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth()->factory()->getTTL() * 60,
            'user' => auth()->user()
        ]);
    }
    protected function credentials(Request $request)
    {
        return ['email' => $request->email, 'password' => $request->password, 'status' => 'Active'];
    }

    public function reset_password(Request $request)
    {
        $input = $request->all();
        $userid = Auth::guard('api')->user()->id;
        $rules = array(
            'old_password' => 'required',
            'new_password' => 'required|min:6',
            'confirm_password' => 'required|same:new_password',
        );
        $validator = Validator::make($input, $rules);
        if ($validator->fails()) {
            $arr = array("status" => 400, "message" => $validator->errors()->first(), "data" => array());
        } else {
            try {
                if ((Hash::check(request('old_password'), Auth::user()->password)) == false) {
                    $arr = array("status" => 400, "message" => "Check your old password.", "data" => array());
                } else if ((Hash::check(request('new_password'), Auth::user()->password)) == true) {
                    $arr = array("status" => 400, "message" => "Please enter a password which is not similar then current password.", "data" => array());
                } else {
                    User::where('id', $userid)->update(['password' => Hash::make($input['new_password'])]);
                    $arr = array("status" => 200, "message" => "Password updated successfully.", "data" => array());
                }
            } catch (\Exception $ex) {
                if (isset($ex->errorInfo[2])) {
                    $msg = $ex->errorInfo[2];
                } else {
                    $msg = $ex->getMessage();
                }
                $arr = array("status" => 400, "message" => $msg, "data" => array());
            }
        }
        return response()->json([

            'user' => $arr
        ], 201);
    }

    public function forgot_password(Request $request)
    {
        $input = $request->only('email');
        $validator = Validator::make($input, [
            'email' => 'required|email',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 400,
                'message' => $validator->errors()->first(),
                'data' => [],
            ]);
        }

        try {
            // This will send a password reset link to the provided email
            $response = Password::sendResetLink($input);

            switch ($response) {
                case Password::RESET_LINK_SENT:
                    return response()->json([
                        'status' => 200,
                        'message' => trans($response),
                        'data' => [],
                    ]);

                case Password::INVALID_USER:
                    return response()->json([
                        'status' => 400,
                        'message' => trans($response),
                        'data' => [],
                    ]);
            }
        } catch (\Swift_TransportException $ex) {
            return response()->json([
                'status' => 400,
                'message' => $ex->getMessage(),
                'data' => [],
            ]);
        } catch (\Exception $ex) {
            return response()->json([
                'status' => 400,
                'message' => $ex->getMessage(),
                'data' => [],
            ]);
        }
    }


//    public function forgot_password(Request $request)
//    {
//        $input = $request->all();
//        $rules = array(
//            'email' => "required|email",
//        );
//        $validator = Validator::make($input, $rules);
//        $user = User::where('email', $input['email'])->get();
//        if ($validator->fails()) {
//            $arr = array("status" => 400, "message" => $validator->errors()->first(), "data" => array());
//        } else {
//
//            try {
//
//                $response = Password::sendResetLink($request->only('email'), function (Message $message) {
//
//                    Notification::send($user, new ForgotPassword());
//                });
//                switch ($response) {
//                    case Password::RESET_LINK_SENT:
//                        return \Response::json(array("status" => 200, "message" => trans($response), "data" => array()));
//                    case Password::INVALID_USER:
//                        return \Response::json(array("status" => 400, "message" => trans($response), "data" => array()));
//                }
//            } catch (\Swift_TransportException $ex) {
//                $arr = array("status" => 400, "message" => $ex->getMessage(), "data" => []);
//            } catch (Exception $ex) {
//                $arr = array("status" => 400, "message" => $ex->getMessage(), "data" => []);
//            }
//        }
//        return response()->json(["message" => trans($response)], 400);
//
//    }







}
