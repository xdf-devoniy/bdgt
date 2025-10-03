<?php

declare(strict_types=1);

/** @var array $viewData */
$metrics = $viewData['metrics'];
$baseCurrency = $viewData['baseCurrency'];
$trend = $viewData['trend'];
$topCategories = $viewData['topCategories'];
$budgets = $viewData['budgets'];
?>
<div class="row g-4">
    <div class="col-12 col-md-3">
        <div class="card shadow-sm border-0 h-100">
            <div class="card-body">
                <p class="text-muted text-uppercase small mb-1">Balance</p>
                <h3 class="fw-semibold mb-0"><?= number_format($metrics['balance']) ?> <?= $baseCurrency ?></h3>
            </div>
        </div>
    </div>
    <div class="col-12 col-md-3">
        <div class="card shadow-sm border-0 h-100">
            <div class="card-body">
                <p class="text-muted text-uppercase small mb-1">Income</p>
                <h3 class="text-success fw-semibold mb-0"><?= number_format($metrics['income']) ?> <?= $baseCurrency ?></h3>
            </div>
        </div>
    </div>
    <div class="col-12 col-md-3">
        <div class="card shadow-sm border-0 h-100">
            <div class="card-body">
                <p class="text-muted text-uppercase small mb-1">Expense</p>
                <h3 class="text-danger fw-semibold mb-0"><?= number_format($metrics['expense']) ?> <?= $baseCurrency ?></h3>
            </div>
        </div>
    </div>
    <div class="col-12 col-md-3">
        <div class="card shadow-sm border-0 h-100">
            <div class="card-body">
                <p class="text-muted text-uppercase small mb-1">Net</p>
                <h3 class="fw-semibold mb-0"><?= number_format($metrics['net']) ?> <?= $baseCurrency ?></h3>
            </div>
        </div>
    </div>
</div>

<div class="row g-4 mt-1">
    <div class="col-12 col-xl-8">
        <div class="card shadow-sm border-0 h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="card-title mb-0">90-day Cashflow Trend</h5>
                    <div class="text-muted small">Projected line shows forecast</div>
                </div>
                <div style="height: 320px;">
                    <canvas id="trendChart"
                        data-chart='<?= json_encode($trend, JSON_THROW_ON_ERROR) ?>'></canvas>
                </div>
            </div>
        </div>
    </div>
    <div class="col-12 col-xl-4">
        <div class="card shadow-sm border-0 h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="card-title mb-0">Top Categories</h5>
                    <div class="text-muted small">Share of spend</div>
                </div>
                <div style="height: 320px;">
                    <canvas id="categoryChart"
                        data-chart='<?= json_encode($topCategories, JSON_THROW_ON_ERROR) ?>'></canvas>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row g-4 mt-1">
    <div class="col-12">
        <div class="card shadow-sm border-0">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="card-title mb-0">Budgets</h5>
                    <button class="btn btn-sm btn-primary">Add Budget</button>
                </div>
                <div class="row g-3">
                    <?php foreach ($budgets as $budget):
                        $progress = min(100, (int) round(($budget['spent'] / $budget['limit']) * 100));
                        $statusClass = $progress >= 100 ? 'bg-danger' : ($progress >= 75 ? 'bg-warning' : 'bg-success');
                    ?>
                        <div class="col-12 col-md-4">
                            <div class="p-3 border rounded-4 h-100 shadow-sm bg-white">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <div class="fw-semibold"><?= $budget['label'] ?></div>
                                    <div class="text-muted small"><?= number_format($budget['spent']) ?> / <?= number_format($budget['limit']) ?></div>
                                </div>
                                <div class="progress rounded-pill" style="height: 10px;">
                                    <div class="progress-bar <?= $statusClass ?>" role="progressbar" style="width: <?= $progress ?>%;"
                                         aria-valuenow="<?= $progress ?>" aria-valuemin="0" aria-valuemax="100"></div>
                                </div>
                                <div class="text-muted small mt-2">Spent <?= $progress ?>%</div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>
</div>
