<?php
namespace App\Http\Controllers;

use App\Traits\ResetsPasswords;
use App\Notification\ResetPassword;

class ResetPasswordController extends Controller
{
    use ResetsPasswords;
}