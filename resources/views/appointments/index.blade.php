@extends('layouts.app')

@section('title', 'Clinical Calendar & Visits')

@section('content')
    <div class="space-y-6 max-w-7xl mx-auto">
        {{-- Booking Livewire Component --}}
        <livewire:appointment-booking />
    </div>
@endsection
