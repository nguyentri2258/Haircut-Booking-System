<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Service;

class ServiceController extends Controller
{
    public function index(){
        $services = Service::all();
        return view("services.index", ['services' => $services]);
    }

    public function create(){
        return view("services.create");
    }

    public function store(Request $request){
        $data = $request->validate([
            "name"=> "required",
            "price"=> "required|numeric",
            "description" => "nullable|string"
        ]);

        $service = Service::create($data);

        return redirect(route('services.index'));
    }

    public function edit(Service $service){
        return view('services.edit', ['service'=> $service]);
    }

    public function update(Service $service, Request $request){
        $data = $request->validate([
            "name"=> "required",
            "price"=> "required|decimal:0,3",
            "description"=> "nullable|string"
        ]);
        
        $service -> update($data);

        return redirect(route("services.index"))->with("success","Cập nhật dịch vụ thành công");
    }

    public function destroy(Service $service){
        $service->delete();
        return redirect(route("services.index"))->with("success","Xóa thành công");
    }
}
