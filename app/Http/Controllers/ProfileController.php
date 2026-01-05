<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class ProfileController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        return view('profiles.profile', compact('user'));
    }

    public function update(Request $request)
    {
        $user = auth()->user();

        $rules = [
            'name'   => 'required|min:3',
            'email'  => 'required|email|unique:users,email,'.$user->id,
            'avatar' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ];

        if ($request->filled('password')) {
            $rules['password'] = 'min:6|confirmed';
        }

        $data = $request->validate($rules);

        if ($request->hasFile('avatar')) {
            $fileName = time().'.'.$request->avatar->extension();
            $request->avatar->move(public_path('uploads/avatar'), $fileName);
            $data['avatar'] = $fileName;
        }

        if (!empty($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        }

        $user->update($data);

        return back()->with('success', 'Cập nhật thành công!');
    }

}
