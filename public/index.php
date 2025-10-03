<?php

declare(strict_types=1);

use MoneyFlow\Config\Config;
use MoneyFlow\Controllers\DashboardController;
use MoneyFlow\Services\CashflowService;
use MoneyFlow\Services\Database;

require dirname(__DIR__) . '/bootstrap.php';

$config = new Config(dirname(__DIR__));
$database = new Database($config);
$cashflowService = new CashflowService($database);

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
