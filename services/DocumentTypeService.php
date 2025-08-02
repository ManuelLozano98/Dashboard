<?php

require_once __DIR__ . '/../models/Document_Type.php';
require_once __DIR__ . '/../models/Response.php';

class DocumentTypeService
{

    public function getDocument_Types()
    {
        return Document_Type::getAll();
    }
    public function getDocument_TypeById($id)
    {
        return Document_Type::findById($id);
    }
}