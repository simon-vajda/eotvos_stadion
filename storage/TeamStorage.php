<?php
require_once("lib/storage.php");

class TeamStorage extends Storage
{
    public function __construct()
    {
        parent::__construct(new JsonIO('data/teams.json'));
    }

    public function getName($id)
    {
        return $this->findById($id)["name"];
    }
}
