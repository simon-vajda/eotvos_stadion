<?php
require_once("lib/storage.php");

class FavoriteStorage extends Storage
{
    public function __construct()
    {
        parent::__construct(new JsonIO('data/favorites.json'));
    }

    public function getFavoriteCount($teamid)
    {
        return count($this->findAll(["teamid" => $teamid]));
    }

    public function getFavoriteTeams($userid)
    {
        return array_column($this->findAll(["userid" => $userid]), "teamid");
    }

    public function userHasLiked($teamid, $userid)
    {
        return $this->findOne(["teamid" => $teamid, "userid" => $userid]) !== NULL;
    }
}
