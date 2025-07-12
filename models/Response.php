<?php
class Response
{
    private $message;
    private $status = 200;
    private $data;
    private $error;
    private $details = [];

    public function __construct()
    {

    }

    public function getMessage()
    {
        return $this->message;
    }

    public function getStatus()
    {
        return $this->status;
    }

    public function getData()
    {
        return $this->data;
    }

    public function getError()
    {
        return $this->error;
    }

    public function getDetails()
    {
        return $this->details;
    }

    public function setMessage($message): void
    {
        $this->message = $message;
    }

    public function setStatus($status): void
    {
        $this->status = $status;
    }

    public function setData($data): void
    {
        $this->data = $data;
    }

    public function setError($error): void
    {
        $this->error = $error;
    }

    public function setDetails($details): void
    {
        $this->details[] = $details;
    }

    public function toArray()
    {
        return [
            'error' => $this->getError(),
            'status' => $this->getStatus(),
            'message' => $this->getMessage(),
            'details' => !empty($this->getDetails()) ? $this->getDetails() : null,
            'data' => !empty($this->getData()) ? $this->getData() : null,
        ];


    }
    public function toJson(): string
    {
        return json_encode($this->toArray());
    }


    public function setSuccessResponse(string $message, mixed $data = null, int $status = 200)
    {
        $this->setError(null);
        $this->setStatus($status);
        $this->setMessage($message);
        $this->setData($data);
        return $this;
    }

    public function setErrorResponse(string $error, string $message, int $status = 400)
    {
        $this->setError($error);
        $this->setStatus($status);
        $this->setMessage($message);
        return $this;
    }

    public function created(string $resource)
    {
        return $this->setSuccessResponse("$resource created successfully", null, 201);
    }

    public function updated(string $resource)
    {
        return $this->setSuccessResponse("$resource updated successfully", null, 200);
    }

    public function deleted(string $resource)
    {
        return $this->setSuccessResponse("$resource deleted successfully", null, 204);
    }

    public function notFound(string $resource)
    {
        return $this->setErrorResponse("$resource not found", "The $resource does not exist", 404);
    }

    public function conflict(string $resource)
    {
        return $this->setErrorResponse("Conflict", "This $resource already exists", 409);
    }

    public function missingFields()
    {
        return $this->setErrorResponse("Bad Request", "Required fields are missing", 400);
    }

    public function invalidCredentials()
    {
        return $this->setErrorResponse("Invalid credentials", "Login failed", 401);
    }

    public function loginSuccess()
    {
        return $this->setSuccessResponse("Login successful", null, 200);
    }
}



