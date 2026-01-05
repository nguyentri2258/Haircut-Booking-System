<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Address;
use App\Models\Availability;
use App\Models\Holiday;
use Illuminate\Http\Request;
use Carbon\Carbon;

class AvailabilityController extends Controller
{
    public function index(Request $request)
    {
        $auth = auth()->user();

        $addresses = collect();
        $users = collect();
        $selectedUser = null;

        $workingEvents = [];

        $addressId = null;

        if ($auth->role === 'owner') {

            $addresses = Address::all();

            $addressId = $request->address_id
                ?? $addresses->first()?->id;

            $users = User::where('role', 'staff')
                ->where('address_id', $addressId)
                ->get();

            $selectedUser = $users
                ->firstWhere('id', $request->user_id)
                ?? $users->first();
        }
        elseif ($auth->role === 'manager') {

            $addressId = $auth->address_id;

            $users = User::where('role', 'staff')
                ->where('address_id', $addressId)
                ->get();

            $selectedUser = $users
                ->firstWhere('id', $request->user_id)
                ?? $users->first();
        }
        else {
            $selectedUser = $auth;
            $addressId = $auth->address_id;
        }

        if ($selectedUser) {

            foreach ($selectedUser->availabilities as $item) {

                $start = Carbon::createFromFormat(
                    'Y-m-d H:i',
                    $item->available_date . ' ' . $item->time_of_day,
                    'Asia/Ho_Chi_Minh'
                );

                $workingEvents[] = [
                    'start' => $start->format('Y-m-d\TH:i:s'),
                    'end'   => $start->copy()->addHours(3)->format('Y-m-d\TH:i:s'),
                    'allDay' => false,
                    'backgroundColor' => '#07d100ff',
                    'borderColor' => '#07d100ff',
                    'extendedProps' => [
                        'is_day_off' => false,
                    ],
                ];
            }

            $holidays = Holiday::where(function ($q) use ($addressId) {
                $q->whereNull('address_id');

                if ($addressId) {
                    $q->orWhere('address_id', $addressId);
                }
            })->get();

            foreach ($holidays as $holiday) {
                $workingEvents[] = [
                    'title' => $holiday->note,
                    'start' => $holiday->date,
                    'end'   => Carbon::parse($holiday->date)->addDay()->toDateString(),
                    'allDay' => true,
                    'display' => 'background',
                    'backgroundColor' => '#a1a1a1ff',
                    'extendedProps' => [
                        'is_day_off' => true,
                    ],
                ];
            }
        }

        return view('availabilities.index', compact(
            'addresses',
            'users',
            'selectedUser',
            'workingEvents'
        ));
    }

    public function store(Request $request, User $user)
    {
        $auth = auth()->user();

        if ($auth->role === 'staff') abort(403);
        if ($user->role !== 'staff') abort(403);

        if (
            $auth->role === 'manager' &&
            $auth->address_id !== $user->address_id
        ) {
            abort(403);
        }

        $data = json_decode($request->availabilities, true);
        if (!is_array($data)) {
            return back()->withErrors('Dữ liệu lịch không hợp lệ');
        }

        $user->availabilities()->delete();

        foreach ($data as $date => $times) {

            $parsedDate = Carbon::parse($date)->format('Y-m-d');

            $isHoliday = Holiday::where('date', $parsedDate)
                ->where(function ($q) use ($user) {
                    $q->whereNull('address_id')
                      ->orWhere('address_id', $user->address_id);
                })
                ->exists();

            if ($isHoliday) continue;

            foreach ($times as $time) {
                if (!$time) continue;

                Availability::create([
                    'user_id'         => $user->id,
                    'available_date' => $parsedDate,
                    'time_of_day'    => $time,
                ]);
            }
        }

        return back()->with('success', 'Đã lưu lịch làm việc');
    }
}