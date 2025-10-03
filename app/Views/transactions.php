<?php

declare(strict_types=1);

/** @var array $viewData */
$transactions = $viewData['transactions'];
$filters = $viewData['filters'];
$options = $viewData['options'];
$baseCurrency = $viewData['baseCurrency'];
$message = $viewData['message'] ?? null;
$errors = $viewData['errors'] ?? [];
$old = $viewData['old'] ?? [];
?>
<div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-center gap-3 mb-4">
    <div>
        <h1 class="h3 fw-semibold mb-1">Transactions</h1>
        <p class="text-muted mb-0">Track incomes and expenses in so'm (<?= htmlspecialchars($baseCurrency) ?>).</p>
    </div>
    <div class="d-flex gap-2">
        <a href="?page=transactions" class="btn btn-outline-secondary">Reset Filters</a>
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#transactionModal">New Transaction</button>
    </div>
</div>

<?php if (!empty($message)): ?>
    <div class="alert alert-success shadow-sm border-0"><?= htmlspecialchars($message) ?></div>
<?php endif; ?>

<?php if (!empty($errors['form'])): ?>
    <div class="alert alert-danger shadow-sm border-0"><?= htmlspecialchars($errors['form']) ?></div>
<?php endif; ?>

<div class="card shadow-sm border-0 mb-4">
    <div class="card-body">
        <form class="row g-3 align-items-end" method="get">
            <input type="hidden" name="page" value="transactions" />
            <div class="col-12 col-md-2">
                <label class="form-label" for="filterType">Type</label>
                <select class="form-select" id="filterType" name="type">
                    <option value="">All</option>
                    <option value="income" <?= $filters['type'] === 'income' ? 'selected' : '' ?>>Income</option>
                    <option value="expense" <?= $filters['type'] === 'expense' ? 'selected' : '' ?>>Expense</option>
                </select>
            </div>
            <div class="col-12 col-md-2">
                <label class="form-label" for="filterWallet">Wallet</label>
                <select class="form-select" id="filterWallet" name="wallet_id">
                    <option value="">All</option>
                    <?php foreach ($options['wallets'] as $wallet): ?>
                        <option value="<?= $wallet['id'] ?>" <?= (string) $filters['wallet_id'] === (string) $wallet['id'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($wallet['name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-12 col-md-2">
                <label class="form-label" for="filterCategory">Category</label>
                <select class="form-select" id="filterCategory" name="category_id">
                    <option value="">All</option>
                    <?php foreach ($options['categories']['income'] as $category): ?>
                        <option value="<?= $category['id'] ?>" <?= (string) $filters['category_id'] === (string) $category['id'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($category['name']) ?> (Income)
                        </option>
                    <?php endforeach; ?>
                    <?php foreach ($options['categories']['expense'] as $category): ?>
                        <option value="<?= $category['id'] ?>" <?= (string) $filters['category_id'] === (string) $category['id'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($category['name']) ?> (Expense)
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-12 col-md-2">
                <label class="form-label" for="filterFrom">From</label>
                <input type="date" class="form-control" id="filterFrom" name="date_from" value="<?= htmlspecialchars((string) $filters['date_from']) ?>" />
            </div>
            <div class="col-12 col-md-2">
                <label class="form-label" for="filterTo">To</label>
                <input type="date" class="form-control" id="filterTo" name="date_to" value="<?= htmlspecialchars((string) $filters['date_to']) ?>" />
            </div>
            <div class="col-12 col-md-2">
                <label class="form-label" for="filterSearch">Search</label>
                <input type="text" class="form-control" id="filterSearch" placeholder="Merchant, note or tag" name="search" value="<?= htmlspecialchars((string) $filters['search']) ?>" />
            </div>
            <div class="col-12 d-flex justify-content-end">
                <button type="submit" class="btn btn-primary">Apply Filters</button>
            </div>
        </form>
    </div>
</div>

<div class="card shadow-sm border-0">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th scope="col">Date</th>
                        <th scope="col">Type</th>
                        <th scope="col">Category</th>
                        <th scope="col">Wallet</th>
                        <th scope="col" class="text-end">Amount (<?= htmlspecialchars($baseCurrency) ?>)</th>
                        <th scope="col">Merchant</th>
                        <th scope="col">Tags</th>
                        <th scope="col">Note</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($transactions)): ?>
                        <tr>
                            <td colspan="8" class="text-center py-5 text-muted">No transactions found. Try adjusting the filters or add your first entry.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($transactions as $transaction): ?>
                            <tr>
                                <td><?= htmlspecialchars($transaction['date']) ?></td>
                                <td>
                                    <span class="badge rounded-pill <?= $transaction['type'] === 'income' ? 'text-bg-success' : 'text-bg-danger' ?>">
                                        <?= ucfirst($transaction['type']) ?>
                                    </span>
                                </td>
                                <td><?= htmlspecialchars($transaction['category']) ?></td>
                                <td><?= htmlspecialchars($transaction['wallet']) ?></td>
                                <td class="text-end fw-semibold <?= $transaction['type'] === 'expense' ? 'text-danger' : 'text-success' ?>">
                                    <?= number_format($transaction['amount'], 2) ?>
                                </td>
                                <td><?= htmlspecialchars($transaction['merchant'] ?? '—') ?></td>
                                <td>
                                    <?php if (!empty($transaction['tags'])): ?>
                                        <div class="d-flex flex-wrap gap-1">
                                            <?php foreach ($transaction['tags'] as $tag): ?>
                                                <span class="badge rounded-pill bg-primary-subtle text-primary">#<?= htmlspecialchars($tag) ?></span>
                                            <?php endforeach; ?>
                                        </div>
                                    <?php else: ?>
                                        <span class="text-muted">—</span>
                                    <?php endif; ?>
                                </td>
                                <td><?= htmlspecialchars($transaction['note'] ?? '—') ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="modal fade" id="transactionModal" tabindex="-1" aria-labelledby="transactionModalLabel" aria-hidden="true" data-force-show="<?= !empty($errors) ? '1' : '0' ?>">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <form method="post" class="needs-validation" novalidate>
                <div class="modal-header border-0 pb-0">
                    <div>
                        <h5 class="modal-title fw-semibold" id="transactionModalLabel">Quick Add Transaction</h5>
                        <p class="text-muted small mb-0">Capture income or expense in a couple of taps.</p>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-12 col-md-4">
                            <label class="form-label" for="transactionType">Type</label>
                            <select class="form-select" id="transactionType" name="type" required>
                                <option value="income" <?= ($old['type'] ?? '') === 'income' ? 'selected' : '' ?>>Income</option>
                                <option value="expense" <?= ($old['type'] ?? 'expense') === 'expense' ? 'selected' : '' ?>>Expense</option>
                            </select>
                        </div>
                        <div class="col-12 col-md-4">
                            <label class="form-label" for="transactionDate">Date</label>
                            <input type="date" class="form-control" id="transactionDate" name="date" value="<?= htmlspecialchars((string) ($old['date'] ?? date('Y-m-d'))) ?>" required />
                        </div>
                        <div class="col-12 col-md-4">
                            <label class="form-label" for="transactionAmount">Amount (<?= htmlspecialchars($baseCurrency) ?>)</label>
                            <input type="number" step="0.01" min="0" class="form-control" id="transactionAmount" name="amount" value="<?= htmlspecialchars((string) ($old['amount'] ?? '')) ?>" required />
                        </div>
                        <div class="col-12 col-md-6">
                            <label class="form-label" for="transactionWallet">Wallet</label>
                            <select class="form-select" id="transactionWallet" name="wallet_id" required>
                                <option value="">Select wallet</option>
                                <?php foreach ($options['wallets'] as $wallet): ?>
                                    <option value="<?= $wallet['id'] ?>" <?= (string) ($old['wallet_id'] ?? '') === (string) $wallet['id'] ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($wallet['name']) ?> (<?= htmlspecialchars($wallet['currency']) ?>)
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-12 col-md-6">
                            <label class="form-label" for="transactionCategory">Category</label>
                            <select class="form-select" id="transactionCategory" name="category_id" required data-category-options='<?= json_encode($options['categories'], JSON_THROW_ON_ERROR) ?>'>
                                <option value="">Select category</option>
                                <?php foreach ($options['categories']['expense'] as $category): ?>
                                    <option data-type="expense" value="<?= $category['id'] ?>" <?= (string) ($old['category_id'] ?? '') === (string) $category['id'] ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($category['name']) ?>
                                    </option>
                                <?php endforeach; ?>
                                <?php foreach ($options['categories']['income'] as $category): ?>
                                    <option data-type="income" value="<?= $category['id'] ?>" <?= (string) ($old['category_id'] ?? '') === (string) $category['id'] ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($category['name']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-12 col-md-6">
                            <label class="form-label" for="transactionMerchant">Merchant</label>
                            <input type="text" class="form-control" id="transactionMerchant" name="merchant" placeholder="Where did the money go?" value="<?= htmlspecialchars((string) ($old['merchant'] ?? '')) ?>" />
                        </div>
                        <div class="col-12 col-md-6">
                            <label class="form-label" for="transactionTags">Tags</label>
                            <input type="text" class="form-control" id="transactionTags" name="tags" placeholder="vacation, groceries" value="<?= htmlspecialchars((string) ($old['tags'] ?? '')) ?>" />
                            <div class="form-text">Separate tags with commas.</div>
                        </div>
                        <div class="col-12">
                            <label class="form-label" for="transactionNote">Note</label>
                            <textarea class="form-control" id="transactionNote" name="note" rows="2" placeholder="Add an optional note"><?= htmlspecialchars((string) ($old['note'] ?? '')) ?></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save Transaction</button>
                </div>
            </form>
        </div>
    </div>
</div>
