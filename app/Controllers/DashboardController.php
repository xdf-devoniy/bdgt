<?php

declare(strict_types=1);

namespace MoneyFlow\Controllers;

use DateTimeImmutable;
use MoneyFlow\Config\Config;
use MoneyFlow\Services\CashflowService;

class DashboardController
{
    public function __construct(
        private Config $config,
        private CashflowService $cashflowService
    ) {
    }

    public function index(): array
    {
        $baseCurrency = $this->config->get('BASE_CURRENCY', 'UZS');
        $userId = (int) $this->config->get('DEMO_USER_ID', 1);

        $today = new DateTimeImmutable('today');
        $from = $today->modify('-89 days');

        return [
            'baseCurrency' => $baseCurrency,
            'metrics' => $this->cashflowService->getMetrics($userId),
            'trend' => $this->cashflowService->getTrend($userId, $from, $today),
            'topCategories' => $this->cashflowService->getTopCategories($userId, 5, $from, $today),
            'budgets' => $this->cashflowService->getBudgets($userId),
        ];
    }
}
