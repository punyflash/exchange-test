<?php

namespace App\Services\Exchange\Clients;

use GuzzleHttp\Promise\PromiseInterface;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Collection;

class BinanceClient extends BaseClient
{
    protected string $url = 'https://api.binance.com/api/v3';

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

    public function getPrices(?PendingRequest $request = null): Response | PromiseInterface
    {
        return $this->client($request)->get('/ticker/price');
    }

    public function getPrice(string $symbol, ?PendingRequest $request = null): Response | PromiseInterface
    {
        $symbol = $this->formatSymbol($symbol);

        return $this->client($request)->get('/ticker/price', ['symbol' => $symbol]);
    }
}
