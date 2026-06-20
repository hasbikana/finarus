@extends('layouts.app')

@section('title', 'Profile - Finarus')
@section('page-title', 'Edit Profile')
@section('page-description', 'Kelola informasi akun dan keamanan Anda')

@section('content')
<div class="max-w-2xl space-y-4">
    <div class="bg-card rounded-lg shadow-lg p-5">
        @include('profile.partials.update-profile-information-form')
    </div>

    <div class="bg-card rounded-lg shadow-lg p-5">
        @include('profile.partials.update-password-form')
    </div>

    <div class="bg-card rounded-lg shadow-lg p-5">
        @include('profile.partials.delete-user-form')
    </div>
</div>
@endsection
