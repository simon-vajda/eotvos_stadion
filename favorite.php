<?php
require_once("start.php");

function exists($name)
{
    return isset($_POST[$name]) && strlen(trim($_POST[$name])) > 0;
}

if (!exists("teamid") || !$auth->is_authenticated() || $teamsDb->findById($_POST["teamid"]) == null) {
    header("location: index.php");
    die();
}


$item = [];
$item["teamid"] = $_POST["teamid"];
$item["userid"] = $auth->authenticated_user()["id"];

$found = $favoritesDb->findOne($item);

if ($found !== null)
    $favoritesDb->delete($found["id"]);
else
    $favoritesDb->add($item);


header("location: team.php?id=" . $_POST["teamid"]);
