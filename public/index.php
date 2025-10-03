<?php

declare(strict_types=1);

use MoneyFlow\Config\Config;
use MoneyFlow\Controllers\DashboardController;
use MoneyFlow\Controllers\TransactionController;
use MoneyFlow\Services\CashflowService;
use MoneyFlow\Services\Database;
use MoneyFlow\Services\TransactionService;

require dirname(__DIR__) . '/bootstrap.php';

$config = new Config(dirname(__DIR__));
$database = new Database($config);
$cashflowService = new CashflowService($database);
$transactionService = new TransactionService($database);

$page = $_GET['page'] ?? 'dashboard';

$viewData = [];
$title = 'MoneyFlow';

switch ($page) {
    case 'dashboard':
    default:
        $controller = new DashboardController($config, $cashflowService);
        $viewData = $controller->index();
        $view = __DIR__ . '/../app/Views/dashboard.php';
        $title = 'Overview';
        break;
    case 'transactions':
        $controller = new TransactionController($config, $transactionService);
        if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST') {
            $response = $controller->store($_POST);
            if (!empty($response['success'])) {
                $message = $response['message'] ?? 'Saved';
                $self = $_SERVER['PHP_SELF'] ?? '/index.php';
                $redirect = $self . '?page=transactions&message=' . rawurlencode($message);
                header('Location: ' . $redirect, true, 303);
                exit;
            }

            $viewData = $controller->index($_GET, [
                'errors' => $response['errors'] ?? [],
                'old' => $response['old'] ?? [],
            ]);
        } else {
            $viewData = $controller->index($_GET);
        }
        $view = __DIR__ . '/../app/Views/transactions.php';
        $title = 'Transactions';
        break;
}

ob_start();
if (isset($view)) {
    /** @psalm-suppress UnresolvableInclude */
    require $view;
}
$content = ob_get_clean();

$layoutData = [
    'title' => $title,
    'content' => $content,
];

$activePage = $page;
$data = $layoutData;

require dirname(__DIR__) . '/resources/layouts/app.php';
