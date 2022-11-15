<?php
namespace App\Http\Controllers;

use App\Traits\SendsPasswordResetEmails;
use App\Notification\ResetPassword;


class RequestPasswordController extends Controller
{
    
    use SendsPasswordResetEmails;
    public function __construct()
    {

        $this->broker = 'users';
    }
}