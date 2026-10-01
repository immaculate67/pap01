<?php
declare(strict_types=1);
$pageTitle = 'Clientes';
require_once __DIR__ . '/includes/header.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $action = $_POST['action'] ?? '';
    if ($action === 'save') {
        $id = (int)($_POST['id'] ?? 0);
        $nome = trim((string)($_POST['nome'] ?? ''));
        $contacto = trim((string)($_POST['contacto'] ?? ''));
        $email = trim((string)($_POST['email'] ?? ''));
        $notas = trim((string)($_POST['notas'] ?? ''));

        if ($nome === '') {
            flash('danger', 'O nome do cliente é obrigatório.');
        } elseif ($email !== '' && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            flash('danger', 'O email introduzido não é válido.');
        } else {
            if ($id > 0) {
                $s = $pdo->prepare('UPDATE clientes SET nome=?, contacto=?, email=?, notas=? WHERE id=?');
                $s->execute([$nome, $contacto, $email, $notas, $id]);
                flash('success', 'Cliente atualizado.');
            } else {
                $s = $pdo->prepare('INSERT INTO clientes(nome,contacto,email,notas) VALUES(?,?,?,?)');
                $s->execute([$nome, $contacto, $email, $notas]);
                flash('success', 'Cliente criado.');
            }
        }
        redirect('clientes.php');
    }
    if ($action === 'delete') {
        $id = (int)($_POST['id'] ?? 0);
        $s = $pdo->prepare('DELETE FROM clientes WHERE id=?');
        $s->execute([$id]);
        flash('success', 'Cliente eliminado.');
        redirect('clientes.php');
    }
}

$q = trim($_GET['q'] ?? '');
if ($q !== '') { $s=$pdo->prepare('SELECT * FROM clientes WHERE nome LIKE ? OR contacto LIKE ? OR email LIKE ? ORDER BY nome'); $like="%{$q}%"; $s->execute([$like,$like,$like]); $clients=$s->fetchAll(); }
else $clients=$pdo->query('SELECT * FROM clientes ORDER BY nome')->fetchAll();
$edit = null;
if (isset($_GET['edit'])) { $s=$pdo->prepare('SELECT * FROM clientes WHERE id=?'); $s->execute([(int)$_GET['edit']]); $edit=$s->fetch() ?: null; }
?>
<div class="page-intro"><div><span class="section-tag">REGISTOS</span><p>Consultar, criar e atualizar clientes.</p></div><button class="btn btn-primary app-btn" data-bs-toggle="modal" data-bs-target="#clientModal"><i class="bi bi-plus-lg"></i> Novo cliente</button></div>
<div class="toolbar"><form class="search-box" method="get"><i class="bi bi-search"></i><input name="q" value="<?= e($q) ?>" placeholder="Pesquisar nome, contacto ou email..."><button>Pesquisar</button></form></div>
<section class="panel-card table-card"><div class="panel-head"><div><span class="panel-label">CLIENTES</span><h2><?= count($clients) ?> registos</h2></div></div><div class="table-responsive"><table class="table app-table"><thead><tr><th>Nome</th><th>Contacto</th><th>Email</th><th>Registado</th><th></th></tr></thead><tbody>
<?php foreach($clients as $c): ?><tr><td><strong><?= e($c['nome']) ?></strong></td><td><?= e($c['contacto']) ?></td><td><?= e($c['email']) ?></td><td><?= date('d/m/Y',strtotime($c['created_at'])) ?></td><td class="text-end"><a class="icon-btn" href="?edit=<?= (int)$c['id'] ?>" title="Editar"><i class="bi bi-pencil"></i></a><form class="d-inline" method="post" onsubmit="return confirm('Eliminar este cliente?')"><input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>"><input type="hidden" name="action" value="delete"><input type="hidden" name="id" value="<?= (int)$c['id'] ?>"><button class="icon-btn danger" title="Eliminar"><i class="bi bi-trash3"></i></button></form></td></tr><?php endforeach; if(!$clients): ?><tr><td colspan="5" class="empty-state">Nenhum cliente encontrado.</td></tr><?php endif; ?></tbody></table></div></section>

<div class="modal fade" id="clientModal" tabindex="-1"><div class="modal-dialog modal-dialog-centered"><div class="modal-content app-modal"><div class="modal-header"><div><span class="panel-label"><?= $edit ? 'EDITAR' : 'NOVO' ?></span><h2><?= $edit ? 'Editar cliente' : 'Criar cliente' ?></h2></div><button class="btn-close btn-close-white" data-bs-dismiss="modal"></button></div><form method="post"><div class="modal-body"><input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>"><input type="hidden" name="action" value="save"><input type="hidden" name="id" value="<?= (int)($edit['id'] ?? 0) ?>"><div class="field"><label>Nome</label><input class="form-control app-input" name="nome" value="<?= e($edit['nome'] ?? '') ?>" required></div><div class="row"><div class="col-md-6 field"><label>Contacto</label><input class="form-control app-input" name="contacto" value="<?= e($edit['contacto'] ?? '') ?>"></div><div class="col-md-6 field"><label>Email</label><input class="form-control app-input" type="email" name="email" value="<?= e($edit['email'] ?? '') ?>"></div></div><div class="field"><label>Notas</label><textarea class="form-control app-input" name="notas" rows="3"><?= e($edit['notas'] ?? '') ?></textarea></div></div><div class="modal-footer"><button type="button" class="btn btn-ghost" data-bs-dismiss="modal">Cancelar</button><button class="btn btn-primary app-btn">Guardar</button></div></form></div></div></div>
<?php if ($edit): ?><script>window.addEventListener('load',()=>new bootstrap.Modal('#clientModal').show());</script><?php endif; ?>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
