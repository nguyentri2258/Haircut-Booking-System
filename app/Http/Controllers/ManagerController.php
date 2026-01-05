<?php

namespace App\Http\Controllers;

use App\Models\{Booking, Address, Service, User};
use Illuminate\Http\Request;
use Carbon\Carbon;

class ManagerController extends Controller
{
    public function index(Request $request)
    {
        $auth = auth()->user();

        $query = Booking::with(['address', 'services', 'user'])
            ->latest();

        if ($auth->role === 'owner') {
            if ($request->filled('address_id')) {
                $query->where('address_id', $request->address_id);
            }
        }
        elseif ($auth->role === 'manager') {
            $query->where('address_id', $auth->address_id);
        }
        elseif ($auth->role === 'staff') {
            $query->where('user_id', $auth->id);
        }

        return view('managers.index', [
            'bookings' => $query->get(),
            'addresses' => $auth->role === 'owner'
                ? Address::all()
                : collect(),
        ]);
    }

    public function updateStatus(Request $request, Booking $booking)
    {
        $validated = $request->validate([
            'status' => 'required|in:confirmed,cancelled',
        ]);

        $booking->status = $validated['status'];
        $booking->save();

        return redirect()->back()->with('success', 'Cập nhật trạng thái thành ' . ucfirst($validated['status']) . '.');
    }

    public function edit(Booking $booking)
    {
        $addresses = Address::all();
        $services = Service::all();
        $users = User::all();

        $booking->time_of_day = Carbon::parse($booking->date)->format('H:i');

        return view('managers.edit', compact('booking', 'addresses', 'services', 'users'));
    }

    public function update(Request $request, Booking $booking)
    {
        $data = $request->validate([
            "name" => "required|string|max:255",
            "phone" => "required|string|max:20",
            "address_id" => "required|exists:addresses,id",
            "service_id" => "nullable|array",
            "service_id.*" => "exists:services,id",
            "user_id" => "required|string",
            "date" => "required|date",
            "time" => "required|string",
            "notes" => "nullable|string"
        ]);

        if ($data['user_id'] === 'auto') {
            if (strpos($data['time'], '|') !== false) {
                [$time, $userId] = explode('|', $data['time']);
                $data['time'] = trim($time);
                $data['user_id'] = trim($userId);
            } else {
                return back()->withErrors(['time' => 'Không xác định được user từ giờ đã chọn.'])->withInput();
            }
        }

        $bookingDateTime = Carbon::parse("{$data['date']} {$data['time']}");

        $existing = Booking::where('user_id', $data['user_id'])
            ->where('date', $bookingDateTime)
            ->where('id', '!=', $booking->id)
            ->exists();

        if ($existing) {
            return back()->withErrors(['time' => 'Giờ này đã được đặt.'])->withInput();
        }

        $booking->update([
            'name' => $data['name'],
            'phone' => $data['phone'],
            'address_id' => $data['address_id'],
            'user_id' => $data['user_id'],
            'date' => $bookingDateTime,
            'notes' => $data['notes'] ?? '',
        ]);

        $booking->services()->sync($data['service_id'] ?? []);

        return redirect()->route('managers.index')->with('success', 'Chỉnh sửa thành công');
    }
}