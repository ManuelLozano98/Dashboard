<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */


class HomeController
{
    public function __construct()
    {

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
        require VIEWS_PATH . '/email-confirmed.php';
    }


}