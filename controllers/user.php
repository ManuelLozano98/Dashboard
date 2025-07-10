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
    $users = $user->getAll();
    echo json_encode($users);
}
function getDocumentTypes()
{
    require '../models/document_type.php';
    $document = new Document_Type();
    echo json_encode($document->getAll());
}

function login($query)
{
    header('Content-Type: application/json');
    if (validateFields($query)) {
        $user = new User();
        $response = $user->checkLogin($query);
        if ($response->getMessage() === "Successful login") {
            session_start();
            $_SESSION["user"] = $user->getUserCreatedInfo();
        }
    } else {
        $response = new Response();
        $response->getInvalidLoginMessage();
    }
    echo json_encode($response->buildResponse());

}

function confirmRegistration($query)
{
    $user = new User();
    $response = $user->verifyToken($query);
    if ($response->getStatus() === 201) {
        header("Location: ../email-confirmed");
    } else {
        header("Location: ../email-confirmed?user=not-registered");
    }
}

function sendEmailVerification($email)
{
    $response = new Response();
    $user = new User();
    $data = $user->findByEmail($email);
    $userDb = new User($data);
    if (Mail::sendEmailVerification($userDb->getEmail(), $userDb->getUsername(), $userDb->getToken())) {
        $response->getEmailVerificationSuccessfullyMessage();
    } else {
        $response->getEmailVerificationErrorMessage();
    }
    echo json_encode($response->buildResponse());

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
    echo json_encode($response->buildResponse());
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