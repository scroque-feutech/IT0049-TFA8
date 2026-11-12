<?php
namespace Database\Seeders;
use App\Models\User;
use App\Models\Customer;
use Illuminate\Database\Seeder;
class DatabaseSeeder extends Seeder {
    public function run(): void {
        $characters=['michael'=>'Michael De Santa','franklin'=>'Franklin Clinton','trevor'=>'Trevor Philips','lamar'=>'Lamar Davis','lester'=>'Lester Crest'];
        $i=0;
        foreach ($characters as $username=>$name) {
            User::firstOrCreate(['username'=>$username],['full_name'=>$name]);
            Customer::firstOrCreate(['email'=>$username.'@example.test'],['full_name'=>$name,'phone'=>'0917000000'.(++$i)]);
        }
    }
}
