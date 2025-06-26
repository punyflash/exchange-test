<?php

namespace App\Services\Exchange\Clients;

use GuzzleHttp\Promise\PromiseInterface;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Collection;

class BybitClient extends BaseClient
{
    protected string $url = 'https://api.bybit.com/v5';

    public function formatPrices(Response $data): array
    {
        return collect($data->json('result.list'))->mapWithKeys(fn ($item) => [
            $item['symbol'] => (float) $item['lastPrice'],
        ])->all();
    }

    public function formatPrice(Response $data, string $symbol): ?float
    {
        return $data->json('result.list.0.lastPrice');
    }

    public function getPrices(?PendingRequest $request = null): Response | PromiseInterface
    {
        return $this->client($request)
            ->get('/market/tickers', ['category' => 'spot']);
    }

    public function getPrice(string $symbol, ?PendingRequest $request = null): Response | PromiseInterface
    {
        $symbol = $this->formatSymbol($symbol);

        return $this->client($request)
            ->get('/market/tickers', ['category' => 'spot', 'symbol' => $symbol]);
    }
}
