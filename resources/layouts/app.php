<?php

declare(strict_types=1);

/** @var array{title:string, content:string} $data */
$title = $data['title'] ?? 'MoneyFlow';
$content = $data['content'] ?? '';

$scriptName = $_SERVER['SCRIPT_NAME'] ?? '';
$scriptDirectory = rtrim(str_replace('\\', '/', dirname($scriptName)), '/');
if ($scriptDirectory === '.' || $scriptDirectory === '/') {
    $scriptDirectory = '';
}

$baseUrl = $scriptDirectory;
$asset = static function (string $path) use ($baseUrl): string {
    $normalized = ltrim($path, '/');
    if ($baseUrl === '') {
        return $normalized;
    }

    return $baseUrl . '/' . $normalized;
};

$appUrl = $baseUrl === '' ? '/' : $baseUrl . '/';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title><?= htmlspecialchars($title) ?> · MoneyFlow</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="<?= htmlspecialchars($asset('assets/css/app.css')) ?>">
</head>
<body class="bg-light text-dark">
    <div class="d-flex">
        <?php require __DIR__ . '/../partials/sidebar.php'; ?>
        <main class="flex-grow-1 min-vh-100">
            <?php require __DIR__ . '/../partials/navbar.php'; ?>
            <div class="container py-4">
                <?= $content ?>
            </div>
        </main>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.13.1/dist/cdn.min.js" defer></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.3/dist/chart.umd.min.js"></script>
    <script src="<?= htmlspecialchars($asset('assets/js/dashboard.js')) ?>" type="module"></script>
</body>
</html>
