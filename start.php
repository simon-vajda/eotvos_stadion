<?php
require_once("storage/TeamStorage.php");
require_once("storage/MatchStorage.php");
require_once("storage/CommentStorage.php");
require_once("storage/UserStorage.php");
require_once("storage/FavoriteStorage.php");
require_once("lib/auth.php");
session_start();

$teamsDb = new TeamStorage();
$matchesDb = new MatchStorage();
$commentsDb = new CommentStorage();
$usersDb = new UserStorage();
$favoritesDb = new FavoriteStorage();
$auth = new Auth($usersDb);
