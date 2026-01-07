<?php

namespace App\Http\Controllers;

use App\Models\{Booking, Address, Service, User};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class BookingController extends Controller
{
    public function index()
    {
        return view("bookings.booking", [
            "addresses" => Address::all(),
            "services" => Service::all(),           
        ]);
    }

    public function getStylistsByAddress(Request $request)
    {
        $request->validate([
            'address_id' => 'required|exists:addresses,id',
        ]);

        $users = User::where('address_id', $request->address_id)
            ->where('role', 'staff')
            ->get();

        return response()->json($users->map(fn($u) => [
            'id' => $u->id,
            'name' => $u->name,
            'avatar' => $u->avatar
                ? asset('uploads/avatar/' . $u->avatar) 
                : asset('images/default-avatar.png'),
        ]));
    }

    public function booking(Request $request)
    {
        if (! session('booking_verified')) {
            return response()->json([
                'message' => 'EMAIL_NOT_VERIFIED'
            ], 403);
        }
        
        $data = $request->validate([
            "name" => "bail|required|string|max:255",
            "phone" => "bail|required|string|min:10",
            "email" => "bail|required|email|max:255",
            "address_id" => "bail|required|exists:addresses,id",
            "service_id" => "bail|required|array|min:1|distinct",
            "service_id.*" => "exists:services,id",
            "date" => "bail|required|date",
            "user_id" => "bail|required|string",     
            "time" => "bail|required|string",
            "notes" => "nullable|string"
        ],[
            "name.required" => "Vui lòng nhập Tên",
            "phone.required" => "Vui lòng nhập Số điện thoại",
            "email.required" => "Vui lòng nhập Email",
            "address_id.required" => "Vui lòng chọn chi nhánh",
            "service_id.required" => "Vui lòng chọn dịch vụ",
            "date.required" => "Vui lòng chọn ngày",
            "user_id.required" => "Vui lòng chọn stylist",
            "time.required" => "Vui lòng chọn giờ",
        ]);

        if ($data['user_id'] === 'auto') {
            if (strpos($data['time'], '|') !== false) {
                [$time, $userId] = explode('|', $data['time']);
                $data['time'] = trim($time);
                $data['user_id'] = trim($userId);
            } else {
                return redirect()->back()->withErrors(['time' => 'Không xác định được stylist từ giờ đã chọn.'])->withInput();
            }
        }

        $bookingDateTime = Carbon::parse("{$data['date']} {$data['time']}");

        $existing = Booking::where('user_id', $data['user_id'])
            ->where('date', $bookingDateTime)
            ->exists();

        if ($existing) {
            return redirect()->back()->withErrors(['time' => 'Giờ này đã được đặt. Vui lòng chọn giờ khác.'])->withInput();
        }

        $booking = Booking::create([
            'name' => $data['name'],
            'phone' => $data['phone'],
            'email' => $data['email'],
            'address_id' => $data['address_id'],
            'user_id' => $data['user_id'],
            'date' => $bookingDateTime,
            'notes' => $data['notes'] ?? '',
            'status' => 'confirmed'
        ]);

        $booking->services()->attach($data['service_id']);

        session()->forget('booking_verified');

        $services = Service::whereIn('id', $data['service_id'] ?? [])->get();

        return response()->json([
            'success' => true,
            'booking_details' => [
                'name' => $data['name'],
                'phone' => $data['phone'],
                'email' => $data['email'],
                'address' => Address::find($data['address_id'])->name,
                'service' => $services->pluck('name')->join(', '),
                'price' => $services->sum('price'),
                'user' => User::find($data['user_id'])->name,
                'date' => $bookingDateTime->format('d/m/Y H:i'),
                'notes' => $data['notes']
            ]
        ]);
    }

    public function getAvailableTimes(Request $request)
    {
        $request->validate([
            'date' => 'required|date',
            'user_id' => 'nullable',
            'address_id' => 'required|exists:addresses,id',
            'booking_id' => 'nullable|exists:bookings,id',
        ]);

        $date = Carbon::parse($request->date)->toDateString();
        $bookingId = $request->booking_id;

        $currentBookingTime = null;

        if ($bookingId) {
            $booking = Booking::find($bookingId);
            if ($booking && $booking->date->toDateString() === $date) {
                $currentBookingTime = $booking->date->format('H:i');
            }
        }

        if ($request->user_id && $request->user_id !== 'auto') {
            $availableTimes = DB::table('user_availabilities')
                ->where('user_id', $request->user_id)
                ->where('available_date', $date)
                ->orderBy('time_of_day')
                ->pluck('time_of_day');

            $bookedTimes = Booking::where('user_id', $request->user_id)
                ->whereDate('date', $date)
                ->when($bookingId, fn($q) => $q->where('id', '!=', $bookingId))
                ->pluck('date')
                ->map(fn($dt) => Carbon::parse($dt)->format('H:i'));

            $filtered = $availableTimes->filter(function ($time) use ($bookedTimes, $currentBookingTime) {
                return !$bookedTimes->contains($time) || $time === $currentBookingTime;
            })->values();

            return response()->json($filtered);
        } else {
            $result = [];
            $users = User::where('address_id', $request->address_id)->get();

            foreach ($users as $user) {
                $availableTimes = DB::table('user_availabilities')
                    ->where('user_id', $user->id)
                    ->where('available_date', $date)
                    ->orderBy('time_of_day')
                    ->pluck('time_of_day');

                $booked = Booking::where('user_id', $user->id)
                    ->whereDate('date', $date)
                    ->when($bookingId, fn($q) => $q->where('id', '!=', $bookingId))
                    ->pluck('date')
                    ->map(fn($dt) => Carbon::parse($dt)->format('H:i'));

                foreach ($availableTimes as $time) {
                    if (!$booked->contains($time) || $time === $currentBookingTime) {
                        $result[] = [
                            'time' => $time,
                            'user_id' => $user->id,
                            'user_name' => $user->name,
                        ];
                    }
                }
            }
            return response()->json($result);
        }
    }
}