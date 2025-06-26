<?php

namespace App\Services\Exchange\Clients;

use GuzzleHttp\Promise\PromiseInterface;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Collection;

class JuCoinClient extends BaseClient
{
    protected string $url = 'https://api.jucoin.com';

    public function formatPrices(Response $data): array
    {
        return collect($data->json('data'))->mapWithKeys(fn ($item) => [
            $this->formatSymbol($item['s']) => (float) $item['p'],
        ])->all();
    }

    public function formatPrice(Response $data, string $symbol): ?float
    {
        return $data->json('data.0.p');
    }

    public function getPrices(?PendingRequest $request = null): Response | PromiseInterface
    {
        return $this->client($request)->get('/v1/spot/public/ticker/price');
    }

    public function getPrice(string $symbol, ?PendingRequest $request = null): Response | PromiseInterface
    {
        return $this->client($request)
            ->get('/v1/spot/public/ticker/price', ['symbol' => $symbol]);
    }
}
