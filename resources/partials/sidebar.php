<?php

declare(strict_types=1);
?>
<aside class="d-none d-md-flex flex-column bg-white border-end shadow-sm" style="width: 240px;">
    <div class="p-4 border-bottom">
        <div class="fw-semibold">Navigation</div>
    </div>
    <nav class="nav flex-column p-2 gap-1">
        <?php
        $links = [
            ['label' => 'Overview', 'href' => $appUrl . '?page=dashboard', 'icon' => 'bi-speedometer2', 'key' => 'dashboard'],
            ['label' => 'Transactions', 'href' => $appUrl . '?page=transactions', 'icon' => 'bi-table', 'key' => 'transactions'],
            ['label' => 'Budgets', 'href' => $appUrl . '?page=budgets', 'icon' => 'bi-bullseye', 'key' => 'budgets'],
            ['label' => 'Reports', 'href' => $appUrl . '?page=reports', 'icon' => 'bi-pie-chart', 'key' => 'reports'],
            ['label' => 'Wallets', 'href' => $appUrl . '?page=wallets', 'icon' => 'bi-wallet2', 'key' => 'wallets'],
            ['label' => 'Goals', 'href' => $appUrl . '?page=goals', 'icon' => 'bi-flag', 'key' => 'goals'],
            ['label' => 'Settings', 'href' => $appUrl . '?page=settings', 'icon' => 'bi-gear', 'key' => 'settings'],
        ];
        $current = $activePage ?? 'dashboard';
        foreach ($links as $link):
            $isActive = $link['key'] === $current;
        ?>
            <a class="nav-link d-flex align-items-center gap-2 rounded-3 px-3 py-2 <?= $isActive ? 'active fw-semibold' : 'text-secondary' ?>" href="<?= htmlspecialchars($link['href']) ?>">
                <i class="bi <?= htmlspecialchars($link['icon']) ?>"></i>
                <span><?= htmlspecialchars($link['label']) ?></span>
            </a>
        <?php endforeach; ?>
    </nav>
</aside>
