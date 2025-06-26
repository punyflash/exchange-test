<?php

namespace App\Commands;

use App\Services\Exchange\MarketAggregator;
use LaravelZero\Framework\Commands\Command;

class CryptoCompare extends Command
{
    protected $signature = 'crypto:compare
        {symbol? : The crypto pair to compare (ex. CUR1_CUR2)}
        {--I|with-income : Include exchange income}';

    protected $description = 'Compare crypto pairs on available exchanges';

    public function handle(MarketAggregator $aggregator): int
    {
        $symbol = $this->argument('symbol');

        $data = $symbol
            ? [strtoupper(str_replace('_', '', $symbol)) => $aggregator->getPrice($symbol)->toArray()]
            : $aggregator->getPrices()->toArray();

        $this->table(...$this->getTableData($aggregator->getExchanges()->all(), $data));

        return static::SUCCESS;
    }

    protected function getTableData(array $exchanges, array $data): array
    {
        $add = $this->option('with-income')
            ? ['Min', 'Max', 'Income']
            : ['Min', 'Max'];

        $header = ['Symbol', ...array_map('ucfirst', $exchanges), ...$add];

        $data = collect($data)
            ->filter(fn ($items) => count($items) === count($exchanges))
            ->map(fn ($items, $symbol) => [
                'symbol' => $symbol,
                ...$items,
                'min' => array_search($min = min($items), $items),
                'max' => array_search($max = max($items), $items),
                ...($this->option('with-income')
                    ? ['income' => $this->income($min, $max)]
                    : []),
            ])
            ->sortBy(
                $this->option('with-income') ? 'income' : 'symbol',
                $this->option('with-income') ? SORT_NUMERIC : SORT_REGULAR,
            );

        return [$header, $data];
    }

    protected function income(float $min, float $max): string
    {
        return round(($max - $min) / $min * 100, 2).'%';
    }
}
