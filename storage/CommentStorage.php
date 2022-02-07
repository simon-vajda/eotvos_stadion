<?php
require_once("lib/storage.php");

class CommentStorage extends Storage
{
    public function __construct()
    {
        parent::__construct(new JsonIO('data/comments.json'));
    }

    public function findComments($teamid)
    {
        $comments = $this->findMany(function ($c) use ($teamid) {
            return $c["teamid"] == $teamid;
        });

        usort($comments, function ($a, $b) {
            $ta = strtotime($a["date"]);
            $tb = strtotime($b["date"]);
            return $ta < $tb ? 1 : ($ta == $tb ? 0 : -1);
        });

        return $comments;
    }
}
