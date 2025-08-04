<?php

require_once __DIR__ . '/User.php';
require_once __DIR__ . '/Role.php';
class UsersRoles
{
    private ?int $id;
    private ?int $roleId;
    private ?int $userId;


    public function __construct($data = [])
    {
        $this->id = $data["id_user_role"] ?? NULL;
        $this->roleId = $data["id_role"] ?? NULL;
        $this->userId = $data["id_user"] ?? NULL;

    }

    public static function getUsersByRoleName($roleName)
    {
        $sql = "SELECT id_user, username FROM USERS WHERE id_user IN (
SELECT U_R.id_user FROM USERS_ROLES U_R WHERE U_R.id_role = (
    SELECT id_role FROM ROLES WHERE NAME = '?'))";
        $data = getDataPreparedQuerySQL($sql, "s", $roleName);
        $users_roles = [];
        foreach ($data as $user_role) {
            $users_roles[] = new UsersRoles($user_role);
        }
        return $users_roles;

    }

    public static function getUsersByRole(Role $role)
    {
        $sql = "SELECT id_user, username FROM USERS WHERE id_user IN (
SELECT U_R.id_user FROM USERS_ROLES U_R WHERE U_R.id_role = ?)";
        $data = getDataPreparedQuerySQL($sql, "i", $role->getId());
        $users_roles = [];
        foreach ($data as $user_role) {
            $users_roles[] = new User($user_role);
        }
        return $users_roles;

    }

    public static function getUserswithRoles()
    {
        $sql = "SELECT * FROM USERS_ROLES";
        $data = querySQL($sql);
        $users_roles = [];
        foreach ($data as $user_role) {
            $users_roles[] = new UsersRoles($user_role);
        }
        return $users_roles;
    }
    public static function getRolesByUser(User $user)
    {
        $id = $user->getId();
        $sql = "SELECT U_R.*, u.username, r.name FROM USERS_ROLES U_R, USERS U, ROLES R WHERE R.ID_ROLE = U_R.ID_ROLE AND U_R.ID_USER = ? AND U.ID_USER = ?";
        $data = getDataPreparedQuerySQL($sql, "ii", $id, $id);
        $roles = [];
        foreach ($data as $user_role) {
            $roles[] = $user_role;
        }
        return $roles;
    }

    public static function findByUserIdAndRoleId($userId, $roleId)
    {
        $sql = "SELECT * FROM USERS_ROLES WHERE id_role=? AND id_user=?";
        $data = getDataPreparedQuerySQL($sql, "ii", $roleId, $userId);
        $users_roles = [];
        foreach ($data as $user_role) {
            $users_roles[] = new UsersRoles($user_role);
        }
        return $users_roles;
    }

    public static function findRolesById($id)
    {
        $sql = "SELECT * FROM USERS_ROLES WHERE id_role=?";
        $data = getDataPreparedQuerySQL($sql, "i", $id);
        $users_roles = [];
        foreach ($data as $user_role) {
            $users_roles[] = new UsersRoles($user_role);
        }
        return $users_roles;
    }

    public static function findByUserId($id)
    {
        $sql = "SELECT * FROM USERS_ROLES WHERE id_user=?";
        $data = getDataPreparedQuerySQL($sql, "i", $id);
        return !empty($data) ? new UsersRoles($data[0]) : false;

    }

    public static function findByRoleId($id)
    {
        $sql = "SELECT * FROM USERS_ROLES WHERE id_role=?";
        $data = getDataPreparedQuerySQL($sql, "i", $id);
        return !empty($data) ? new UsersRoles($data[0]) : false;

    }
    public static function getUsersRolesById($id)
    {
        $sql = "SELECT * FROM USERS_ROLES WHERE id_user_role=?";
        $data = getDataPreparedQuerySQL($sql, "i", $id);
        return !empty($data) ? new UsersRoles($data[0]) : false;
    }

    public static function insert(UsersRoles $usersRoles)
    {
        $sql = "INSERT INTO USERS_ROLES VALUES (?,?,?)";
        return preparedQuerySQLObject($sql, "iii", $usersRoles, ["id", "user_id", "role_id"]) ? $usersRoles->setId(getId()) : false;
    }
    public static function delete(UsersRoles $usersRoles)
    {
        $sql = "DELETE FROM USERS_ROLES WHERE id_user_role = ?";
        return preparedQuerySQL($sql, "i", $usersRoles->getId());
    }

    public static function hasRole(User $user, $rolename)
    {
        $sql = "SELECT id_user_role FROM users_roles WHERE id_user = ? AND id_role = (select id_role from roles where name = ?)";
        $data = getDataPreparedQuerySQL($sql, "is", $user->getId(), $rolename);
        return !empty($data);
    }
    public static function isAdmin(User $user)
    {
        // return self::hasRole($user,"admin"); 
        $sql = "SELECT id_user_role FROM users_roles WHERE id_user = ? AND id_role = (select id_role from roles where name = ?)";
        $data = getDataPreparedQuerySQL($sql, "is", $user->getId(), "admin");
        return !empty($data);
    }

    /**
     * Get the value of userId
     */
    public function getUserId()
    {
        return $this->userId;
    }

    /**
     * Set the value of userId
     *
     * @return  self
     */
    public function setUserId($userId)
    {
        $this->userId = $userId;

        return $this;
    }

    /**
     * Get the value of roleId
     */
    public function getRoleId()
    {
        return $this->roleId;
    }

    /**
     * Set the value of roleId
     *
     * @return  self
     */
    public function setRoleId($roleId)
    {
        $this->roleId = $roleId;

        return $this;
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

    public function toArray()
    {
        return [
            'id' => $this->id,
            'id_role' => $this->roleId,
            'id_user' => $this->userId
        ];
    }
}