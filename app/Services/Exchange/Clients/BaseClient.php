<?php

namespace App\Services\Exchange\Clients;

use GuzzleHttp\Promise\PromiseInterface;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;

abstract class BaseClient
{
    protected string $url;

    protected function client(?PendingRequest $request = null): PendingRequest
    {
        return $request
            ? $request->baseUrl($this->url)->throw()
            : Http::baseUrl($this->url)->throw();
    }

    protected function formatSymbol(string $symbol): string
    {
        return strtoupper(str_replace('_', '', $symbol));
    }

    abstract public function formatPrices(Response $data): array;

    abstract public function formatPrice(Response $data, string $symbol): ?float;

    abstract public function getPrices(?PendingRequest $request = null): Response|PromiseInterface;

    abstract public function getPrice(string $symbol, ?PendingRequest $request = null): Response|PromiseInterface;
}
