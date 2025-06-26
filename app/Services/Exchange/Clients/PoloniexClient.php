<?php

namespace App\Services\Exchange\Clients;

use GuzzleHttp\Promise\PromiseInterface;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\Response;

class PoloniexClient extends BaseClient
{
    protected string $url = 'https://api.poloniex.com';

    public function formatPrices(Response $data): array
    {
        return collect($data->json())->mapWithKeys(fn ($item) => [
            $this->formatSymbol($item['symbol']) => (float) $item['price'],
        ])->all();
    }

    public function formatPrice(Response $data, string $symbol): ?float
    {
        return $data->json('price');
    }

    public function getPrices(?PendingRequest $request = null): Response|PromiseInterface
    {
        return $this->client($request)->get('/markets/price');
    }

    public function getPrice(string $symbol, ?PendingRequest $request = null): Response|PromiseInterface
    {
        return $this->client($request)->get("/markets/{$symbol}/price");
    }
}
