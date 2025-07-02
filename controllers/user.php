<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

require_once '../models/user.php';

function getUsers()
{
    $user = new User();
    $users = $user->getUsers();
    echo json_encode($users);
}

function login($query)
{
    header('Content-Type: application/json');
    if (validateFields($query)) {
        $user = new User();
        $response = $user->checkLogin($query);

    } else {
        $response = new Response();
        $response->getInvalidLoginMessage();
    }
    echo json_encode($response->buildResponse());

}

function confirmRegistration($query){
    $user = new User();
    $response = $user->verifyToken($query);
    if($response->getStatus() === 201){
        header("Location: ../email-confirmed");
    }
    else{
        header("Location: ../email-confirmed?user=not-registered");
    }
}

function save($method, $request)
{
    header('Content-Type: application/json');
    $validated = validateFields($request);
    $response = null;
    if ($validated) {
        $user = new User();
        $response = $user->insertOrEdit($request, $method);
    } else {
        $response = new Response();
        $response->getRequiredFieldsMissingMessage();
    }
    echo json_encode($response);
}

function validateFields($params)
{
    if (count($params) === 0) {
        return false;
    }
    foreach ($params as $key => $value) {
        if (empty($key)) {
            return false;
        }
    }
    return true;
}
function deleteUser($request)
{
    header('Content-Type: application/json');
    $validated = validateFields($request);
    if ($validated) {
        $user = new User();
        $response = $user->delete($request["id"]);
        echo json_encode($response);
    }
}