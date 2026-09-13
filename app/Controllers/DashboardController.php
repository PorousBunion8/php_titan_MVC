<?php
// controller para gerenciar o painel principal com metricas e filtros de servicos

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Session;
use App\Models\ServiceOrder;
use App\Models\User;

//========================================================
//= Controller da dashboard principal
//========================================================

class DashboardController extends Controller
{
    private ServiceOrder $serviceModel;
    private User $userModel;

    public function __construct()
    {
        // protege a rota para usuarios logados
        if (!Session::has('user')) {
            $this->redirect('/login');
        }

        $this->serviceModel = new ServiceOrder();
        $this->userModel = new User();
    }

    // exibe o painel principal com totais, servicos pendentes e tabela filtrada
    public function index(): void
    {
        $user = Session::get('user');
        $userId = (int) $user['id'];

        // captura parametros de busca
        $filters = [
            'description' => trim($_GET['description'] ?? ''),
            'user_name'   => trim($_GET['user_name'] ?? ''),
            'status'      => trim($_GET['status'] ?? ''),
            'date_start'  => trim($_GET['date_start'] ?? ''),
            'date_end'    => trim($_GET['date_end'] ?? '')
        ];

        // busca dados do banco
        $totalUserServices = $this->serviceModel->getTotalByUser($userId);
        $pendingServices   = $this->serviceModel->getPendingByUser($userId, 5);
        $latestServices    = $this->serviceModel->getLatestServices(3);
        $services          = $this->serviceModel->getFilteredServices($filters);
        $activeUsers       = $this->userModel->getAllActive();

        $flashSuccess = Session::getFlash('success');
        $flashError   = Session::getFlash('error');

        $this->view('dashboard.index', [
            'user'              => $user,
            'currentDate'       => date('d/m/Y'),
            'totalUserServices' => $totalUserServices,
            'pendingServices'   => $pendingServices,
            'latestServices'    => $latestServices,
            'services'          => $services,
            'activeUsers'       => $activeUsers,
            'filters'           => $filters,
            'success'           => $flashSuccess,
            'error'             => $flashError
        ]);
    }
}
