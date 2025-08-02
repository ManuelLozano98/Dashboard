<?php

require_once __DIR__ . '/../models/Role.php';
require_once __DIR__ . '/../models/Response.php';
class RoleService
{

    public function getRoles()
    {
        return Role::getAll();
    }
    public function getRole($id)
    {
        return Role::findById($id);
    }

    public function deleteRole($id)
    {
        $role = Role::findById($id);
        $response = new Response();
        if (!$role) {
            return $response->notFound("Role");
        }
        return Role::delete($id) ? $response->deleted("Role") : $response->setErrorResponse(
            "Deletion failed",
            "The role could not be deleted due to an internal error",
            500
        );
    }
    public function saveRole($method, $arrayRole)
    {
        $response = new Response();
        if ($method === "POST") {
            if (Role::findByName($arrayRole["name"])) {
                return $response->conflict("Role");
            }

            $role = new Role($arrayRole);
            if (Role::insert($role) === false) {
                $response->setErrorResponse(
                    "Insertion failed",
                    "The role could not be created due to an internal error",
                    500
                );
            } else {
                $response->created("Role");
                $response->setData($role->toArray());
            }
            return $response;

        } else {
            $roleDb = Role::findById($arrayRole["id"]);
            if (!$roleDb) {
                return $response->notFound("Role");
            }
            $insertedName = Role::findByName($arrayRole["name"]);
            if ($insertedName) {
                return $response->conflict("Role");
            }
            $roleDb->setName($arrayRole["name"]);
            if (Role::edit($roleDb) === false) {
                $response->setErrorResponse(
                    "Update failed",
                    "The role could not be updated due to an internal error",
                    500
                );
            } else {
                $response->updated("Role");
                $response->setData($roleDb->toArray());
            }
            return $response;
        }
    }

}