<?php
// controller para gerenciar login, cadastro de novos usuarios e logout

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Session;
use App\Models\User;

//========================================================
//= Controller de autenticacao
//========================================================

class AuthController extends Controller
{
    private User $userModel;

    public function __construct()
    {
        $this->userModel = new User();
    }

    // exibe a tela de login
    public function showLogin(): void
    {
        // redireciona se ja estiver logado
        if (Session::has('user')) {
            $this->redirect('/dashboard');
        }

        $error = Session::getFlash('error');
        $success = Session::getFlash('success');

        $this->view('auth.login', [
            'error' => $error,
            'success' => $success
        ]);
    }

    // processa o login
    public function login(): void
    {
        $email = trim($_POST['email'] ?? '');
        $password = trim($_POST['password'] ?? '');

        if (empty($email) || empty($password)) {
            Session::setFlash('error', 'Ops, Email ou Senha inválido');
            $this->redirect('/login');
        }

        $user = $this->userModel->findByEmail($email);

        // valida se o usuario existe e se a senha confere
        if (!$user || !password_verify($password, $user['password'])) {
            Session::setFlash('error', 'Ops, Email ou Senha inválido');
            $this->redirect('/login');
        }

        // armazena os dados do usuario na sessao
        Session::set('user', [
            'id' => $user['id_user'],
            'name' => $user['name'],
            'email' => $user['email']
        ]);

        $this->redirect('/dashboard');
    }

    // finaliza a sessao do usuario
    public function logout(): void
    {
        Session::destroy();
        $this->redirect('/login');
    }

    // exibe o formulario de cadastro de usuario
    public function showRegister(): void
    {
        $error = Session::getFlash('error');
        $this->view('auth.register', ['error' => $error]);
    }

    // processa o cadastro de novo usuario
    public function register(): void
    {
        $name = trim($_POST['name'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $password = trim($_POST['password'] ?? '');

        if (empty($name) || empty($email) || empty($password)) {
            Session::setFlash('error', 'Preencha todos os campos obrigatorios.');
            $this->redirect('/register');
        }

        // verifica se o email ja esta em uso
        if ($this->userModel->findByEmail($email)) {
            Session::setFlash('error', 'Este email ja esta cadastrado no sistema.');
            $this->redirect('/register');
        }

        if ($this->userModel->create($name, $email, $password)) {
            Session::setFlash('success', 'Usuario cadastrado com sucesso! Efetue o login.');
            $this->redirect('/login');
        } else {
            Session::setFlash('error', 'Ocorreu um erro ao salvar o usuario.');
            $this->redirect('/register');
        }
    }
}
