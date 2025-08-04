<?php
require_once __DIR__ . "/../models/User.php";
require_once __DIR__ . "/MailService.php";
require_once __DIR__ . "/DocumentTypeService.php";
class UserService
{
    private MailService $mailService;
    private DocumentTypeService $documentTypeService;

    public function getUsers()
    {
        return User::getAll();
    }

    public function getUser($id)
    {
        return User::findById($id);
    }

    public function getUsernames()
    {
        $users = User::getUsernames();
        return array_map(function ($u) {
            return [
                "id" => $u->getId(),
                "username" => $u->getUsername()
            ];
        }, $users);

    }

    public function checkLogin($loginData)
    {
        $response = new Response();
        $login = $loginData["login"];
        $password = $loginData["password"];

        $data = $this->isEmail($login)
            ? User::findByEmail($login)
            : User::findByUsername($login);

        if ($data && $data->getActive() === 1 && $this->checkPassword($password, $data->getPassword())) {
            $response->loginSuccess();
            session_start();
            $_SESSION["user"] = $data;
        } else {
            $response->invalidCredentials();
        }

        return $response;
    }

    private function checkPassword($password, $hash)
    {
        return password_verify($password, $hash);
    }

    private function isEmail($param)
    {
        return filter_var($param, FILTER_VALIDATE_EMAIL) !== false;
    }

    public function saveImage($img)
    {
        $tmpName = $img['tmp_name'];
        $fileName = basename($img['name']);
        $fileExt = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

        $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
        if (in_array($fileExt, $allowed)) {
            $newName = uniqid() . '.' . $fileExt;
            $uploadPath = __DIR__ . '/../files/users_img/' . $newName;

            if (move_uploaded_file($tmpName, $uploadPath)) {
                return $newName;
            } else {
                return "File couldnt move";
            }
        } else {
            return "Ext not allowed";
        }
    }

    public function verifyToken($token)
    {
        $response = new Response();
        $response->setErrorResponse("Conflict", "The token has already expired", 409);
        $user = User::findByToken($token);
        $date = (new DateTime('now'))->format('Y-m-d H:i:s');
        if ($user && $user->getActive() === 0 && $user->getTokenExpiredAt() > $date) {
            if (User::activateAccount($user)) {
                $response->setSuccessResponse("The user has been successfully verified");
            }
        }
        return $response;
    }

    public function saveUser($method, $arrayUser)
    {
        $response = new Response();
        if ($method === "POST") {
            if (User::findByUsername($arrayUser["username"]) || User::findByEmail($arrayUser["email"])) {
                return $response->conflict("User");
            }
            $newUser = new User($arrayUser);
            $hash = password_hash($newUser->getPassword(), PASSWORD_BCRYPT);
            $newUser->setPassword($hash);
            $this->generateToken($newUser);
            $user = User::insert($newUser);
            if ($user === false) {
                $response->setErrorResponse(
                    "Insertion failed",
                    "The user could not be created due to an internal error",
                    500
                );
            } else {
                $response->created("User");
                $response->setData($user->toArray());
                $this->mailService = new MailService();
                if ($this->mailService->sendEmailVerification($user->getEmail(), $user->getUsername(), $user->getToken())) {
                    $response->setDetails(["email" => "Email sent successfully"]);
                } else {
                    $response->setDetails(["email" => "Email could not be sent. Mailer Error"]);
                }
            }
            return $response;

        } else {
            $userDb = User::findById($arrayUser["id"]);
            if (!$userDb) {
                return $response->notFound("User");
            }
            $existingEmail = User::findByEmail($arrayUser["email"]);
            $emailTaken = $existingEmail && $existingEmail->getEmail() !== $userDb->getEmail();

            if ($emailTaken) {
                return $response->conflict("User");
            }
        }
        if (!empty($arrayUser["password"])) {
            $password = $arrayUser["password"];
            $hash = password_hash($password, PASSWORD_BCRYPT);
            $userDb->setPassword($hash);
        }
        $img = "";
        if (isset($_FILES["image"]) && $_FILES["image"]['error'] === UPLOAD_ERR_OK) {
            $img = $this->saveImage($_FILES["image"]);
            $userDb->setImage($img);
        }

        $userDb->setName($arrayUser["name"]);
        $userDb->setActive(empty($arrayUser["active"]) ? $userDb->getActive() : $arrayUser["active"]);
        $userDb->setEmail(empty($arrayUser["email"]) ? $userDb->getEmail() : $arrayUser["email"]);
        $userDb->setPhone(empty($arrayUser["phone"]) ? $userDb->getPhone() : $arrayUser["phone"]);
        $userDb->setAddress(empty($arrayUser["address"]) ? $userDb->getAddress() : $arrayUser["address"]);
        $userDb->setDocumentType(empty($arrayUser["document_type"]) ? $userDb->getDocumentType() : $arrayUser["document_type"]);
        $userDb->setDocument(empty($arrayUser["document"]) ? $userDb->getDocument() : $arrayUser["document"]);



        if (User::edit($userDb) === false) {
            $response->setErrorResponse(
                "Update failed",
                "The user could not be updated due to an internal error",
                500
            );
        } else {
            $response->updated("User");
            $response->setData($userDb->toArray());
        }
        return $response;
    }

    public function deleteUser($id)
    {
        $user = User::findById($id);
        $response = new Response();
        if (!$user) {
            return $response->notFound("User");
        }
        return User::delete($id) ? $response->deleted("User") : $response->setErrorResponse(
            "Deletion failed",
            "The user could not be deleted due to an internal error",
            500
        );
    }

    private function generateToken(User $user)
    {
        $user->setToken(bin2hex(random_bytes(32)));
        $user->setTokenExpiredAt((new DateTime('+1 day'))->format('Y-m-d H:i:s'));
    }
    public function getUserByEmail($email)
    {
        return User::findByEmail($email);
    }

    public function sendEmailVerification($email)
    {
        $response = new Response();
        $data = User::findByEmail($email);
        if (!$data) {
            $response->notFound("Email");
            return $response;
        }
        $this->mailService = new MailService();
        if ($this->mailService->sendEmailVerification($data->getEmail(), $data->getUsername(), $data->getToken())) {
            $response->setSuccessResponse("Email sent successfully");
        } else {
            $response->setSuccessResponse("An error occurred, email could not be sent");
        }
        return $response;

    }
    public function getDocumentNameByUser(User $user)
    {
        $this->documentTypeService = new DocumentTypeService();
        if ($this->documentTypeService->getDocument_TypeById($user->getDocumentType()) !== FALSE) {
            return $this->documentTypeService->getDocument_TypeById($user->getDocumentType())->getName();
        }
        return "";
    }

    public function getDocumentTypes()
    {
        $this->documentTypeService = new DocumentTypeService();
        return $this->documentTypeService->getDocument_Types();
    }
}