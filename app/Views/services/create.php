<!-- tela de cadastro de novo servico -->
<?php
/**
 * @var string|null $error
 */
require VIEW_PATH . '/layouts/header.php';
?>

<div class="service-form-container">
    <div class="auth-card">
        <h2>Cadastrar Novo Serviço</h2>

        <?php if (!empty($error)): ?>
            <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <form action="<?= BASE_URL ?>/services/create" method="POST">
            <div class="form-group">
                <input type="text" name="description" placeholder="descrição" required autofocus>
            </div>

            <div class="form-group">
                <input type="text" name="price" id="price_input" placeholder="preço (ex: 150,00)" required>
            </div>

            <div class="auth-actions">
                <button type="submit" class="btn btn-dark">Cadastrar</button>
                <a href="<?= BASE_URL ?>/dashboard" class="auth-link">Voltar para a Dashboard</a>
            </div>
        </form>
    </div>
</div>

<?php require VIEW_PATH . '/layouts/footer.php'; ?>