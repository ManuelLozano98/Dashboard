<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of role
 *
 * @author Usuario
 */
require_once __DIR__ . '/../configurations/database.php';

class Role
{
    private ?int $id;
    private string $name;

    public function __construct($data = [])
    {
        $this->id = $data["id_role"] ?? NULL;
        $this->name = $data["name"] ?? "";
    }

    /**
     * Get the value of id
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set the value of id
     *
     * @return  self
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * Get the value of name
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set the value of name
     *
     * @return  self
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }
    public static function getAll()
    {
        $sql = "SELECT * FROM ROLES";
        $query = querySQL($sql);
        $roles = [];
        foreach ($query as $role) {
            $roles[] = new Role($role);
        }
        return $roles;
    }
    public function getCountRoles()
    {
        $sql = "SELECT COUNT(*) AS RECORDS FROM ROLES";
        return querySQL($sql);
    }
    public function getUserRolesByRole(Role $role)
    {
        $sql = "SELECT R.ID_ROLE, R.NAME FROM ROLES R, USERS_ROLES U_R WHERE U_R.ID_USER =? AND R.ID_ROLE = U_R.ID_ROLE";
        $data = getDataPreparedQuerySQL($sql, "i", $role->getId());
        if (!empty($data)) {
            return $this->asRoles($data);
        }
    }
    public function asRoles(array $array)
    {
        return array_map([$this, 'asRole'], $array);
    }
    public function asRole(array $array)
    {
        return
            new Role($array);
    }

    public static function findByName($name)
    {
        $sql = "SELECT * FROM ROLES WHERE name = ?";
        $data = getDataPreparedQuerySQL($sql, "s", $name);
        return !empty($data) ? new Role($data[0]) : false;
    }

    public static function findById($id)
    {
        $sql = "SELECT * FROM ROLES WHERE ID_ROLE=?";
        $data = getDataPreparedQuerySQL($sql, "i", $id);
        return !empty($data) ? new Role($data[0]) : false;
    }

    public static function insert(Role $role)
    {
        $sql = "INSERT INTO ROLES VALUES (?,?)";
        return preparedQuerySQLObject($sql, "is", $role, ["id", "name"]) ? $role->setId(getId()) : false;
    }
    public static function edit(Role $role)
    {
        $sql = "UPDATE ROLES SET name = ? WHERE id_role = ?";
        return preparedQuerySQLObject($sql, "si", $role, ["name", "id"]) ? $role : false;
    }

    public static function delete($id)
    {

        $sql = "DELETE FROM ROLES WHERE ID_ROLE = ?";
        return preparedQuerySQL($sql, "i", $id);
    }

    public function toArray()
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
        ];
    }
}