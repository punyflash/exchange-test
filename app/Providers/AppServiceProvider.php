<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }

    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(\App\Services\Exchange\MarketAggregator::class, fn () => new \App\Services\Exchange\MarketAggregator([
                'binance' => new \App\Services\Exchange\Clients\BinanceClient(),
                'jucoin' => new \App\Services\Exchange\Clients\JuCoinClient(),
                'poloniex' => new \App\Services\Exchange\Clients\PoloniexClient(),
                'bybit' => new \App\Services\Exchange\Clients\BybitClient(),
                'whitebit' => new \App\Services\Exchange\Clients\WhitebitClient(),
            ]),
        );
    }
}
