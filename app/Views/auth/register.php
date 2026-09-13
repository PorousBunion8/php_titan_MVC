<!-- tela de cadastro de novo usuario -->
<?php
/**
 * @var string|null $error
 */
?>
<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastrar Novo Usuario - Sistema de Controle de Servicos</title>
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/style.css?v=<?= time() ?>">
</head>

<body class="auth-page">

    <div class="auth-card">
        <h2>Cadastrar Novo Usuário</h2>

        <?php if (!empty($error)): ?>
            <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <form action="<?= BASE_URL ?>/register" method="POST">
            <div class="form-group">
                <input type="text" name="name" placeholder="Nome completo" required>
            </div>

            <div class="form-group">
                <input type="email" name="email" placeholder="email@email.com" required>
            </div>

            <div class="form-group">
                <input type="password" name="password" placeholder="*****************" required>
            </div>

            <div class="auth-actions">
                <button type="submit" class="btn btn-dark">Cadastrar</button>
                <a href="<?= BASE_URL ?>/login" class="auth-link">Voltar para o login</a>
            </div>
        </form>
    </div>

</body>

</html>