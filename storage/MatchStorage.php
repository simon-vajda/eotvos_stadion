<?php
require_once("lib/storage.php");

class MatchStorage extends Storage
{
    public function __construct()
    {
        parent::__construct(new JsonIO('data/matches.json'));
    }

    public function findMatches($id)
    {
        $matches = $this->findMany(function ($m) use ($id) {
            return $m["home"]["id"] === $id || $m["away"]["id"] === $id;
        });

        usort($matches, function ($a, $b) {
            $ta = strtotime($a["date"]);
            $tb = strtotime($b["date"]);
            return $ta < $tb ? 1 : ($ta == $tb ? 0 : -1);
        });

        return $matches;
    }

    public function findPreviousMatches($from, $to, $favorites)
    {
        $matches = $this->findMany(function ($m) use ($favorites) {
            return strtotime($m["date"]) < strtotime(date("Y-m-d")) && (in_array($m["home"]["id"], $favorites) || in_array($m["away"]["id"], $favorites));
        });

        usort($matches, function ($a, $b) {
            $ta = strtotime($a["date"]);
            $tb = strtotime($b["date"]);
            return $ta < $tb ? 1 : ($ta == $tb ? 0 : -1);
        });

        return array_slice($matches, $from, $to - $from);
    }

    public function previousMatchCount($favorites)
    {
        $matches = $this->findMany(function ($m) use ($favorites) {
            return strtotime($m["date"]) < strtotime(date("Y-m-d")) && (in_array($m["home"]["id"], $favorites) || in_array($m["away"]["id"], $favorites));
        });

        return count($matches);
    }

    public function isPlayedMatch($id)
    {
        $today = strtotime(date("Y-m-d"));
        $match = $this->findById($id);
        return strtotime($match["date"]) < $today;
    }
}
