<?php
class Response
{
    private $message;
    private $status;
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
        array_push($this->details, $details);
    }

    public function buildResponse()
    {
        return [
            'error' => $this->getError(),
            'status' => $this->getStatus(),
            'message' => $this->getMessage(),
            'details' => !empty($this->getDetails()) ? $this->getDetails() : null,
            'data' => !empty($this->getData()) ? $this->getData() : null,
        ];


    }

    public function getAddConflictMessage($tableName)
    {
        $this->setError("Conflict");
        $this->setMessage("This $tableName does already exists");
        $this->setStatus("409");
    }

    public function getCreatedSuccesfullyMessage($tableName)
    {
        $this->setError(null);
        $this->setStatus("201");
        $this->setMessage("$tableName created successfully");
    }
    public function getUpdatedNotFoundMessage($tableName)
    {
        $this->setError("Category not found");
        $this->setStatus("404");
        $this->setMessage("This $tableName cannot be updated because it does not exist");
    }
    public function getUpdatedExistsMessage($tableName)
    {
        $this->setError("Conflict");
        $this->setMessage("This $tableName cannot be updated because it already exists");
        $this->setStatus("409");
    }

    public function getUpdatedSuccesfullyMessage($tableName)
    {
        $this->setError(null);
        $this->setStatus("201");
        $this->setMessage("$tableName updated successfully");
    }

    public function getDeletedNotFoundMessage($tableName)
    {
        $this->setError("$tableName not found");
        $this->setStatus("404");
        $this->setMessage("This $tableName cannot be deleted because it does not exist");
    }

    public function getDeletedSuccesfullyMessage($tableName)
    {
        $this->setError(null);
        $this->setStatus("204");
        $this->setMessage("$tableName deleted successfully");
        $this->setData(null);
    }

    public function getRequiredFieldsMissingMessage()
    {
        $this->setStatus("400");
        $this->setError("Bad Request");
        $this->setMessage("Required fields are missing");
    }
    public function getInvalidLoginMessage()
    {
        $this->setStatus("200");
        $this->setError("Invalid credentials");
    }
    public function getValidLoginMessage()
    {
        $this->setStatus("200");
        $this->setError(null);
        $this->setMessage("Successful login");
    }




}