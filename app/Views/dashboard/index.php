<!-- tela principal da dashboard com metricas e listagem de servicos -->
<?php
/**
 * @var array $user
 * @var string $currentDate
 * @var float $totalUserServices
 * @var array $pendingServices
 * @var array $latestServices
 * @var array $services
 * @var array $filters
 * @var string|null $success
 * @var string|null $error
 */
require VIEW_PATH . '/layouts/header.php';
?>

<div class="dashboard-container">
    <h1 class="page-title">DASHBOARD</h1>

    <?php if (!empty($success)): ?>
        <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
    <?php endif; ?>

    <?php if (!empty($error)): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <!-- cards superiores de metricas conforme o wireframe -->
    <div class="metrics-grid">
        <!-- total do usuario logado -->
        <div class="card metric-card">
            <h3>Total dos Serviços (Seu)</h3>
            <p class="metric-total">R$ <?= number_format($totalUserServices ?? 0, 2, ',', '.') ?></p>
        </div>

        <!-- ultimos servicos gerais -->
        <div class="card">
            <h3>Ultimos Serviços</h3>
            <ul class="mini-list">
                <?php if (!empty($latestServices)): ?>
                    <?php foreach ($latestServices as $ls): ?>
                        <li><?= $ls['id_service'] ?> - <?= htmlspecialchars($ls['description']) ?></li>
                    <?php endforeach; ?>
                <?php else: ?>
                    <li class="empty-text">Nenhum serviço registrado.</li>
                <?php endif; ?>
            </ul>
        </div>

        <!-- ultimos pendentes do usuario logado -->
        <div class="card">
            <h3>Serviços Pendentes</h3>
            <ul class="mini-list">
                <?php if (!empty($pendingServices)): ?>
                    <?php foreach ($pendingServices as $ps): ?>
                        <li><?= $ps['id_service'] ?> - <?= htmlspecialchars($ps['description']) ?></li>
                    <?php endforeach; ?>
                <?php else: ?>
                    <li class="empty-text">Nenhum serviço pendente.</li>
                <?php endif; ?>
            </ul>
        </div>
    </div>

    <!-- formulario de filtros de busca -->
    <form action="<?= BASE_URL ?>/dashboard" method="GET" class="filter-bar">
        <div class="filter-group">
            <label class="filter-label" for="filter_description">Serviço</label>
            <input type="text" id="filter_description" name="description" placeholder="Nome do serviço" list="services_list" value="<?= htmlspecialchars($filters['description'] ?? '') ?>" autocomplete="off">
            <datalist id="services_list">
                <?php if (!empty($services)): ?>
                    <?php
                    $uniqueDesc = array_unique(array_column($services, 'description'));
                    foreach ($uniqueDesc as $desc):
                    ?>
                        <option value="<?= htmlspecialchars($desc) ?>"></option>
                    <?php endforeach; ?>
                <?php endif; ?>
            </datalist>
        </div>

        <div class="filter-group">
            <label class="filter-label" for="filter_user_name">Usuário</label>
            <input type="text" id="filter_user_name" name="user_name" placeholder="Nome do usuário" list="users_list" value="<?= htmlspecialchars($filters['user_name'] ?? '') ?>" autocomplete="off">
            <datalist id="users_list">
                <?php if (!empty($activeUsers)): ?>
                    <?php foreach ($activeUsers as $u): ?>
                        <option value="<?= htmlspecialchars($u['name']) ?>"></option>
                    <?php endforeach; ?>
                <?php endif; ?>
            </datalist>
        </div>

        <div class="filter-group">
            <label class="filter-label" for="filter_status">Status</label>
            <select id="filter_status" name="status">
                <option value="">Status (Todos)</option>
                <option value="Pendente" <?= ($filters['status'] ?? '') === 'Pendente' ? 'selected' : '' ?>>Pendente</option>
                <option value="Finalizado" <?= ($filters['status'] ?? '') === 'Finalizado' ? 'selected' : '' ?>>Finalizado</option>
            </select>
        </div>

        <div class="filter-group">
            <label class="filter-label" for="filter_date_start">Data Inicial</label>
            <input type="date" id="filter_date_start" name="date_start" value="<?= htmlspecialchars($filters['date_start'] ?? '') ?>">
        </div>

        <div class="filter-group">
            <label class="filter-label" for="filter_date_end">Data Final</label>
            <input type="date" id="filter_date_end" name="date_end" value="<?= htmlspecialchars($filters['date_end'] ?? '') ?>">
        </div>

        <div class="filter-actions">
            <button type="submit" class="btn btn-dark">Filtrar</button>
            <a href="<?= BASE_URL ?>/dashboard" class="btn btn-secondary">Limpar</a>
        </div>
    </form>

    <!-- tabela principal de ordens de servico -->
    <div class="table-responsive">
        <table class="services-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>DESCRIÇÃO</th>
                    <th>VALOR</th>
                    <th>COMISSÃO</th>
                    <th>STATUS</th>
                    <th>USUÁRIO</th>
                    <th class="text-center">AÇÕES</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($services)): ?>
                    <?php foreach ($services as $srv): ?>
                        <?php
                        $isFinished = !empty($srv['finished_at']);
                        $statusLabel = $isFinished ? 'FINALIZADO' : 'PENDENTE';
                        $statusClass = $isFinished ? 'badge-finished' : 'badge-pending';
                        ?>
                        <tr>
                            <td><?= $srv['id_service'] ?></td>
                            <td><?= htmlspecialchars($srv['description']) ?></td>
                            <td>R$ <?= number_format((float) $srv['price'], 2, ',', '.') ?></td>
                            <td>
                                <?php if ($isFinished && $srv['commission_user'] !== null): ?>
                                    R$ <?= number_format((float) $srv['commission_user'], 2, ',', '.') ?>
                                <?php else: ?>
                                    -
                                <?php endif; ?>
                            </td>
                            <td><span class="badge <?= $statusClass ?>"><?= $statusLabel ?></span></td>
                            <td><?= htmlspecialchars($srv['user_name']) ?></td>
                            <td class="table-actions">
                                <a href="<?= BASE_URL ?>/services/edit?id=<?= $srv['id_service'] ?>" class="btn-action btn-edit" title="Alterar">Alterar</a>

                                <form action="<?= BASE_URL ?>/services/delete" method="POST" class="inline-form" onsubmit="return confirm('Deseja realmente excluir este servico?');">
                                    <input type="hidden" name="id_service" value="<?= $srv['id_service'] ?>">
                                    <button type="submit" class="btn-action btn-delete" title="Excluir">Excluir</button>
                                </form>

                                <?php if (!$isFinished): ?>
                                    <form action="<?= BASE_URL ?>/services/finish" method="POST" class="inline-form" onsubmit="return confirm('Confirmar a finalizacao deste servico e envio de e-mail?');">
                                        <input type="hidden" name="id_service" value="<?= $srv['id_service'] ?>">
                                        <button type="submit" class="btn-action btn-finish" title="Finalizar serviço">Finalizar</button>
                                    </form>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="7" class="text-center empty-table">Nenhum serviço encontrado com os filtros selecionados.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require VIEW_PATH . '/layouts/footer.php'; ?>