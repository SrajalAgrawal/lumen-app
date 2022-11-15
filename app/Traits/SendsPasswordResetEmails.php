<?php

namespace App\Traits;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;

trait SendsPasswordResetEmails
{
    /**
     * Display the form to request a password reset link.
     *
     * @return \Illuminate\Http\Response
     */
    public function showLinkRequestForm()
    {
        return view('auth.passwords.email');
    }
    /**
     * Send a reset link to the given user.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\JsonResponse
     */
    public function sendResetLinkEmail(Request $request)
    {

        $this->validateEmail($request);
        // We will send the password reset link to this user. Once we have attempted
        // to send the link, we will examine the response then see the message we
        // need to show to the user. Finally, we'll send out a proper response.

        $response = $this->broker()->sendResetLink(
            $this->credentials($request)
        );
        return $response == Password::RESET_LINK_SENT
            ? $this->sendResetLinkResponse($request, $response)
            : $this->sendResetLinkFailedResponse($request, $response);
    }
 
    protected function validateEmail(Request $request)
    {
        $this->validate($request, ['email' => 'required|email',]);
    }
   
    protected function credentials(Request $request)
    {
        return $request->only('email');
    }

    protected function sendResetLinkResponse(Request $request, $response)
    {
        return response()->json(['status' => trans($response)]);
    }

    protected function sendResetLinkFailedResponse(Request $request, $response)
    {
        return response()->json(['status'=>trans($response)]);

        return back()
            ->withInput($request->only('email'))
            ->withErrors(['email' => trans($response)]);
    }
    
    public function broker()
    {
        return Password::broker();
    }
}