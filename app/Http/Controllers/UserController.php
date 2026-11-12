<?php
namespace App\Http\Controllers;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Intervention\Image\ImageManager;
class UserController extends Controller
{
    public function index() { return view('users.index',['users'=>User::orderBy('id')->paginate(15)]); }
    public function create() { return view('users.create'); }
    public function store(Request $request) {
        User::create($this->validated($request));
        return redirect()->route('users.index')->with('success','User created.');
    }
    public function show(User $user) { return view('users.show',compact('user')); }
    public function edit(User $user) { return view('users.edit',compact('user')); }
    public function update(Request $request,User $user) {
        $data=$this->validated($request,$user);
        unset($data['avatar']);
        if ($request->hasFile('avatar')) {
            $image=ImageManager::gd()->read($request->file('avatar')->getRealPath())->cover(400,400);
            $path='avatars/user-'.$user->id.'-'.bin2hex(random_bytes(8)).'.jpg';
            Storage::disk('public')->put($path,(string)$image->toJpeg(85));
            $old=$user->avatar;
            $data['avatar']=$path;
            try { $user->update($data); } catch (\Throwable $e) { Storage::disk('public')->delete($path); throw $e; }
            if ($old) Storage::disk('public')->delete($old);
        } else { $user->update($data); }
        return redirect()->route('users.index')->with('success','User updated.');
    }
    public function destroy(Request $request,User $user) {
        if ($request->user()->is($user)) return back()->withErrors(['user'=>'You cannot delete the account you are logged into.']);
        $avatar=$user->avatar;
        $user->delete();
        if ($avatar) Storage::disk('public')->delete($avatar);
        return redirect()->route('users.index')->with('success','User deleted.');
    }
    private function validated(Request $request,?User $user=null): array {
        $data=$request->validate([
            'username'=>['required','string','max:255','alpha_dash',Rule::unique('users','username')->ignore($user)],
            'full_name'=>['required','string','max:255'],
            'email'=>['required','email','lowercase','max:255',Rule::unique('users','email')->ignore($user)],
            'password'=>[$user?'nullable':'required','confirmed',Password::min(8)],
            'avatar'=>['nullable','image','mimes:jpg,jpeg,png,webp','max:2048','dimensions:max_width=6000,max_height=6000'],
        ]);
        if (empty($data['password'])) unset($data['password']);
        unset($data['avatar']);
        return $data;
    }
}
