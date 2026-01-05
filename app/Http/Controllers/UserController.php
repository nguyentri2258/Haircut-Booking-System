<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Address;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function login()
    {
        return view('users.login');
    }

    public function loginPost(Request $request)
    {
        $request->validate([
            'email'    => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::attempt($request->only('email', 'password'))) {

            $request->session()->regenerate();

            if (Auth::user()->password === null) {
                Auth::logout();

                return back()->withErrors([
                    'email' => 'Tài khoản chưa được kích hoạt. Vui lòng đặt mật khẩu trước.'
                ]);
            }

            return redirect()->intended('/dashboard');
        }

        return back()->withErrors([
            'email' => 'Email hoặc mật khẩu không đúng',
        ]);
    }

    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')
            ->with('success', 'Đăng xuất thành công!');
    }

    public function activate()
    {
        return view('users.activate');
    }

    public function activatePost(Request $request)
    {
        $request->validate([
            'email'    => 'required|email|exists:users,email',
            'name'     => 'required|string|min:3',
            'password' => 'required|min:6|confirmed',
        ]);

        $user = User::where('email', $request->email)->first();

        if ($user->password !== null) {
            return back()->withErrors([
                'email' => 'Tài khoản đã được kích hoạt. Vui lòng đăng nhập.'
            ]);
        }

        $user->update([
            'name'     => $request->name,
            'password' => Hash::make($request->password),
        ]);

        return redirect()->route('login')
            ->with('success', 'Kích hoạt tài khoản thành công. Hãy đăng nhập.');
    }

    public function index(Request $request)
    {
        $auth = auth()->user();

        $query = User::query()->where('role', '!=', 'owner');

        if ($auth->isOwner()) {
            if ($request->address_id) {
                $query->where('address_id', $request->address_id);
            }
        }

        if ($auth->isManager() || $auth->isStaff()) {
            $query->where('address_id', $auth->address_id);
        }

        $users = $query
            ->orderByRaw("FIELD(role, 'manager', 'staff')")
            ->latest()
            ->get();

        $addresses = Address::all();

        return view('users.index', compact('users', 'addresses'));
    }

    public function create()
    {
        $auth = auth()->user();

        $this->authorize('create-user', $auth->isManager() ? 'staff' : 'manager');

        $addresses = $auth->isOwner()
            ? Address::all()
            : Address::where('id', $auth->address_id)->get();

        return view('users.create', compact('addresses'));
    }

    public function store(Request $request)
    {
        $auth = auth()->user();

        $data = $request->validate([
            'email' => 'required|email|unique:users,email',
            'role' => 'required|in:manager,staff',
            'address_id' => 'required|exists:addresses,id',
        ]);

        $this->authorize('create-user', $data['role']);

        if ($auth->isManager()) {
            $data['role'] = 'staff';
            $data['address_id'] = $auth->address_id;
        }

        User::create([
            ...$data,
            'password' => null,
        ]);

        return redirect()->route('users.index')
            ->with('success', 'Tạo tài khoản thành công');
    }

    public function edit(User $user)
    {
        $this->authorize('edit-user', $user);

        $auth = auth()->user();

        $addresses = $auth->isOwner()
            ? Address::all()
            : Address::where('id', $auth->address_id)->get();

        return view('users.edit', compact('user', 'addresses'));
    }

    public function update(Request $request, User $user)
    {
        $this->authorize('edit-user', $user);

        $data = $request->validate([
            'email'      => 'required|email|unique:users,email,' . $user->id,
            'name'       => 'nullable|string|min:3',
            'role'       => 'required|in:manager,staff',
            'address_id' => 'required|exists:addresses,id',
        ]);

        if (auth()->user()->isManager()) {
            unset($data['role'], $data['address_id']);
        }

        $user->update($data);

        return redirect()->route('users.index')
            ->with('success', 'Cập nhật thành công');
    }

    public function destroy(User $user)
    {
        $this->authorize('delete-user', $user);

        $user->delete();

        return back()->with('success', 'Đã xoá tài khoản');
    }   
}