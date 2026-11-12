<?php
namespace Tests\Feature;
use App\Models\User;
use App\Models\Customer;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;
class PosTest extends TestCase
{
    use RefreshDatabase;
    private function member(): User {
        return User::create(['username'=>'tester','full_name'=>'Test User','email'=>'tester@example.test','password'=>'MyPassword123!']);
    }
    public function test_public_pages_render(): void {
        foreach (['/','/about','/login','/register','/forgot-password','/reset-password/token?email=one@example.test'] as $path) $this->get($path)->assertOk();
    }
    public function test_all_resource_actions_require_authentication(): void {
        foreach (['customers','users'] as $resource) {
            foreach (['/'.$resource,'/'.$resource.'/create','/'.$resource.'/1','/'.$resource.'/1/edit'] as $path) $this->get($path)->assertRedirect('/login');
            $this->post('/'.$resource,[])->assertRedirect('/login');
            $this->put('/'.$resource.'/1',[])->assertRedirect('/login');
            $this->patch('/'.$resource.'/1',[])->assertRedirect('/login');
            $this->delete('/'.$resource.'/1')->assertRedirect('/login');
        }
        $this->get('/storage/avatars/user-1-0123456789abcdef.jpg')->assertRedirect('/login');
    }
    public function test_registration_logs_in_and_hashes_password(): void {
        $this->post('/register',['username'=>'sean','full_name'=>'Sean Roque','email'=>'sean@example.test','password'=>'Password123!','password_confirmation'=>'Password123!'])->assertRedirect('/dashboard');
        $user=User::where('email','sean@example.test')->firstOrFail();
        $this->assertAuthenticatedAs($user);
        $this->assertTrue(Hash::check('Password123!',$user->password));
        $this->get('/users')->assertOk(); $this->get('/customers')->assertOk();
    }
    public function test_login_returns_to_intended_page_and_logout_blocks_access(): void {
        $user=$this->member();
        $this->get('/customers')->assertRedirect('/login');
        $this->post('/login',['email'=>$user->email,'password'=>'MyPassword123!'])->assertRedirect('/customers');
        $this->get('/customers')->assertOk();
        $this->post('/logout')->assertRedirect('/');
        $this->assertGuest();
        $this->get('/customers')->assertRedirect('/login');
    }
    public function test_invalid_login_and_throttling(): void {
        $this->member();
        for ($i=0;$i<5;$i++) $this->post('/login',['email'=>'tester@example.test','password'=>'wrong'])->assertSessionHasErrors('email');
        $this->post('/login',['email'=>'tester@example.test','password'=>'MyPassword123!'])->assertSessionHasErrors('email');
        $this->assertGuest();
    }
    public function test_customer_crud_and_validation(): void {
        $this->actingAs($this->member());
        $this->get('/customers/create')->assertOk();
        $this->post('/customers',['full_name'=>''])->assertSessionHasErrors(['full_name','email']);
        $this->post('/customers',['full_name'=>'New Customer','email'=>'customer@example.test','phone'=>''])->assertRedirect('/customers');
        $customer=Customer::firstOrFail();
        $this->get('/customers/'.$customer->id)->assertOk()->assertSee('New Customer');
        $this->get('/customers/'.$customer->id.'/edit')->assertOk();
        $this->put('/customers/'.$customer->id,['full_name'=>'Updated Customer','email'=>'customer@example.test','phone'=>'123'])->assertRedirect('/customers');
        $this->assertDatabaseHas('customers',['id'=>$customer->id,'full_name'=>'Updated Customer']);
        $this->delete('/customers/'.$customer->id)->assertRedirect('/customers');
        $this->assertDatabaseMissing('customers',['id'=>$customer->id]);
        $this->get('/customers/9999')->assertNotFound();
    }
    public function test_user_crud_unique_fields_and_optional_password(): void {
        $self=$this->member();$this->actingAs($self);
        $this->get('/users/create')->assertOk();
        $data=['username'=>'newuser','full_name'=>'New User','email'=>'new@example.test','password'=>'Password123!','password_confirmation'=>'Password123!'];
        $this->post('/users',$data)->assertRedirect('/users');
        $new=User::where('username','newuser')->firstOrFail();$hash=$new->password;
        $this->post('/users',$data)->assertSessionHasErrors(['username','email']);
        $this->get('/users/'.$new->id)->assertOk();
        $this->get('/users/'.$new->id.'/edit')->assertOk();
        $data['full_name']='Updated User';$data['password']='';$data['password_confirmation']='';
        $this->put('/users/'.$new->id,$data)->assertRedirect('/users');
        $this->assertSame($hash,$new->fresh()->password);
        $this->delete('/users/'.$self->id)->assertSessionHasErrors('user');
        $this->assertDatabaseHas('users',['id'=>$self->id]);
        $this->delete('/users/'.$new->id)->assertRedirect('/users');
        $this->assertDatabaseMissing('users',['id'=>$new->id]);
    }
    public function test_avatar_upload_resize_replacement_rejection_and_delete(): void {
        Storage::fake('public');
        $self=$this->member();$this->actingAs($self);
        $user=User::create(['username'=>'avataruser','full_name'=>'Avatar User','email'=>'avatar@example.test','password'=>'Password123!']);
        $data=['username'=>$user->username,'full_name'=>$user->full_name,'email'=>$user->email];
        $this->put('/users/'.$user->id,$data+['avatar'=>UploadedFile::fake()->image('avatar.png',800,600)])->assertRedirect('/users');
        $old=$user->fresh()->avatar;
        Storage::disk('public')->assertExists($old);
        $info=getimagesize(Storage::disk('public')->path($old));
        $this->assertSame(400,$info[0]);$this->assertSame(400,$info[1]);$this->assertSame('image/jpeg',$info['mime']);
        $this->get('/storage/'.$old)->assertOk();
        $this->put('/users/'.$user->id,$data+['avatar'=>UploadedFile::fake()->image('new.png',500,500)])->assertRedirect('/users');
        Storage::disk('public')->assertMissing($old);
        $new=$user->fresh()->avatar;
        $this->put('/users/'.$user->id,$data+['avatar'=>UploadedFile::fake()->create('attack.php',5,'text/plain')])->assertSessionHasErrors('avatar');
        $this->put('/users/'.$user->id,$data+['avatar'=>UploadedFile::fake()->image('large.png')->size(2049)])->assertSessionHasErrors('avatar');
        $this->assertSame($new,$user->fresh()->avatar);
        $this->get('/storage/avatars/not-allowed.php')->assertNotFound();
        $this->delete('/users/'.$user->id)->assertRedirect('/users');
        Storage::disk('public')->assertMissing($new);
    }
    public function test_registration_rejects_invalid_data_and_mismatched_password(): void {
        $this->post('/register',['username'=>'one','full_name'=>'One','email'=>'invalid','password'=>'short','password_confirmation'=>'different'])->assertSessionHasErrors(['email','password']);
        $this->assertGuest();
    }
    public function test_old_seed_records_have_no_shared_default_login(): void {
        $this->seed();
        $this->assertDatabaseCount('users',5);$this->assertDatabaseCount('customers',5);
        $this->assertNull(User::where('username','michael')->firstOrFail()->password);
    }
}
