<?php
class Response
{
    private $message;
    private $status;
    private $data;
    private $error;
    private $details = [];

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

}