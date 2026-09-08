@extends('layouts.app')

@section('title', 'Video Session')

@section('content')
    <livewire:video-room :appointment-id="$appointmentId" />
@endsection
