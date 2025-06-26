<?php

namespace App\Services\Exchange;

use App\Services\Exchange\Clients\BaseClient;
use Illuminate\Http\Client\Pool;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;

class MarketAggregator
{
    public function __construct(
        /** @var array<BaseClient> */
        protected array $exchanges
    ) {}

    public function getExchanges(): Collection
    {
        return collect($this->exchanges)->keys();
    }

    public function getPrices(): Collection
    {
        $prices = Http::pool(fn (Pool $pool) => collect($this->exchanges)
            ->map(fn (BaseClient $exchange, string|int $key) => $exchange->getPrices($pool->as($key)))
        );

        $prices = collect($prices)
            ->map(fn (Response $response, string|int $key) => $this->exchanges[$key]->formatPrices($response));

        $result = collect();

        foreach ($prices as $exchange => $data) {
            foreach ($data as $symbol => $price) {
                $result->put($symbol, array_merge($result->get($symbol, []), [$exchange => $price]));
            }
        }

        return $result;
    }

    public function getPrice(string $symbol): Collection
    {
        $prices = Http::pool(fn (Pool $pool) => collect($this->exchanges)
            ->map(fn (BaseClient $exchange, string|int $key) => $exchange->getPrice($symbol, $pool->as($key)))
        );

        return collect($prices)
            ->map(fn (Response $response, string|int $key) => $this->exchanges[$key]->formatPrice($response, $symbol))
            ->filter();
    }
}
