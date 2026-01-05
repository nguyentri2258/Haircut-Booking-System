@extends('layouts.dashboard')

@section('content')
@php
    $auth = auth()->user();
    $canEdit = $auth->role !== 'staff';
@endphp

<div class="container">
    <div class="row">
        <div class="mb-3">
            <h4 class="mb-0">
                Lịch làm
                @if($selectedUser instanceof \App\Models\User)
                    - {{ $selectedUser->name }}
                @endif
            </h4>
        </div>

        @if(session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif

        <div class="d-flex gap-2 mb-3 flex-wrap">
            @if($auth->role === 'owner')
                <select class="form-select"
                        style="max-width:220px"
                        onchange="location.href = this.value">
                    @foreach($addresses as $address)
                        <option
                            value="{{ route('availabilities.index', ['address_id' => $address->id]) }}"
                            {{ request('address_id') == $address->id ? 'selected' : '' }}>
                            {{ $address->name }}
                        </option>
                    @endforeach
                </select>
            @endif

            @if($auth->role !== 'staff')
                <select class="form-select"
                        style="max-width:220px"
                        onchange="location.href = this.value">
                    @foreach($users as $staff)
                        <option
                            value="{{ route('availabilities.index', array_filter([
                                'address_id' => request('address_id'),
                                'user_id' => $staff->id
                            ])) }}"
                            {{ ($selectedUser instanceof \App\Models\User && $selectedUser->id === $staff->id) ? 'selected' : '' }}>
                            {{ $staff->name }}
                        </option>
                    @endforeach
                </select>
            @endif
        </div>

        <div class="col-md-12">
            <div id="working-calendar"></div>
        </div>

        @if($canEdit && $selectedUser instanceof \App\Models\User)
            <div class="col-md-12 mt-3">
                <form id="availability-form"
                      action="{{ route('availabilities.store', ['user' => $selectedUser->id]) }}"
                      method="POST">
                    @csrf
                    <input type="hidden" id="availability-input" name="availabilities">
                    <button type="button"
                            id="save-calendar"
                            class="btn btn-primary">
                        💾 Lưu lịch
                    </button>
                </form>
            </div>
        @endif
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.js"></script>
<script>
    window.availabilityConfig = {
        events: @json($workingEvents ?? []),
        canEdit: @json($canEdit)
    };
</script>
<script src="{{ asset('js/availabilities/staff_availabilities.js') }}"></script>
@endsection
