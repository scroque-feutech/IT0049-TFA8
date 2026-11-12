<?php
namespace App\Http\Controllers\Auth;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Validation\Rules\Password;
class PasswordController extends Controller {
    public function update(Request $request) {
        $data=$request->validate(['current_password'=>['required','current_password'],'password'=>['required',Password::min(8),'confirmed']]);
        $request->user()->update(['password'=>$data['password']]);
        return back()->with('success','Password updated.');
    }
}
