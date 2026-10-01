<?php
declare(strict_types=1);
require_once __DIR__ . '/../config/config.php';
require_login();
$pageTitle = $pageTitle ?? APP_NAME;
$active = basename($_SERVER['PHP_SELF']);
$flash = get_flash();
$user = current_user();
?>
<!doctype html>
<html lang="pt-PT">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="GeStock — sistema de gestão para pequenos negócios">
    <title><?= e($pageTitle) ?> · GeStock</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="assets/css/app.css" rel="stylesheet">
</head>
<body>
<div class="app-shell">
    <aside class="sidebar" id="sidebar">
        <div class="brand-block">
            <div class="brand-mark">G</div>
            <div>
                <div class="brand-name">GeStock</div>
                <div class="brand-sub">GESTÃO / OVERVIEW</div>
            </div>
        </div>

        <nav class="nav-stack">
            <div class="nav-label">GESTÃO</div>
            <a class="nav-link <?= $active === 'dashboard.php' ? 'active' : '' ?>" href="dashboard.php"><i class="bi bi-grid-1x2"></i><span>Dashboard</span></a>
            <a class="nav-link <?= $active === 'clientes.php' ? 'active' : '' ?>" href="clientes.php"><i class="bi bi-people"></i><span>Clientes</span></a>
            <a class="nav-link <?= in_array($active, ['produtos.php','produto_form.php'], true) ? 'active' : '' ?>" href="produtos.php"><i class="bi bi-box-seam"></i><span>Produtos</span></a>
            <a class="nav-link <?= in_array($active, ['vendas.php','venda_nova.php','venda_view.php'], true) ? 'active' : '' ?>" href="vendas.php"><i class="bi bi-receipt"></i><span>Vendas</span></a>
            <a class="nav-link <?= $active === 'stock.php' ? 'active' : '' ?>" href="stock.php"><i class="bi bi-bar-chart-line"></i><span>Stock</span></a>
        </nav>

        <div class="sidebar-bottom">
            <div class="user-mini">
                <div class="avatar"><?= e(strtoupper(substr($user['nome'] ?? 'U', 0, 1))) ?></div>
                <div class="user-copy">
                    <strong><?= e($user['nome'] ?? 'Utilizador') ?></strong>
                    <span><?= e($user['username'] ?? '') ?></span>
                </div>
            </div>
            <a class="logout-link" href="logout.php"><i class="bi bi-box-arrow-right"></i> Sair</a>
        </div>
    </aside>

    <main class="main-area">
        <header class="topbar">
            <button class="mobile-menu" type="button" onclick="document.getElementById('sidebar').classList.toggle('open')"><i class="bi bi-list"></i></button>
            <div>
                <div class="eyebrow">PAP • TÉCNICO DE INFORMÁTICA DE GESTÃO</div>
                <h1><?= e($pageTitle) ?></h1>
            </div>
            <div class="topbar-meta"><span class="status-dot"></span> Sistema operacional</div>
        </header>

        <div class="content-wrap">
            <?php if ($flash): ?>
                <div class="alert alert-<?= e($flash['type']) ?> app-alert" role="alert"><?= e($flash['message']) ?></div>
            <?php endif; ?>
