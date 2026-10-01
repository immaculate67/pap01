<?php
declare(strict_types=1);
$pageTitle = 'Dashboard';
require_once __DIR__ . '/includes/header.php';

$stats = [
    'vendas' => (float)$pdo->query('SELECT COALESCE(SUM(total),0) FROM vendas WHERE DATE(data_venda)=CURDATE()')->fetchColumn(),
    'clientes' => (int)$pdo->query('SELECT COUNT(*) FROM clientes')->fetchColumn(),
    'produtos' => (int)$pdo->query('SELECT COUNT(*) FROM produtos')->fetchColumn(),
    'stock_baixo' => (int)$pdo->query('SELECT COUNT(*) FROM produtos WHERE stock <= ' . STOCK_LOW_THRESHOLD)->fetchColumn(),
];
$recent = $pdo->query('SELECT v.id, v.total, v.data_venda, c.nome AS cliente FROM vendas v LEFT JOIN clientes c ON c.id=v.cliente_id ORDER BY v.id DESC LIMIT 6')->fetchAll();
$low = $pdo->query('SELECT id, nome, stock, preco FROM produtos WHERE stock <= ' . STOCK_LOW_THRESHOLD . ' ORDER BY stock ASC, nome ASC LIMIT 5')->fetchAll();
?>
<div class="page-intro"><div><span class="section-tag">OVERVIEW</span><p>Estado atual do negócio num único ponto de controlo.</p></div><a href="venda_nova.php" class="btn btn-primary app-btn"><i class="bi bi-plus-lg"></i> Nova venda</a></div>

<div class="metric-grid">
    <div class="metric-card gold"><span>VENDAS</span><strong>€ <?= number_format($stats['vendas'],2,',',' ') ?></strong><small>Hoje</small></div>
    <div class="metric-card teal"><span>CLIENTES</span><strong><?= $stats['clientes'] ?></strong><small>Registos</small></div>
    <div class="metric-card teal"><span>PRODUTOS</span><strong><?= $stats['produtos'] ?></strong><small>Catálogo</small></div>
    <div class="metric-card red"><span>STOCK BAIXO</span><strong><?= $stats['stock_baixo'] ?></strong><small>≤ <?= STOCK_LOW_THRESHOLD ?> unidades</small></div>
</div>

<div class="dashboard-grid">
    <section class="panel-card">
        <div class="panel-head"><div><span class="panel-label">ATIVIDADE RECENTE</span><h2>Últimas vendas</h2></div><a href="vendas.php" class="text-link">Ver todas <i class="bi bi-arrow-up-right"></i></a></div>
        <div class="activity-list">
        <?php foreach ($recent as $sale): ?>
            <a class="activity-row" href="venda_view.php?id=<?= (int)$sale['id'] ?>"><div><strong>Venda #<?= (int)$sale['id'] ?></strong><span><?= e($sale['cliente'] ?: 'Cliente não identificado') ?></span></div><strong>€ <?= number_format((float)$sale['total'],2,',',' ') ?></strong><span><?= date('d/m', strtotime($sale['data_venda'])) ?></span></a>
        <?php endforeach; if (!$recent): ?><div class="empty-state">Ainda não existem vendas.</div><?php endif; ?>
        </div>
    </section>
    <section class="panel-card alert-panel">
        <div class="panel-head"><div><span class="panel-label">ATENÇÃO</span><h2>Stock baixo</h2></div><a href="stock.php" class="text-link">Abrir stock <i class="bi bi-arrow-up-right"></i></a></div>
        <?php foreach ($low as $item): ?><div class="stock-row"><div><strong><?= e($item['nome']) ?></strong><span>€ <?= number_format((float)$item['preco'],2,',',' ') ?></span></div><span class="stock-badge <?= (int)$item['stock'] <= 3 ? 'critical' : '' ?>"><?= (int)$item['stock'] ?> un.</span></div><?php endforeach; if (!$low): ?><div class="empty-state">Sem alertas de stock.</div><?php endif; ?>
    </section>
</div>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
