<?php
require_once("start.php");

if (!isset($_GET["id"])) {
  header("location: index.php");
  die();
}

$id = $_GET["id"];
$team = $teamsDb->findById($id);
$matches = $matchesDb->findMatches($id);
$isAdmin = $auth->is_authenticated() && $auth->authenticated_user()["username"] == "admin";

if ($team == NULL) {
  header("location: index.php");
  die();
}

function result($m)
{
  global $matchesDb;
  return $matchesDb->isPlayedMatch($m["id"]) ? $m["home"]["score"] . " - " . $m["away"]["score"] : "";
}

function resultColor($m)
{
  global $matchesDb;
  global $id;

  if (!$matchesDb->isPlayedMatch($m["id"]))
    return "";

  $ownScore   = $m["home"]["id"] == $id ? $m["home"]["score"] : $m["away"]["score"];
  $otherScore = $m["home"]["id"] != $id ? $m["home"]["score"] : $m["away"]["score"];

  if ($ownScore < $otherScore)
    return "loser";
  if ($ownScore > $otherScore)
    return "winner";

  return "tie";
}

if (count($_POST) > 0) {

  if (isset($_POST["new-comment"]) && strlen(trim($_POST["new-comment"])) > 0) {
    $newComment = [];
    $newComment["author"] = $auth->authenticated_user()["id"];
    $newComment["text"] = $_POST["new-comment"];
    $newComment["teamid"] = $id;
    $newComment["date"] = date("Y-m-d H:i:s");
    $commentsDb->add($newComment);
  } else {
    $commentError = true;
  }
}
?>

<!DOCTYPE html>
<html lang="hu">

<head>
  <meta charset="UTF-8" />
  <meta http-equiv="X-UA-Compatible" content="IE=edge" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Csapat | Stadium</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous" />
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css">
  <style>
    .winner {
      color: green !important;
    }

    .loser {
      color: red !important;
    }

    .tie {
      color: #f7c80c !important;
    }

    .delete-btn {
      background: none;
      border-width: 0;
      text-decoration: underline;
      display: inline;
    }
  </style>
</head>

<body>
  <nav class="navbar navbar-expand-sm navbar-dark bg-dark sticky-top">
    <div class="container-fluid">
      <a class="navbar-brand" href="index.php">Eötvös Loránd Stadion</a>
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarCollapse" aria-controls="navbarCollapse" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
      </button>
      <div class="collapse navbar-collapse" id="navbarCollapse">
        <ul class="navbar-nav ms-auto my-2 my-lg-0">
          <?php if (!$auth->is_authenticated()) : ?>
            <li class="nav-item">
              <a class="nav-link" href="login.php">Bejelentkezés</a>
            </li>
            <li class="nav-item">
              <a class="nav-link" href="signup.php">Regisztráció</a>
            </li>
          <?php else : ?>
            <li>
              <div class="p-2 text-dark"><?= $auth->authenticated_user()["username"] ?></div>
            </li>
            <li class="nav-item">
              <a class="nav-link" href="login.php?logout">Kijelentkezés</a>
            </li>
          <?php endif ?>
        </ul>
      </div>
    </div>
  </nav>

  <div class="container pt-4">
    <div class="d-flex flex-row justify-content-between">
      <h1><?= $team["name"] ?></h1>
      <form action="favorite.php" method="POST" class="d-flex align-items-center">
        <input type="hidden" name="teamid" value="<?= $id ?>">
        <button type="submit" class="btn <?= $favoritesDb->userHasLiked($id, $auth->authenticated_user()["id"]) ? "btn-danger" : "btn-outline-danger" ?> <?= !$auth->is_authenticated() ? "disabled" : "" ?>">
          <i class="bi bi-heart-fill"></i>
        </button>
        <span class="ms-2 fs-4"><?= $favoritesDb->getFavoriteCount($id) ?></span>
      </form>
    </div>

    <h2 class="mt-5">Meccsek</h2>
    <table class="table table-striped">
      <tr>
        <th>Hazai csapat</th>
        <th>Vendég csapat</th>
        <th>Eredmény</th>
        <th>Dátum</th>
        <?php if ($isAdmin) : ?>
          <th></th>
        <?php endif ?>
      </tr>
      <?php foreach ($matches as $m) : ?>
        <tr>
          <td><?= $teamsDb->getName($m["home"]["id"]) ?></td>
          <td><?= $teamsDb->getName($m["away"]["id"]) ?></td>
          <td class="<?= resultColor($m) ?>"><?= result($m) ?></td>
          <td><?= $m["date"] ?></td>
          <?php if ($isAdmin) : ?>
            <td><a href="edit.php?id=<?= $m["id"] ?>&teamid=<?= $id ?>">Módosít</a></td>
          <?php endif ?>
        </tr>
      <?php endforeach ?>
    </table>

    <h2 class="mt-5 mb-3">Megjegyzések</h2>

    <div class="container">

      <div class="row">
        <form action="" method="post" novalidate>
          <?php if (!$auth->is_authenticated()) : ?>
            <div class="mb-1 text-danger">
              Megjegyzés íráshoz be kell jelentkezni!
            </div>
          <?php endif ?>
          <textarea class="form-control" name="new-comment" id="new-comment" rows="2" placeholder="Új megjegyzés..." <?= !$auth->is_authenticated() ? "disabled" : "" ?>></textarea>
          <button class="btn btn-primary float-end me-2 mt-2 mb-4" type="submit" <?= !$auth->is_authenticated() ? "disabled" : "" ?>>
            Közzététel
          </button>
          <?php if (isset($commentError)) : ?>
            <div class="mb-1 text-danger">
              A komment nem lehet üres!
            </div>
          <?php endif ?>
        </form>
      </div>

      <div class="row">
        <?php foreach ($commentsDb->findComments($id) as $c) : ?>
          <div class="mb-3">
            <div class="card mb-2">
              <div class="card-body pb-1">
                <div class="card-text"><?= $c["text"] ?></div>
                <hr class="mt-2 mb-1" />
                <div class="mb-0">
                  <span class="text-dark"><?= $usersDb->getUsername($c["author"]) ?></span>
                  <span class="text-secondary"> • <?= $c["date"] ?></span>
                  <?php if ($isAdmin) : ?>
                    <form action="delete.php" method="post" class="d-inline">
                      <input type="hidden" name="id" value="<?= $c["id"] ?>">
                      <input class="text-danger float-end delete-btn" type="submit" value="Törlés"></input>
                    </form>
                  <?php endif ?>
                </div>
              </div>
            </div>
          </div>
        <?php endforeach ?>
      </div>

    </div>
  </div>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous"></script>
</body>

</html>