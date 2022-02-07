<?php
require_once("start.php");

function exists($name)
{
    return isset($_POST[$name]) && strlen(trim($_POST[$name])) > 0;
}

if (!$auth->is_authenticated() || $auth->authenticated_user()["username"] != "admin") {
    header("location: index.php");
    die();
}

if (!exists("id")) {
    header("location: index.php");
    die();
}

$id = $_POST["id"];
$comment = $commentsDb->findById($id);

if ($comment == NULL) {
    header("location: index.php");
    die();
}

$teamId = $comment["teamid"];
$commentsDb->delete($id);

header("location: team.php?id=" . $teamId);
