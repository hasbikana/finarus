<?php

use App\Jobs\FetchBankEmails;
use App\Models\User;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Schedule::call(function () {
    User::whereHas('settings', fn($q) => $q->where('email_fetch_enabled', true))
        ->whereHas('oauthTokens')
        ->each(fn($user) => FetchBankEmails::dispatch($user->id));
})->everyTenMinutes()->name('fetch-bank-emails');
