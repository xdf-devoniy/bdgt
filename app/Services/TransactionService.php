<?php

declare(strict_types=1);

namespace MoneyFlow\Services;

use DateTimeImmutable;
use InvalidArgumentException;
use JsonException;
use PDO;

class TransactionService
{
    public function __construct(private Database $database)
    {
    }

    /**
     * @param array<string, mixed> $filters
     * @return array<int, array<string, mixed>>
     */
    public function getTransactions(int $userId, array $filters = []): array
    {
        $pdo = $this->database->getConnection();

        $sql = [
            'SELECT',
            '    t.id,',
            '    t.date,',
            '    t.type,',
            '    t.amount,',
            '    t.currency,',
            '    t.rate,',
            '    t.note,',
            '    t.merchant,',
            '    t.tags_json,',
            '    c.name AS category_name,',
            '    w.name AS wallet_name',
            'FROM transactions t',
            'LEFT JOIN categories c ON c.id = t.category_id',
            'LEFT JOIN wallets w ON w.id = t.wallet_id',
            'WHERE t.user_id = :user_id',
        ];

        $params = [':user_id' => $userId];

        if (!empty($filters['type']) && in_array($filters['type'], ['income', 'expense'], true)) {
            $sql[] = 'AND t.type = :type';
            $params[':type'] = $filters['type'];
        }

        if (!empty($filters['wallet_id'])) {
            $sql[] = 'AND t.wallet_id = :wallet_id';
            $params[':wallet_id'] = (int) $filters['wallet_id'];
        }

        if (!empty($filters['category_id'])) {
            $sql[] = 'AND t.category_id = :category_id';
            $params[':category_id'] = (int) $filters['category_id'];
        }

        if (!empty($filters['date_from'])) {
            $sql[] = 'AND t.date >= :date_from';
            $params[':date_from'] = $filters['date_from'];
        }

        if (!empty($filters['date_to'])) {
            $sql[] = 'AND t.date <= :date_to';
            $params[':date_to'] = $filters['date_to'];
        }

        if (!empty($filters['search'])) {
            $sql[] = 'AND (t.note LIKE :search OR t.merchant LIKE :search OR t.tags_json LIKE :search)';
            $params[':search'] = '%' . $filters['search'] . '%';
        }

        $sql[] = 'ORDER BY t.date DESC, t.id DESC';
        $sql[] = 'LIMIT 200';

        $stmt = $pdo->prepare(implode(' ', $sql));
        foreach ($params as $key => $value) {
            $paramType = is_int($value) ? PDO::PARAM_INT : PDO::PARAM_STR;
            $stmt->bindValue($key, $value, $paramType);
        }

        $stmt->execute();

        $transactions = [];
        foreach ($stmt->fetchAll() as $row) {
            $tags = [];
            if (!empty($row['tags_json'])) {
                try {
                    $decoded = json_decode((string) $row['tags_json'], true, 512, JSON_THROW_ON_ERROR);
                } catch (JsonException) {
                    $decoded = [];
                }

                if (is_array($decoded)) {
                    $tags = array_values(array_filter(array_map('strval', $decoded)));
                }
            }

            $transactions[] = [
                'id' => (int) $row['id'],
                'date' => $row['date'],
                'type' => $row['type'],
                'amount' => (float) $row['amount'],
                'currency' => $row['currency'],
                'rate' => (float) $row['rate'],
                'note' => $row['note'],
                'merchant' => $row['merchant'],
                'category' => $row['category_name'] ?? 'Uncategorised',
                'wallet' => $row['wallet_name'] ?? 'Wallet',
                'tags' => $tags,
            ];
        }

        return $transactions;
    }

    /**
     * @return array{wallets: array<int, array<string, mixed>>, categories: array<string, array<int, array<string, mixed>>>}
     */
    public function getFormOptions(int $userId): array
    {
        $pdo = $this->database->getConnection();

        $walletStmt = $pdo->prepare('SELECT id, name, currency FROM wallets WHERE user_id = :user_id ORDER BY name');
        $walletStmt->bindValue(':user_id', $userId, PDO::PARAM_INT);
        $walletStmt->execute();
        $wallets = $walletStmt->fetchAll();

        $categoryStmt = $pdo->prepare(
            "SELECT id, name, type FROM categories WHERE user_id = :user_id ORDER BY type, name"
        );
        $categoryStmt->bindValue(':user_id', $userId, PDO::PARAM_INT);
        $categoryStmt->execute();
        $categories = [
            'income' => [],
            'expense' => [],
        ];
        foreach ($categoryStmt->fetchAll() as $row) {
            if (!isset($categories[$row['type']])) {
                $categories[$row['type']] = [];
            }

            $categories[$row['type']][] = [
                'id' => (int) $row['id'],
                'name' => $row['name'],
            ];
        }

        return [
            'wallets' => array_map(static fn (array $wallet): array => [
                'id' => (int) $wallet['id'],
                'name' => $wallet['name'],
                'currency' => $wallet['currency'],
            ], $wallets),
            'categories' => $categories,
        ];
    }

    /**
     * @param array<string, mixed> $data
     */
    public function createTransaction(int $userId, array $data): void
    {
        $type = $data['type'] ?? '';
        if (!in_array($type, ['income', 'expense'], true)) {
            throw new InvalidArgumentException('Invalid transaction type provided.');
        }

        $amount = (float) ($data['amount'] ?? 0);
        if ($amount <= 0) {
            throw new InvalidArgumentException('Amount must be greater than zero.');
        }

        $walletId = (int) ($data['wallet_id'] ?? 0);
        $categoryId = (int) ($data['category_id'] ?? 0);
        if ($walletId <= 0 || $categoryId <= 0) {
            throw new InvalidArgumentException('Wallet and category are required.');
        }

        $dateString = $data['date'] ?? (new DateTimeImmutable())->format('Y-m-d');
        $date = DateTimeImmutable::createFromFormat('Y-m-d', $dateString) ?: new DateTimeImmutable();

        $currency = $data['currency'] ?? 'UZS';
        $rate = (float) ($data['rate'] ?? 1);
        if ($rate <= 0) {
            $rate = 1;
        }

        $note = trim((string) ($data['note'] ?? ''));
        $merchant = trim((string) ($data['merchant'] ?? ''));

        $tagsInput = (string) ($data['tags'] ?? '');
        $tags = [];
        if ($tagsInput !== '') {
            $tags = array_values(array_filter(array_map(static fn (string $tag): string => trim($tag), explode(',', $tagsInput))));
        }

        $pdo = $this->database->getConnection();
        $stmt = $pdo->prepare(
            'INSERT INTO transactions (user_id, wallet_id, category_id, type, amount, currency, rate, date, note, merchant, tags_json)
             VALUES (:user_id, :wallet_id, :category_id, :type, :amount, :currency, :rate, :date, :note, :merchant, :tags)'
        );

        $stmt->bindValue(':user_id', $userId, PDO::PARAM_INT);
        $stmt->bindValue(':wallet_id', $walletId, PDO::PARAM_INT);
        $stmt->bindValue(':category_id', $categoryId, PDO::PARAM_INT);
        $stmt->bindValue(':type', $type);
        $stmt->bindValue(':amount', $amount);
        $stmt->bindValue(':currency', $currency);
        $stmt->bindValue(':rate', $rate);
        $stmt->bindValue(':date', $date->format('Y-m-d'));
        $stmt->bindValue(':note', $note !== '' ? $note : null);
        $stmt->bindValue(':merchant', $merchant !== '' ? $merchant : null);
        $stmt->bindValue(':tags', json_encode($tags, JSON_THROW_ON_ERROR | JSON_UNESCAPED_UNICODE));

        $stmt->execute();
    }
}
