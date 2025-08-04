<?php
require_once __DIR__ . '/UserService.php';
require_once __DIR__ . '/RoleService.php';
require_once __DIR__ . '/../models/UsersRoles.php';
require_once __DIR__ . '/../models/Response.php';

class UserRolesService
{
    private UserService $userService;
    private RoleService $roleService;

    public function __construct()
    {
        $this->userService = new UserService();
        $this->roleService = new RoleService();
    }
    public function getRoles()
    {
        return $this->roleService->getRoles();
    }
    public function getUsers()
    {
        return $this->userService->getUsers();
    }
    public function getUsersRolesById($id)
    {
        return UsersRoles::getUsersRolesById($id);
    }

    public function getUserswithRoles()
    {
        $usersRoles = UsersRoles::getUserswithRoles();
        $data = [];
        foreach ($usersRoles as $key => $value) {
            $data[$key] = $value->toArray();
            $data[$key]["username"] = $this->userService->getUser($value->getUserId())->getUsername();
            $data[$key]["name"] = $this->roleService->getRole($value->getRoleId())->getName();

        }
        $json = [];
        foreach ($data as $entry) {
            $user = $entry["username"];
            if (!isset($json[$user])) {
                $json[$user] = [
                    "id_user" => $entry["id_user"],
                    "username" => $user,
                    "roles" => []
                ];
            }
            $json[$user]["roles"][] = [
                "id" => $entry["id"],
                "id_role" => $entry["id_role"],
                "name" => $entry["name"]
            ];
        }
        $json = array_values($json);
        return $json;

    }

    public function getUserswithRole(Role $role)
    {
        return UsersRoles::getUsersByRole($role);
    }
    public function getUserswithRoleName($rolename)
    {
        return UsersRoles::getUsersByRoleName($rolename);
    }
    public function getRolesByUser(User $user)
    {
        $roles = UsersRoles::getRolesByUser($user);
        $data = [
            "id_user" => $roles[0]["id_user"],
            "username" => $roles[0]["username"],
            "roles" => []
        ];
        foreach ($roles as $entry) {
            $data["roles"][] = [
                "id" => $entry["id_user_role"],
                "id_role" => $entry["id_role"],
                "name" => $entry["name"]
            ];
        }
        return $data;
    }

    public function deleteUserRoles($id)
    {
        $response = new Response();
        $usersRoles = UsersRoles::findRolesById($id);
        foreach ($usersRoles as $userRole) {
            if (UsersRoles::delete($userRole)) {
                $response->deleted("User role");
            } else {
                return $response->setErrorResponse(
                    "Deletion failed",
                    "The user role could not be deleted due to an internal error",
                    500
                );
            }
        }
        return $response;
    }
    public function deleteRolesByUserRole($id)
    {
        $response = new Response();
        $usersRole = UsersRoles::getUsersRolesById($id);
        if (UsersRoles::delete($usersRole)) {
            $response->deleted("User role");
        } else {
            return $response->setErrorResponse(
                "Deletion failed",
                "The user role could not be deleted due to an internal error",
                500
            );
        }
        return $response;
    }
    public function saveUserRoles($method, $arrayUserRoles)
    {
        $response = new Response();
        if ($method === "POST") {
            if (!$this->userService->getUser($arrayUserRoles["id_user"])) {
                return $response->notFound("User");
            }
            $userRole = UsersRoles::findByUserIdAndRoleId($arrayUserRoles["id_user"], $arrayUserRoles["id_role"]);
            if (!empty($userRole)) {
                return $response->conflict("UserRoles");
            }
            $usersRoles = new UsersRoles($arrayUserRoles);
            if (UsersRoles::insert($usersRoles) === false) {
                $response->setErrorResponse(
                    "Insertion failed",
                    "The category could not be created due to an internal error",
                    500
                );
            } else {
                $response->created("UserRoles");
                $response->setData($usersRoles->toArray());
            }
            return $response;
        }
    }

    public function deleteByUserIdAndRoleId($userId, $roleId)
    {
        $response = new Response();
        $usersRoles = UsersRoles::findByUserIdAndRoleId($userId, $roleId);
        foreach ($usersRoles as $userRole) {
            if (UsersRoles::delete($userRole)) {
                $response->deleted("User role");
            } else {
                $response->setErrorResponse(
                    "Deletion failed",
                    "The user role could not be deleted due to an internal error",
                    500
                );
            }
        }
        return $response;
    }

    public function getRoleService()
    {
        return $this->roleService;
    }
    public function getUserService()
    {
        return $this->userService;
    }

    public function hasAdminRole(User $user)
    {
        return UsersRoles::isAdmin($user);
    }
    public function hasRole(User $user, $rolename)
    {
        return UsersRoles::hasRole($user, $rolename);
    }

}