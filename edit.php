<?php
require_once("start.php");

$errors = [];

function existsGet($name)
{
  return isset($_GET[$name]) && strlen(trim($_GET[$name])) > 0;
}

function existsPost($name)
{
  return isset($_POST[$name]) && strlen(trim($_POST[$name])) > 0;
}

function hasError($name)
{
  global $errors;
  return isset($errors[$name]);
}

if (!$auth->is_authenticated() || $auth->authenticated_user()["username"] != "admin") {
  header("location: index.php");
  die();
}

if (!existsGet("id")) {
  header("location: index.php");
  die();
}

$id = $_GET["id"];
$match = $matchesDb->findById($id);

if ($match == NULL) {
  header("location: index.php");
  die();
}

$team1 = $teamsDb->getName($match["home"]["id"]);
$team2 = $teamsDb->getName($match["away"]["id"]);

if (count($_POST) > 0) {
  if (existsPost("homePoints")) {
    $homePoints = $_POST["homePoints"];

    if (filter_var($homePoints, FILTER_VALIDATE_INT) !== null) {
      if ($homePoints >= 0) {
        $match["home"]["score"] = $homePoints;
      } else {
        $errors["homePoints"] = "Nem lehet negatív!";
      }
    } else {
      $errors["homePoints"] = "Egész számnak kell lennie!";
    }
  } else {
    $errors["homePoints"] = "Nem lehet üres!";
  }

  if (existsPost("awayPoints")) {
    $awayPoints = $_POST["awayPoints"];

    if (filter_var($awayPoints, FILTER_VALIDATE_INT) !== null) {
      if ($awayPoints >= 0) {
        $match["away"]["score"] = $awayPoints;
      } else {
        $errors["awayPoints"] = "Nem lehet negatív!";
      }
    } else {
      $errors["awayPoints"] = "Egész számnak kell lennie!";
    }
  } else {
    $errors["awayPoints"] = "Nem lehet üres!";
  }

  if (existsPost("date")) {
    $date = $_POST["date"];

    if (strtotime($date) !== false) {
      $match["date"] = $date;
    } else {
      $errors["date"] = "Hibás dátum!";
    }
  } else {
    $errors["date"] = "Nem lehet üres!";
  }

  if (count($errors) == 0) {
    $matchesDb->update($match["id"], $match);
    $teamid = $_GET["teamid"] ?? null;
    $location = $teamid === null ? "index.php" : "team.php?id=" . $teamid;
    header("location: " . $location);
    die();
  }
}

?>

<!DOCTYPE html>
<html lang="hu">

<head>
  <meta charset="UTF-8" />
  <meta http-equiv="X-UA-Compatible" content="IE=edge" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Szerkesztés | Stadium</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous" />
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
  <form action="" method="post" class="mt-4" novalidate>
    <div class="container">
      <div class="row">
        <div class="container col col-lg-8">
          <h1>Meccs adatok</h1>
          <div class="row mb-2 mt-4">
            <label for="homePoints" class="col-sm-3 col-form-label"><?= $team1 ?> pontok</label>
            <div class="col-sm-9">
              <div class="input-group">
                <input type="number" class="form-control <?= hasError("homePoints") ? "is-invalid" : "" ?>" id="homePoints" name="homePoints" value="<?= $match["home"]["score"] ?>" min="0" />
                <button type="button" class="btn btn-danger" onclick="onDelete('homePoints')">
                  Törlés
                </button>
              </div>
              <?php if (hasError("homePoints")) : ?>
                <div class="text-start mt-1 text-danger">
                  <?= $errors["homePoints"] ?>
                </div>
              <?php endif ?>
            </div>
          </div>
          <div class="row mb-2">
            <label for="awayPoints" class="col-sm-3 col-form-label"><?= $team2 ?> pontok</label>
            <div class="col-sm-9">
              <div class="input-group">
                <input type="number" class="form-control <?= hasError("awayPoints") ? "is-invalid" : "" ?>" id="awayPoints" name="awayPoints" value="<?= $match["away"]["score"] ?>" min="0" />
                <button type="button" class="btn btn-danger" onclick="onDelete('awayPoints')">
                  Törlés
                </button>
              </div>
              <?php if (hasError("awayPoints")) : ?>
                <div class="text-start mt-1 text-danger">
                  <?= $errors["awayPoints"] ?>
                </div>
              <?php endif ?>
            </div>
          </div>
          <div class="row mb-2">
            <label for="date" class="col-sm-3 col-form-label">Dátum</label>
            <div class="col-sm-9">
              <input type="date" class="form-control <?= hasError("date") ? "is-invalid" : "" ?>" name="date" id="date" value="<?= $match["date"] ?>" />
              <?php if (hasError("date")) : ?>
                <div class="text-start mt-1 text-danger">
                  <?= $errors["date"] ?>
                </div>
              <?php endif ?>
            </div>
          </div>
          <div class="row mt-5">
            <button type="submit" class="btn btn-primary col ms-3 me-3">
              Mentés
            </button>
          </div>
        </div>
      </div>
    </div>
  </form>
  <script>
    function onDelete(id) {
      document.querySelector("#" + id).value = 0;
    }
  </script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous"></script>
</body>

</html>