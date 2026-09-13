<!-- tela de login de usuarios -->
<?php
/**
 * @var string|null $error
 * @var string|null $success
 */
?>
<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Sistema de Controle de Servicos</title>
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/style.css?v=<?= time() ?>">
</head>

<body class="auth-page">

    <div class="auth-card">
        <h2>Sistema de Controle de Serviços</h2>

        <?php if (!empty($error)): ?>
            <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <?php if (!empty($success)): ?>
            <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
        <?php endif; ?>

        <form action="<?= BASE_URL ?>/login" method="POST">
            <div class="form-group">
                <input type="email" name="email" placeholder="email@email.com" required autocomplete="email">
            </div>

            <div class="form-group">
                <input type="password" name="password" placeholder="******************" required autocomplete="current-password">
            </div>

            <div class="auth-actions">
                <button type="submit" class="btn btn-dark">Entrar</button>
                <a href="<?= BASE_URL ?>/register" class="auth-link">Cadastrar usuário</a>
            </div>
        </form>
    </div>

</body>

</html>