<?php

use App\Providers\AppServiceProvider;
use Illuminate\Support\ServiceProvider;

return ServiceProvider::defaultProviders()
    ->merge([
        AppServiceProvider::class,
    ])->toArray();
