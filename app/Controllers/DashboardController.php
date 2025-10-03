<?php

declare(strict_types=1);

namespace MoneyFlow\Controllers;

use MoneyFlow\Config\Config;

class DashboardController
{
    public function __construct(private Config $config)
    {
    }

    public function index(): array
    {
        $baseCurrency = $this->config->get('BASE_CURRENCY', 'UZS');

        return [
            'baseCurrency' => $baseCurrency,
            'metrics' => [
                'balance' => 12450000,
                'income' => 5600000,
                'expense' => 4350000,
                'net' => 1250000,
            ],
            'trend' => [
                ['date' => '2024-01-01', 'income' => 1500000, 'expense' => 1200000],
                ['date' => '2024-02-01', 'income' => 1400000, 'expense' => 1100000],
                ['date' => '2024-03-01', 'income' => 1350000, 'expense' => 900000],
                ['date' => '2024-04-01', 'income' => 1350000, 'expense' => 950000],
                ['date' => '2024-05-01', 'income' => 1500000, 'expense' => 1180000],
            ],
            'topCategories' => [
                ['label' => 'Oziq-ovqat', 'value' => 35],
                ['label' => 'Transport', 'value' => 18],
                ['label' => 'Uy-joy', 'value' => 15],
                ['label' => "Ko'ngilochar", 'value' => 12],
                ['label' => 'Boshqalar', 'value' => 20],
            ],
            'budgets' => [
                ['label' => 'Oylik', 'spent' => 3200000, 'limit' => 4000000],
                ['label' => 'Transport', 'spent' => 450000, 'limit' => 600000],
                ['label' => "Ko'ngilochar", 'spent' => 280000, 'limit' => 500000],
            ],
        ];
    }
}
