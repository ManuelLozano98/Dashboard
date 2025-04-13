<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

require_once "config.php";

$db = new mysqli(DB_HOST, DB_USERNAME, DB_PASSWORD, DB_NAME) or die('There was an error connecting to the database' . mysqli_errno($db));

function querySQL($sql) {
    global $db;
    $q = $db->query($sql);
    $data = [];
    while($row = mysqli_fetch_assoc($q)){
        $data[]=$row;
    }
    return $data;
}
function preparedQuerySQL($sql,$types,...$params){
    global $db;
    $stmt = $db->prepare($sql);
    $stmt->bind_param($types,...$params);
    $q = $stmt->execute();
    return $q;
}

function getDataPreparedQuerySQL($sql,$types,...$params){
    global $db;
    $stmt = $db->prepare($sql);
    $stmt->bind_param($types,...$params);
    $stmt->execute();
    $result = $stmt->get_result();
    $data = [];
    while($row = mysqli_fetch_assoc($result)){
        $data[]=$row;
    }
    return $data;
}