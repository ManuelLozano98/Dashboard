<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */


class UserController
{

    public function index()
    {
        require VIEWS_PATH . '/index.php';
    }
    public function listUsers()
    {
        require VIEWS_PATH . '/users.php';
    }
    public function logout()
    {
        require VIEWS_PATH . '/logout.php';
    }


}