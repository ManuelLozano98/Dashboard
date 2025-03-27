<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Category
 *
 * @author Usuario
 */
require_once '../configurations/database.php';

class Category {

    public function insert($name, $description) {
        $sql = "INSERT INTO CATEGORIES (name,description,active) VALUES (?,?,'1')";
        return preparedQuerySQL($sql, "ss", $name, $description);
    }

    public function edit($id, $name, $description) {
        $sql = "UPDATE CATEGORIES SET NAME=?, description=? WHERE idcategory=?";
        return preparedQuerySQL($sql, "ssi", $name, $description, $id);
    }

    public function disable($id) {
        $sql = "UPDATE CATEGORIES SET ACTIVE='0' WHERE IDCATEGORY=?";
        return preparedQuerySQL($sql, "i", $id);
    }

    public function enable($id) {
        $sql = "UPDATE CATEGORIES SET ACTIVE='1' WHERE IDCATEGORY=?";
        return preparedQuerySQL($sql, "i", $id);
    }

    public function getCategory($id) {
        $sql = "SELECT * FROM CATEGORIES WHERE IDCATEGORY=?";
        return preparedQuerySQL($sql, "i", $id);
    }

    public function getCategories() {
        $sql = "SELECT * FROM CATEGORIES";
        return querySQL($sql);
    }

    public function getCountCategories() {
        $sql = "SELECT COUNT(*) FROM CATEGORIES";
        return querySQL($sql);
    }

}
