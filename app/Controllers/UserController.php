<?php
namespace App\Controllers;

use App\Core\Request;
use App\Core\Response;
use App\Core\Session;
use App\Core\Theme;
use App\Models\User;
use App\Models\Order;

class UserController
{
    private $theme;
    private $session;

    public function __construct()
    {
        $this->theme = new Theme();
        $this->session = new Session();
    }

    public function login(Request $request)
    {
        if ($request->isPost() && $this->session->verifyCsrfToken($request->getPost('csrf_token'))) {
            $username = $request->getPost('username');
            $password = $request->getPost('password');
            $userModel = new User();
            $user = $userModel->findByUsername($username);
            if ($user && password_verify($password, $user['password'])) {
                $this->session->set('user_id', $user['id']);
                $this->session->set('is_admin', $user['is_admin']);
                Response::redirectNamed('home');
            }
        }
        return $this->theme->loadTemplate('user/login');
    }

    public function register(Request $request)
    {
        if ($request->isPost() && $this->session->verifyCsrfToken($request->getPost('csrf_token'))) {
            $username = $request->getPost('username');
            $password = password_hash($request->getPost('password'), PASSWORD_DEFAULT);
            $userModel = new User();
            $userModel->create([
                'username' => $username,
                'password' => $password
            ]);
            Response::redirectNamed('user.login');
        }
        return $this->theme->loadTemplate('user/register');
    }

    public function logout(Request $request)
    {
        $this->session->destroy();
        Response::redirectNamed('home');
    }

    public function profile(Request $request)
    {
        if (!$this->session->get('user_id')) {
            Response::redirectNamed('user.login');
        }
        $userModel = new User();
        $orderModel = new Order();
        $user = $userModel->find($this->session->get('user_id'));
        $orders = $orderModel->getUserOrders($this->session->get('user_id'));
        return $this->theme->loadTemplate('user/profile', [
            'user' => $user,
            'orders' => $orders
        ]);
    }
}
?>