<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
require_once __DIR__ . '/../services/UserService.php';

class HomeController
{
    private UserService $userService;
    public function __construct()
    {
        $this->userService = new UserService();

    }


    public function index()
    {

        require VIEWS_PATH . '/login.php';
    }

    public function login()
    {
        require VIEWS_PATH . '/login.php';
    }

    public function register()
    {
        require VIEWS_PATH . '/register.php';
    }
    public function confirmEmail()
    {
        require VIEWS_PATH . '/confirm-email.php';
    }
    public function emailConfirmed()
    {
        $token = $_GET['token'] ?? null;
        $userParam = $_GET['user'] ?? null;

        if ($token) {
            $result = $this->userService->verifyToken($token);
            if ($result->getStatus() === 200) {
                require VIEWS_PATH . '/email-confirmed.php';
                return;
            }
        }
        if ($userParam === 'not-registered') {
            require VIEWS_PATH . '/email-confirmed.php';
            return;
        }
        header("Location: /AdminDashboard/email/confirm?user=not-registered");
        exit;
    }
    public function isLogged()
    {
        return !empty($_SESSION["user"]);
    }
}