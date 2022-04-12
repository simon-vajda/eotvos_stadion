<?php
require_once("start.php");

$teams = $teamsDb->findAll();
$hasFavorites = false;

if ($auth->is_authenticated()) {
  $favoriteTeams = $favoritesDb->getFavoriteTeams($auth->authenticated_user()["id"]);
  $hasFavorites = !empty($favoriteTeams);
}

if (!$hasFavorites) $favoriteTeams = array_column($teams, "id");

$previousMatches = $matchesDb->findPreviousMatches(0, 5, $favoriteTeams);
?>

<!DOCTYPE html>
<html lang="hu">

<head>
  <meta charset="UTF-8" />
  <meta http-equiv="X-UA-Compatible" content="IE=edge" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Főoldal | Stadion</title>
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
  <div class="container pt-4">
    <h1>Eötvös Loránd Stadion</h1>
    <p>
      Üdvözlünk a stadion oldalán! Tudj meg többet a következő meccsekről, és
      a nálunk játszó csapatokról.
    </p>

    <h2 class="mt-5">Csapatok</h2>
    <table class="table table-striped">
      <tr>
        <th>Név</th>
        <th>Város</th>
      </tr>
      <?php foreach ($teams as $t) : ?>
        <tr>
          <td><a href="team.php?id=<?= $t["id"] ?>"><?= $t["name"] ?></a></td>
          <td><?= $t["city"] ?></td>
        </tr>
      <?php endforeach ?>
    </table>

    <h2 class="mt-5">Legutóbbi meccsek <?= $hasFavorites ? "(kedvencek)" : "" ?></h2>
    <table id="matches" class="table table-striped">
      <tr>
        <th>Hazai csapat</th>
        <th>Vendég csapat</th>
        <th>Eredmény</th>
        <th>Dátum</th>
      </tr>
      <?php foreach ($previousMatches as $m) : ?>
        <tr>
          <td><?= $teamsDb->getName($m["home"]["id"]) ?></td>
          <td><?= $teamsDb->getName($m["away"]["id"]) ?></td>
          <td><?= $m["home"]["score"] ?> - <?= $m["away"]["score"] ?></td>
          <td><?= $m["date"] ?></td>
        </tr>
      <?php endforeach ?>
    </table>
    <div class="text-center mb-4">
      <button id="loadBtn" class="btn btn-outline-secondary btn-sm <?= $matchesDb->previousMatchCount($favoriteTeams) <= 5 ? "invisible" : "" ?>">Több</button>
    </div>
  </div>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous"></script>
  <script src="loadMatches.js"></script>
</body>

</html>