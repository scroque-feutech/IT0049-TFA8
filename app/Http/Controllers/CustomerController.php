<?php
namespace App\Http\Controllers;
use App\Models\Customer;
use Illuminate\Http\Request;
class CustomerController extends Controller
{
    public function index() { return view('customers.index',['customers'=>Customer::orderBy('id')->paginate(15)]); }
    public function create() { return view('customers.create'); }
    public function store(Request $request) {
        Customer::create($this->validated($request));
        return redirect()->route('customers.index')->with('success','Customer created.');
    }
    public function show(Customer $customer) { return view('customers.show',compact('customer')); }
    public function edit(Customer $customer) { return view('customers.edit',compact('customer')); }
    public function update(Request $request,Customer $customer) {
        $customer->update($this->validated($request));
        return redirect()->route('customers.index')->with('success','Customer updated.');
    }
    public function destroy(Customer $customer) {
        $customer->delete();
        return redirect()->route('customers.index')->with('success','Customer deleted.');
    }
    private function validated(Request $request): array {
        $data=$request->validate(['full_name'=>['required','string','max:255'],'email'=>['required','email','max:255'],'phone'=>['nullable','string','max:20']]);
        $data['phone']=$data['phone']??'';
        return $data;
    }
}
