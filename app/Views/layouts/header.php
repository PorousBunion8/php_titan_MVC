<!-- cabecalho e barra lateral da area restrita -->
<?php
/**
 * @var array $user
 * @var string $currentDate
 */
?>
<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>JM Informatica - Sistema de Controle de Servicos</title>
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/style.css?v=<?= time() ?>">
</head>

<body>

    <div class="app-layout">
        <!-- menu lateral (sidebar) -->
        <aside class="sidebar">
            <div class="user-badge">
                <span class="user-label">Logado como:</span>
                <strong class="user-name"><?= htmlspecialchars($user['name'] ?? 'Usuario') ?></strong>
                <span class="system-date"><?= $currentDate ?? date('d/m/Y') ?></span>
            </div>

            <nav class="sidebar-nav">
                <a href="<?= BASE_URL ?>/dashboard" class="nav-item">Dashboard</a>
                <a href="<?= BASE_URL ?>/services/create" class="nav-item">Cadastrar Serviço</a>
                <a href="<?= BASE_URL ?>/logout" class="nav-item nav-logout">Sair</a>
            </nav>
        </aside>

        <!-- conteudo principal da pagina -->
        <main class="main-content"></main>