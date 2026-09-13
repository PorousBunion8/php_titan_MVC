<!-- tela de edicao de servico existente -->
<?php
/**
 * @var array $service
 * @var string|null $error
 */
require VIEW_PATH . '/layouts/header.php';
?>

<div class="service-form-container">
    <div class="auth-card">
        <h2>Alterar Serviço</h2>

        <?php if (!empty($error)): ?>
            <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <form action="<?= BASE_URL ?>/services/edit" method="POST">
            <input type="hidden" name="id_service" value="<?= $service['id_service'] ?>">

            <div class="form-group">
                <input type="text" name="description" placeholder="descrição" value="<?= htmlspecialchars($service['description']) ?>" required autofocus>
            </div>

            <div class="form-group">
                <input type="text" name="price" id="price_input" placeholder="preço" value="<?= number_format((float) $service['price'], 2, ',', '.') ?>" required>
            </div>

            <div class="auth-actions">
                <button type="submit" class="btn btn-dark">Salvar Alterações</button>
                <a href="<?= BASE_URL ?>/dashboard" class="auth-link">Voltar para a Dashboard</a>
            </div>
        </form>
    </div>
</div>

<?php require VIEW_PATH . '/layouts/footer.php'; ?>