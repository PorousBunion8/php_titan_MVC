<?php
// controller para manipulacao de criacao, edicao, exclusao e finalizacao de servicos

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Session;
use App\Core\Mailer;
use App\Models\ServiceOrder;

//========================================================
//= Controller de manutencao de servicos
//========================================================

class ServiceController extends Controller
{
    private ServiceOrder $serviceModel;

    public function __construct()
    {
        if (!Session::has('user')) {
            $this->redirect('/login');
        }

        $this->serviceModel = new ServiceOrder();
    }

    // exibe tela de cadastro de novo servico
    public function create(): void
    {
        $user = Session::get('user');
        $error = Session::getFlash('error');

        $this->view('services.create', [
            'user'        => $user,
            'currentDate' => date('d/m/Y'),
            'error'       => $error
        ]);
    }

    // grava o novo servico
    public function store(): void
    {
        $description = trim($_POST['description'] ?? '');
        $priceRaw    = trim($_POST['price'] ?? '');

        // normaliza valor monetario vindo do formulario
        $price = $this->parsePrice($priceRaw);

        if (empty($description) || $price <= 0) {
            Session::setFlash('error', 'Preencha todos os campos obrigatorios para cadastrar o servico.');
            $this->redirect('/dashboard');
        }

        if ($price > 999999999.99) {
            Session::setFlash('error', 'O valor informado e muito alto. O limite maximo e R$ 999.999.999,99.');
            $this->redirect('/dashboard');
        }

        $user = Session::get('user');
        $userId = (int) $user['id'];

        if ($this->serviceModel->create($description, $price, $userId)) {
            Session::setFlash('success', 'Servico cadastrado com sucesso com status Pendente.');
        } else {
            Session::setFlash('error', 'Falha ao cadastrar o novo servico.');
        }

        $this->redirect('/dashboard');
    }

    // exibe tela de edicao de servico
    public function edit(): void
    {
        $id = (int) ($_GET['id'] ?? 0);
        $service = $this->serviceModel->findById($id);

        if (!$service) {
            Session::setFlash('error', 'Servico nao encontrado.');
            $this->redirect('/dashboard');
        }

        $user = Session::get('user');
        $error = Session::getFlash('error');

        $this->view('services.edit', [
            'user'        => $user,
            'currentDate' => date('d/m/Y'),
            'service'     => $service,
            'error'       => $error
        ]);
    }

    // processa a atualizacao do servico
    public function update(): void
    {
        $id          = (int) ($_POST['id_service'] ?? 0);
        $description = trim($_POST['description'] ?? '');
        $priceRaw    = trim($_POST['price'] ?? '');

        $price = $this->parsePrice($priceRaw);

        if ($id <= 0 || empty($description) || $price <= 0) {
            Session::setFlash('error', 'Preencha todos os campos corretamente.');
            $this->redirect('/services/edit?id=' . $id);
        }

        if ($price > 999999999.99) {
            Session::setFlash('error', 'O valor informado e muito alto. O limite maximo e R$ 999.999.999,99.');
            $this->redirect('/services/edit?id=' . $id);
        }

        if ($this->serviceModel->update($id, $description, $price)) {
            Session::setFlash('success', 'Servico atualizado com sucesso.');
            $this->redirect('/dashboard');
        } else {
            Session::setFlash('error', 'Falha ao atualizar o servico.');
            $this->redirect('/services/edit?id=' . $id);
        }
    }

    // remove um servico do sistema
    public function delete(): void
    {
        $id = (int) ($_POST['id_service'] ?? 0);

        if ($id > 0 && $this->serviceModel->delete($id)) {
            Session::setFlash('success', 'Servico excluido com sucesso.');
        } else {
            Session::setFlash('error', 'Nao foi possivel excluir o servico.');
        }

        $this->redirect('/dashboard');
    }

    // finaliza o servico, grava comissao e envia notificacao por e-mail
    public function finish(): void
    {
        $id = (int) ($_POST['id_service'] ?? 0);
        $service = $this->serviceModel->findById($id);

        if (!$service) {
            Session::setFlash('error', 'Servico nao localizado.');
            $this->redirect('/dashboard');
        }

        if (!empty($service['finished_at'])) {
            Session::setFlash('error', 'Este servico ja foi finalizado.');
            $this->redirect('/dashboard');
        }

        if ($this->serviceModel->finish($id)) {
            $commission = $this->serviceModel->calculateCommission((float) $service['price']);

            // dispara notificacao por e-mail para o tecnico
            Mailer::sendServiceFinishedEmail(
                $service['user_email'],
                $service['user_name'],
                $service['description'],
                (float) $service['price'],
                $commission
            );

            Session::setFlash('success', 'Servico finalizado com sucesso! Comissao calculada e e-mail enviado.');
        } else {
            Session::setFlash('error', 'Falha ao finalizar o servico.');
        }

        $this->redirect('/dashboard');
    }

    // converte formato monetario brasileiro ou americano para float
    private function parsePrice(string $priceStr): float
    {
        $priceStr = str_replace('R$', '', $priceStr);
        $priceStr = trim($priceStr);

        if (strpos($priceStr, ',') !== false) {
            $priceStr = str_replace('.', '', $priceStr);
            $priceStr = str_replace(',', '.', $priceStr);
        }

        return (float) $priceStr;
    }
}
