<?php

declare(strict_types=1);

namespace MoneyFlow\Services;

use DateTimeImmutable;
use PDO;

class CashflowService
{
    public function __construct(private Database $database)
    {
    }

    public function getMetrics(int $userId): array
    {
        $pdo = $this->database->getConnection();

        $walletStmt = $pdo->prepare(
            'SELECT COALESCE(SUM(opening_balance), 0) AS opening_balance FROM wallets WHERE user_id = :user_id'
        );
        $walletStmt->bindValue(':user_id', $userId, PDO::PARAM_INT);
        $walletStmt->execute();
        $walletTotals = $walletStmt->fetch();

        $transactionStmt = $pdo->prepare(
            "SELECT 
                COALESCE(SUM(CASE WHEN type = 'income' THEN amount ELSE 0 END), 0) AS total_income,
                COALESCE(SUM(CASE WHEN type = 'expense' THEN amount ELSE 0 END), 0) AS total_expense,
                COALESCE(SUM(CASE WHEN type = 'income' THEN amount ELSE -amount END), 0) AS net_movement
            FROM transactions
            WHERE user_id = :user_id"
        );
        $transactionStmt->bindValue(':user_id', $userId, PDO::PARAM_INT);
        $transactionStmt->execute();
        $transactionTotals = $transactionStmt->fetch();

        $openingBalance = (float) ($walletTotals['opening_balance'] ?? 0);
        $income = (float) ($transactionTotals['total_income'] ?? 0);
        $expense = (float) ($transactionTotals['total_expense'] ?? 0);
        $net = (float) ($transactionTotals['net_movement'] ?? 0);
        $balance = $openingBalance + $net;

        return [
            'balance' => $balance,
            'income' => $income,
            'expense' => $expense,
            'net' => $net,
        ];
    }

    public function getTrend(int $userId, DateTimeImmutable $from, ?DateTimeImmutable $to = null): array
    {
        $pdo = $this->database->getConnection();

        $to ??= new DateTimeImmutable('today');

        $stmt = $pdo->prepare(
            "SELECT 
                DATE(date) AS day,
                COALESCE(SUM(CASE WHEN type = 'income' THEN amount ELSE 0 END), 0) AS income,
                COALESCE(SUM(CASE WHEN type = 'expense' THEN amount ELSE 0 END), 0) AS expense
            FROM transactions
            WHERE user_id = :user_id
              AND date >= :from
              AND date <= :to
            GROUP BY day
            ORDER BY day"
        );
        $stmt->bindValue(':user_id', $userId, PDO::PARAM_INT);
        $stmt->bindValue(':from', $from->format('Y-m-d'));
        $stmt->bindValue(':to', $to->format('Y-m-d'));
        $stmt->execute();

        $rows = $stmt->fetchAll();

        $trend = [];
        foreach ($rows as $row) {
            $trend[] = [
                'date' => $row['day'],
                'income' => (float) $row['income'],
                'expense' => (float) $row['expense'],
            ];
        }

        return $trend;
    }

    public function getTopCategories(
        int $userId,
        int $limit,
        DateTimeImmutable $from,
        ?DateTimeImmutable $to = null
    ): array {
        $pdo = $this->database->getConnection();

        $to ??= new DateTimeImmutable('today');

        $stmt = $pdo->prepare(
            "SELECT 
                c.name AS label,
                COALESCE(SUM(t.amount), 0) AS total
            FROM transactions t
            INNER JOIN categories c ON c.id = t.category_id
            WHERE t.user_id = :user_id
              AND t.type = 'expense'
              AND t.date >= :from
              AND t.date <= :to
            GROUP BY t.category_id, c.name
            ORDER BY total DESC
            LIMIT :limit"
        );
        $stmt->bindValue(':user_id', $userId, PDO::PARAM_INT);
        $stmt->bindValue(':from', $from->format('Y-m-d'));
        $stmt->bindValue(':to', $to->format('Y-m-d'));
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();

        $rows = $stmt->fetchAll();

        $categories = [];
        foreach ($rows as $row) {
            $categories[] = [
                'label' => $row['label'] ?? 'Uncategorized',
                'value' => (float) $row['total'],
            ];
        }

        return $categories;
    }

    public function getBudgets(int $userId, int $limit = 6): array
    {
        $pdo = $this->database->getConnection();

        $stmt = $pdo->prepare(
            "SELECT 
                b.id,
                b.amount,
                b.period_start,
                b.period_end,
                b.scope,
                b.category_id,
                c.name AS category_name
            FROM budgets b
            LEFT JOIN categories c ON c.id = b.category_id
            WHERE b.user_id = :user_id
            ORDER BY b.period_end DESC, b.id DESC
            LIMIT :limit"
        );
        $stmt->bindValue(':user_id', $userId, PDO::PARAM_INT);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();

        $budgets = [];

        $spentStmt = $pdo->prepare(
            "SELECT 
                COALESCE(SUM(amount), 0) AS spent
            FROM transactions
            WHERE user_id = :user_id
              AND type = 'expense'
              AND date >= :from
              AND date <= :to
              AND (:category_id IS NULL OR category_id = :category_id)"
        );

        foreach ($stmt->fetchAll() as $budget) {
            $categoryId = $budget['category_id'] ?? null;
            $scope = $budget['scope'] ?? null;

            if ($categoryId === null && $scope !== null && $scope !== 'overall' && is_numeric($scope)) {
                $categoryId = (int) $scope;
            }

            $label = 'Overall';
            if ($categoryId !== null) {
                $label = $budget['category_name'] ?? ('Category #' . $categoryId);
            }

            $spentStmt->bindValue(':user_id', $userId, PDO::PARAM_INT);
            $spentStmt->bindValue(':from', (new DateTimeImmutable($budget['period_start']))->format('Y-m-d'));
            $spentStmt->bindValue(':to', (new DateTimeImmutable($budget['period_end']))->format('Y-m-d'));

            if ($categoryId === null) {
                $spentStmt->bindValue(':category_id', null, PDO::PARAM_NULL);
            } else {
                $spentStmt->bindValue(':category_id', (int) $categoryId, PDO::PARAM_INT);
            }

            $spentStmt->execute();
            $spentRow = $spentStmt->fetch();
            $spentStmt->closeCursor();

            $spent = (float) ($spentRow['spent'] ?? 0);

            $budgets[] = [
                'id' => (int) $budget['id'],
                'label' => $label,
                'limit' => (float) $budget['amount'],
                'spent' => $spent,
                'period_start' => $budget['period_start'],
                'period_end' => $budget['period_end'],
            ];
        }

        return $budgets;
    }
}
