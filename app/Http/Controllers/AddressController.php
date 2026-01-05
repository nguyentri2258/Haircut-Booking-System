<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Address;

class AddressController extends Controller
{
    public function index(){
        $addresses = Address::all();
        return view("addresses.index", ['addresses' => $addresses]);
    }

    public function create(){
        return view("addresses.create");
    }

    public function store(Request $request){
        $data = $request->validate([
            "name"=> "required",
            "address"=> "required",
            "note"=> "nullable"
        ]);

        $address = Address::create($data);

        return redirect(route('addresses.index'));
    }

    public function edit(Address $address){
        return view('addresses.edit', ['address'=> $address]);
    }

    public function update(Address $address, Request $request){
        $data = $request->validate([
            "name"=> "required",
            "address"=> "required",
            "note"=> "nullable"
        ]);      

        $address -> update($data);
        
        return redirect(route("addresses.index"))->with("success","Cập nhật địa chỉ thành công");
    }

    public function destroy(Address $address){
        $address->delete();
        return redirect(route("addresses.index"))->with("success","Xóa thành công");
    }   
}