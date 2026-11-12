<?php
namespace App\Http\Controllers\Auth;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
class PasswordResetLinkController extends Controller {
    public function create() { return view('auth.forgot-password'); }
    public function store(Request $request) {
        $request->validate(['email'=>['required','email']]);
        if (config('mail.default')==='log') return back()->withErrors(['email'=>'Email delivery is not configured. Contact your site owner.']);
        Password::sendResetLink($request->only('email'));
        return back()->with('status','If that account exists, a password reset link has been requested.');
    }
}
