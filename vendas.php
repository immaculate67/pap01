<?php
declare(strict_types=1);
$pageTitle='Vendas'; require_once __DIR__.'/includes/header.php';
$vendas=$pdo->query('SELECT v.*, c.nome cliente FROM vendas v LEFT JOIN clientes c ON c.id=v.cliente_id ORDER BY v.id DESC')->fetchAll();
?>
<div class="page-intro"><div><span class="section-tag">OPERAÇÕES</span><p>Registar operações e consultar o histórico.</p></div><a href="venda_nova.php" class="btn btn-primary app-btn"><i class="bi bi-plus-lg"></i> Registar venda</a></div>
<section class="panel-card table-card"><div class="panel-head"><div><span class="panel-label">HISTÓRICO</span><h2><?= count($vendas) ?> vendas</h2></div></div><div class="table-responsive"><table class="table app-table"><thead><tr><th>Venda</th><th>Cliente</th><th>Data</th><th>Total</th><th></th></tr></thead><tbody><?php foreach($vendas as $v): ?><tr><td><strong>#<?= (int)$v['id'] ?></strong></td><td><?= e($v['cliente']?:'Cliente não identificado') ?></td><td><?= date('d/m/Y H:i',strtotime($v['data_venda'])) ?></td><td><strong>€ <?= number_format((float)$v['total'],2,',',' ') ?></strong></td><td class="text-end"><a class="icon-btn" href="venda_view.php?id=<?= (int)$v['id'] ?>"><i class="bi bi-arrow-up-right"></i></a></td></tr><?php endforeach; if(!$vendas): ?><tr><td colspan="5" class="empty-state">Ainda não existem vendas.</td></tr><?php endif; ?></tbody></table></div></section>
<?php require_once __DIR__.'/includes/footer.php'; ?>
