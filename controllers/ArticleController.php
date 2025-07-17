<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
require_once __DIR__ . '/../services/ArticleService.php';

class ArticleController
{
    private ArticleService $service;
    private Response $response;

    public function __construct()
    {
        $this->service = new ArticleService();
        $this->response = new Response();
    }

    public function index()
    {
        require VIEWS_PATH . '/articles.php';
    }

}