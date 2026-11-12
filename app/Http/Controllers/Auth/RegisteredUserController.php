<?php
namespace App\Http\Controllers\Auth;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rules\Password;
class RegisteredUserController extends Controller
{
    public function create() { return view('auth.register'); }
    public function store(Request $request) {
        $data=$request->validate([
            'username'=>['required','string','max:255','alpha_dash','unique:users,username'],
            'full_name'=>['required','string','max:255'],
            'email'=>['required','string','lowercase','email','max:255','unique:users,email'],
            'password'=>['required','confirmed',Password::min(8)],
        ]);
        $user=User::create($data);
        event(new Registered($user));
        Auth::login($user);
        $request->session()->regenerate();
        return redirect()->route('dashboard');
    }
}
