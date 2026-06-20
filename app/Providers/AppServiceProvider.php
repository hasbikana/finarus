<?php

namespace App\Providers;

use App\Services\EmailParserService;
use App\Parsers\BcaParser;
use App\Parsers\MandiriParser;
use App\Parsers\BniParser;
use App\Parsers\BriParser;
use App\Parsers\GopayParser;
use App\Parsers\OvoParser;
use App\Parsers\DanaParser;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(EmailParserService::class, function () {
            $service = new EmailParserService;
            $service->register(new BcaParser);
            $service->register(new MandiriParser);
            $service->register(new BniParser);
            $service->register(new BriParser);
            $service->register(new GopayParser);
            $service->register(new OvoParser);
            $service->register(new DanaParser);
            return $service;
        });
    }

    public function boot(): void
    {
        //
    }
}
