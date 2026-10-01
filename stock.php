<?php
declare(strict_types=1);
$pageTitle='Stock'; require_once __DIR__.'/includes/header.php';
$products=$pdo->query('SELECT id,nome,preco,stock FROM produtos ORDER BY stock ASC,nome ASC')->fetchAll();$low=count(array_filter($products,fn($p)=>(int)$p['stock']<=STOCK_LOW_THRESHOLD));
?>
<div class="page-intro"><div><span class="section-tag">ESTADO</span><p>Quantidades atuais e alertas de stock baixo.</p></div><span class="status-pill low"><?= $low ?> alertas</span></div>
<section class="panel-card table-card"><div class="panel-head"><div><span class="panel-label">STOCK</span><h2>Visão operacional</h2></div></div><div class="table-responsive"><table class="table app-table"><thead><tr><th>Produto</th><th>Preço</th><th>Qtd.</th><th>Estado</th><th></th></tr></thead><tbody><?php foreach($products as $p): $s=(int)$p['stock']; ?><tr><td><strong><?= e($p['nome']) ?></strong></td><td>€ <?= number_format((float)$p['preco'],2,',',' ') ?></td><td><strong><?= $s ?></strong></td><td><span class="status-pill <?= $s<=3?'critical':($s<=STOCK_LOW_THRESHOLD?'low':'ok') ?>"><?= $s<=3?'CRÍTICO':($s<=STOCK_LOW_THRESHOLD?'BAIXO':'OK') ?></span></td><td class="text-end"><a href="produto_form.php?edit=<?= (int)$p['id'] ?>" class="icon-btn"><i class="bi bi-pencil"></i></a></td></tr><?php endforeach; if(!$products): ?><tr><td colspan="5" class="empty-state">Não existem produtos.</td></tr><?php endif; ?></tbody></table></div></section>
<?php require_once __DIR__.'/includes/footer.php'; ?>
