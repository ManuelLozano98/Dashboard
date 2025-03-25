<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

require_once "config.php";

$db = new mysqli(DB_HOST, DB_USERNAME, DB_PASSWORD, DB_NAME);

function querySQL($sql) {
    global $db;
    $q = $db->query($sql);
    $db->close();
    return $q;
}

$db->close();
