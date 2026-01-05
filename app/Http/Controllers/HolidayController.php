<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Holiday;
use App\Models\Address;
use Carbon\Carbon;

class HolidayController extends Controller
{   
    public function index(Request $request)
    {
        $holidays = Holiday::with('address')->orderBy('date')->get();

        return view('holidays.index', [
            'holidays' => $holidays
        ]);
    }

    public function create()
    {
        $addresses = Address::all();

        return view('holidays.create', [
            'addresses' => $addresses
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'date'       => 'required|date',
            'note'       => 'required|string|max:255',
            'address_id' => 'nullable|exists:addresses,id',
        ]);

        $exists = Holiday::where('date', $data['date'])
            ->where(function ($q) use ($data) {
                if (!empty($data['address_id'])) {
                    $q->where('address_id', $data['address_id']);
                } else {
                    $q->whereNull('address_id');
                }
            })
            ->exists();

        if ($exists) {
            return back()
                ->withErrors(['date' => 'Ngày nghỉ này đã tồn tại'])
                ->withInput();
        }

        Holiday::create([
            'date'       => $data['date'],
            'note'       => $data['note'],
            'address_id' => $data['address_id'] ?? null,
        ]);

        return redirect(route('holidays.index'))
            ->with('success', 'Tạo ngày nghỉ thành công');
    }
    
    public function edit(Holiday $holiday)
    {
        $addresses = Address::all();

        return view('holidays.edit', [
            'holiday'   => $holiday,
            'addresses' => $addresses
        ]);
    }

    public function update(Holiday $holiday, Request $request)
    {
        $data = $request->validate([
            'date'       => 'required|date',
            'note'       => 'required|string|max:255',
            'address_id' => 'nullable|exists:addresses,id',
        ]);

        $exists = Holiday::where('id', '!=', $holiday->id)
            ->where('date', $data['date'])
            ->where(function ($q) use ($data) {
                if (!empty($data['address_id'])) {
                    $q->where('address_id', $data['address_id']);
                } else {
                    $q->whereNull('address_id');
                }
            })
            ->exists();

        if ($exists) {
            return back()
                ->withErrors(['date' => 'Ngày nghỉ này đã tồn tại'])
                ->withInput();
        }

        $holiday->update([
            'date'       => $data['date'],
            'note'       => $data['note'],
            'address_id' => $data['address_id'] ?? null,
        ]);

        return redirect(route('holidays.index'))
            ->with('success', 'Cập nhật ngày nghỉ thành công');
    }

    public function destroy(Holiday $holiday)
    {
        $holiday->delete();

        return redirect(route('holidays.index'))
            ->with('success', 'Xóa thành công');
    }

    public function getAll(Request $request)
    {
        $addressId = $request->address_id;

        $holidays = Holiday::where(function ($q) use ($addressId) {
                $q->whereNull('address_id');

                if ($addressId) {
                    $q->orWhere('address_id', $addressId);
                }
            })
            ->pluck('date')
            ->map(fn ($date) => Carbon::parse($date)->toDateString());

        return response()->json([
            'holidays' => $holidays
        ]);
    }
}