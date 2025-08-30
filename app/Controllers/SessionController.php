<?php
namespace App\Controllers;

use App\Core\Request;
use App\Core\Session;
use App\Core\Response;

class SessionController
{
    protected $session;

    public function __construct()
    {
        $this->session = new Session();
    }

    public function generateCsrfToken(Request $request)
    {
        $token = $this->session->generateCsrfToken();
        header('Content-Type: application/json');
        return json_encode(['csrf_token' => $token]);
    }
}
?>