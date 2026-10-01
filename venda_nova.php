<?php
declare(strict_types=1);
$pageTitle='Nova venda'; require_once __DIR__.'/includes/header.php';
$clientes=$pdo->query('SELECT id,nome FROM clientes ORDER BY nome')->fetchAll();
$produtos=$pdo->query('SELECT id,nome,preco,stock FROM produtos ORDER BY nome')->fetchAll();
if($_SERVER['REQUEST_METHOD']==='POST'){
 verify_csrf();
 $clienteId=(int)($_POST['cliente_id']??0);
 $ids=$_POST['produto_id']??[];
 $qtys=$_POST['quantidade']??[];
 $items=[];
 foreach($ids as $i=>$pid){
   $pid=(int)($pid ?? 0);
   $qty=(int)($qtys[$i] ?? 0);
   if($pid > 0 && $qty > 0){
     $key = $pid;
     if(!isset($items[$key])) $items[$key] = ['id'=>$pid,'qty'=>0];
     $items[$key]['qty'] += $qty;
   }
 }

 if($clienteId <= 0){ flash('danger','Seleciona um cliente válido antes de guardar a venda.'); redirect('venda_nova.php'); }
 if(!$items){ flash('danger','Adiciona pelo menos um item com quantidade válida.'); redirect('venda_nova.php'); }

 try{
   $pdo->beginTransaction();
   $check=$pdo->prepare('SELECT id,nome,preco,stock FROM produtos WHERE id=? FOR UPDATE');
   $insert=$pdo->prepare('INSERT INTO vendas(cliente_id,total) VALUES(?,0)');
   $insert->execute([$clienteId]);
   $saleId=(int)$pdo->lastInsertId();
   $itemInsert=$pdo->prepare('INSERT INTO itens_venda(venda_id,produto_id,quantidade,preco_unitario,subtotal) VALUES(?,?,?,?,?)');
   $stockUpdate=$pdo->prepare('UPDATE produtos SET stock=stock-? WHERE id=?');
   $total=0.0;

   foreach($items as $it){
     $check->execute([$it['id']]);
     $p=$check->fetch();
     if(!$p) throw new RuntimeException('Produto inválido.');
     if((int)$it['qty'] > (int)$p['stock']) throw new RuntimeException('Stock insuficiente para ' . $p['nome'] . '.');
     $sub=(float)$it['qty'] * (float)$p['preco'];
     $total += $sub;
     $itemInsert->execute([$saleId, $p['id'], (int)$it['qty'], $p['preco'], $sub]);
     $stockUpdate->execute([(int)$it['qty'], $p['id']]);
   }

   $u=$pdo->prepare('UPDATE vendas SET total=? WHERE id=?');
   $u->execute([$total, $saleId]);
   $pdo->commit();
   flash('success','Venda #'.$saleId.' registada. Stock atualizado.');
   redirect('venda_view.php?id='.$saleId);
 }catch(Throwable $e){
   if($pdo->inTransaction()) $pdo->rollBack();
   flash('danger', $e->getMessage());
   redirect('venda_nova.php');
 }
}
?>
<div class="page-intro"><div><span class="section-tag">VENDAS / NOVA</span><p>Seleciona o cliente, adiciona os itens e guarda.</p></div><a href="vendas.php" class="btn btn-ghost">Voltar</a></div>
<section class="panel-card form-card"><form method="post" id="saleForm"><input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>"><div class="field"><label>Cliente</label><select class="form-select app-input" name="cliente_id" required><option value="0">Cliente não identificado</option><?php foreach($clientes as $c): ?><option value="<?= (int)$c['id'] ?>"><?= e($c['nome']) ?></option><?php endforeach; ?></select></div><div class="panel-divider"></div><div class="panel-head"><div><span class="panel-label">ITENS</span><h2>Produtos da venda</h2></div><button type="button" class="btn btn-ghost" onclick="addSaleRow()"><i class="bi bi-plus"></i> Adicionar item</button></div><div id="saleItems"></div><div class="sale-total"><span>TOTAL</span><strong id="saleTotal">€ 0,00</strong></div><div class="form-actions"><a href="vendas.php" class="btn btn-ghost">Cancelar</a><button class="btn btn-primary app-btn">Guardar venda <i class="bi bi-check2"></i></button></div></form></section>
<template id="saleRowTemplate"><div class="sale-row"><select class="form-select app-input product-select" name="produto_id[]" onchange="updateSaleTotal()" required><option value="">Selecionar produto</option><?php foreach($produtos as $p): ?><option value="<?= (int)$p['id'] ?>" data-price="<?= e((string)$p['preco']) ?>" data-stock="<?= (int)$p['stock'] ?>"><?= e($p['nome']) ?> — € <?= number_format((float)$p['preco'],2,',',' ') ?> (<?= (int)$p['stock'] ?> un.)</option><?php endforeach; ?></select><input class="form-control app-input qty-input" name="quantidade[]" type="number" min="1" value="1" oninput="updateSaleTotal()" required><button type="button" class="icon-btn danger" onclick="this.closest('.sale-row').remove();updateSaleTotal()"><i class="bi bi-x-lg"></i></button></div></template>
<script>document.addEventListener('DOMContentLoaded',()=>addSaleRow());</script>
<?php require_once __DIR__.'/includes/footer.php'; ?>
