<?php

declare(strict_types=1);

namespace MoneyFlow\Controllers;

use MoneyFlow\Config\Config;
use MoneyFlow\Services\TransactionService;

class TransactionController
{
    public function __construct(
        private Config $config,
        private TransactionService $transactionService
    ) {
    }

    /**
     * @param array<string, mixed> $query
     * @param array<string, mixed> $overrides
     */
    public function index(array $query = [], array $overrides = []): array
    {
        $baseCurrency = $this->config->get('BASE_CURRENCY', 'UZS');
        $userId = (int) $this->config->get('DEMO_USER_ID', 1);

        $filters = [
            'type' => $query['type'] ?? null,
            'wallet_id' => $query['wallet_id'] ?? null,
            'category_id' => $query['category_id'] ?? null,
            'date_from' => $query['date_from'] ?? null,
            'date_to' => $query['date_to'] ?? null,
            'search' => $query['search'] ?? null,
        ];

        return array_merge([
            'baseCurrency' => $baseCurrency,
            'filters' => $filters,
            'transactions' => $this->transactionService->getTransactions($userId, $filters),
            'options' => $this->transactionService->getFormOptions($userId),
            'message' => $query['message'] ?? null,
        ], $overrides);
    }

    /**
     * @param array<string, mixed> $data
     * @return array{success: bool, message?: string, errors?: array<string, string>, old?: array<string, mixed>}
     */
    public function store(array $data): array
    {
        $userId = (int) $this->config->get('DEMO_USER_ID', 1);
        $baseCurrency = $this->config->get('BASE_CURRENCY', 'UZS');

        $payload = $data;
        if (empty($payload['currency'])) {
            $payload['currency'] = $baseCurrency;
        }

        try {
            $this->transactionService->createTransaction($userId, $payload);
        } catch (\Throwable $e) {
            return [
                'success' => false,
                'errors' => ['form' => $e->getMessage()],
                'old' => $payload,
            ];
        }

        return [
            'success' => true,
            'message' => 'Transaction saved successfully.',
        ];
    }
}
