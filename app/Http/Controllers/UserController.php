<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;

class UserController extends Controller
{

  public function emailRequestVerification(Request $request)
  {
    if ( $request->user()->hasVerifiedEmail() ) {
        return response()->json('Email address is already verified.');
    }
    
    $request->user()->sendEmailVerificationNotification();
    
    return response()->json('Email request verification sent to '. Auth::user()->email);
  }


  public function emailVerify(Request $request)
  {
    $this->validate($request, [
      'token' => 'required|string',
    ]);
        \Tymon\JWTAuth\Facades\JWTAuth::getToken();
        \Tymon\JWTAuth\Facades\JWTAuth::parseToken()->authenticate();
        if ( ! $request->user() ) {
                return response()->json('Invalid token', 401);
            }
            
            if ( $request->user()->hasVerifiedEmail() ) {
                return response()->json('Email address '.$request->user()->getEmailForVerification().' is already verified.');
            }
        $request->user()->markEmailAsVerified();
        return response()->json('Email address '. $request->user()->email.' successfully verified.');
    }

    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['login','captcha','register','user']]);
    }


    public function login(Request $request)
    {
        // dd("DB::getPDO()");
        $this->validate($request, [
            'email' => 'required|string',
            'password' => 'required|string',
        ]);

        $credentials = $request->only(['email', 'password']);

        if (! $token = Auth::attempt($credentials)) {
            return response()->json(['message' => 'UUnauthorized'], 401);
        }

        return $this->respondWithToken($token);
    }

    public function me()
    {
        return response()->json(auth()->user());
    }

    public function logout()
    {
        auth()->logout();

        return response()->json(['message' => 'Successfully logged out']);
    }



    protected function respondWithToken($token)
    {
        return response()->json([
            'access_token' => $token,
            'user' => auth()->user(),
            'expires_in' => auth()->factory()->getTTL() * 60 * 24
        ]);
    }

    public function showAllUsers(Request $request)
    {
        $filter = $request->search;

        $response = DB::table('users')
            ->where('name', 'LIKE', "%{$filter}%")
            ->orWhere('email', 'LIKE', "%{$filter}%")
            ->orWhere('id', 'LIKE', "%{$filter}%")
            ->paginate(10);

        return response($response);
    }
    public function showOneUser($id)
    {
        return response()->json(User::findorfail($id));
    }

    public function register(Request $request)
    {
        $this->validate($request, [
            'name' => 'required',
            'email' => 'required|email|unique:users',
            'password' => 'required|string|min:5'
        ]);
        $table = DB::table('users');
        $user = new User;
        $user->name = $request->name;
        $user->email = $request->email;
        $user->password = Hash::make($request->input('password'));
        $user->role = 0;
        $user->createdBy = 'user';
        $user->deletedBy = 'null';
        $user->save();

        $credentials = $request->only(['email', 'password']);

        if (! $token = Auth::attempt($credentials)) {
            return response()->json(['message' => 'UUnauthorized'], 401);
        }

        return $this->respondWithToken($token);
    }
    public function captcha(Request $request){
        $token = $request->token;
        $key = env('CAPTCHA_SECRET_KEY');
        $url = "https://www.google.com/recaptcha/api/siteverify?secret={$key}&response={$token}";
        $response = Http::post($url);

        return response($response);

    }
}