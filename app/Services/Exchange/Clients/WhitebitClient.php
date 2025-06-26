<?php

namespace App\Services\Exchange\Clients;

use GuzzleHttp\Promise\PromiseInterface;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\Response;

class WhitebitClient extends BaseClient
{
    protected string $url = 'https://whitebit.com/api/v4/public';

    public function formatPrices(Response $data): array
    {
        return collect($data->json())->mapWithKeys(fn ($item, $symbol) => [
            $this->formatSymbol($symbol) => (float) $item['last_price'],
        ])->all();
    }

    public function formatPrice(Response $data, string $symbol): ?float
    {
        return $data->json("$symbol.last_price");
    }

    public function getPrices(?PendingRequest $request = null): Response|PromiseInterface
    {
        return $this->client($request)->get('/ticker');
    }

    public function getPrice(string $symbol, ?PendingRequest $request = null): Response|PromiseInterface
    {
        return $this->client($request)->get('/ticker');
    }
}
