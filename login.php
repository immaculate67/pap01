<?php
declare(strict_types=1);
require_once __DIR__ . '/config/config.php';

if (is_logged_in()) redirect('dashboard.php');

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();

    $username = trim((string)($_POST['username'] ?? ''));
    $password = (string)($_POST['password'] ?? '');

    if ($username === '' || $password === '') {
        $error = 'Preenche o utilizador e a palavra-passe.';
    } else {
        $stmt = $pdo->prepare('SELECT id, nome, username, password_hash FROM utilizadores WHERE username = ? LIMIT 1');
        $stmt->execute([$username]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password_hash'])) {
            session_regenerate_id(true);
            $_SESSION['user_id'] = (int)$user['id'];
            $_SESSION['user'] = ['nome' => $user['nome'], 'username' => $user['username']];
            redirect('dashboard.php');
        }

        $error = 'Utilizador ou palavra-passe incorretos.';
    }
}
?>
<!doctype html>
<html lang="pt-PT">
<head>
<meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">
<title>Entrar · GeStock</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
<link href="assets/css/app.css" rel="stylesheet">
</head>
<body class="login-body">
<div class="login-grid"></div>
<div class="login-wrap">
    <div class="login-kicker">PROVA DE APTIDÃO PROFISSIONAL</div>
    <div class="login-brand">GeStock</div>
    <p class="login-tagline">Um sistema. Uma visão do negócio.</p>
    <div class="login-panel">
        <div class="panel-label">ACESSO AO SISTEMA</div>
        <h1>Entrar</h1>
        <p class="muted">Acede ao teu espaço de gestão.</p>
        <?php if ($error): ?><div class="alert alert-danger app-alert"><?= e($error) ?></div><?php endif; ?>
        <form method="post" autocomplete="off">
            <input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>">
            <div class="field"><label>Utilizador</label><input class="form-control app-input" name="username" required autofocus></div>
            <div class="field"><label>Palavra-passe</label><input class="form-control app-input" type="password" name="password" required></div>
            <button class="btn btn-primary app-btn w-100" type="submit">Entrar <i class="bi bi-arrow-right"></i></button>
        </form>
        <div class="demo-note">Demo inicial: <strong>admin</strong> / <strong>admin123</strong></div>
    </div>
    <div class="login-footer">LAKSHIT BATTAN • 2025/2026</div>
</div>
</body>
</html>
