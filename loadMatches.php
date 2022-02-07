<?php
require_once("start.php");

if (isset($_GET["from"]))
    $from = filter_var($_GET["from"], FILTER_VALIDATE_INT);
else
    $from = 0;

if ($from === false || $from < 0)
    $from = 0;

$teams = $teamsDb->findAll();
$hasFavorites = false;

if ($auth->is_authenticated()) {
    $favoriteTeams = $favoritesDb->getFavoriteTeams($auth->authenticated_user()["id"]);
    $hasFavorites = !empty($favoriteTeams);
}

if (!$hasFavorites) $favoriteTeams = array_column($teams, "id");

$to = $from + 5;
$output = [];
foreach ($matchesDb->findPreviousMatches($from, $to, $favoriteTeams) as $m) {
    $x = [];
    $x["home"]["name"] = $teamsDb->getName($m["home"]["id"]);
    $x["home"]["score"] = $m["home"]["score"];
    $x["away"]["name"] = $teamsDb->getName($m["away"]["id"]);
    $x["away"]["score"] = $m["away"]["score"];
    $x["date"] = $m["date"];
    $output["matches"][] = $x;
}

$output["hasMore"] = $to < $matchesDb->previousMatchCount($favoriteTeams);

echo (json_encode($output));
