<?php
require_once("lib/storage.php");

class UserStorage extends Storage
{
    public function __construct()
    {
        parent::__construct(new JsonIO('data/users.json'));
    }

    public function getUsername($id)
    {
        return $this->findById($id)["username"];
    }
}
