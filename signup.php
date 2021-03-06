<?php
require_once("start.php");

if ($auth->is_authenticated()) {
  header("location: index.php");
  die();
}

$errors = [];
$user = [];

function exists($name)
{
  return isset($_POST[$name]) && strlen(trim($_POST[$name])) > 0;
}

function getError($name)
{
  global $errors;
  return hasError($name) ? $errors[$name] : "";
}

function hasError($name)
{
  global $errors;
  return isset($errors[$name]);
}

function restore($name)
{
  global $user;
  global $errors;
  return (count($errors) > 0 && isset($user[$name])) ? $user[$name] : "";
}

if (count($_POST) > 0) {

  if (exists("username")) {
    $username = $_POST["username"];

    if ($auth->user_exists($username))
      $errors["username"] = "A felhasználónév már foglalt!";
    else
      $user["username"] = $username;
  } else {
    $errors["username"] = "A felhasználónév nem lehet üres!";
  }

  if (exists("email")) {
    $email = $_POST["email"];

    if (filter_var($email, FILTER_VALIDATE_EMAIL) !== false)
      $user["email"] = $email;
    else
      $errors["email"] = "Hibás email cím!";
  } else {
    $errors["email"] = "Az email cím nem lehet üres!";
  }

  if (exists("password")) {
    $password = $_POST["password"];
  } else {
    $errors["password"] = "A jelszó nem lehet üres!";
  }

  if (exists("password2")) {
    $password2 = $_POST["password2"];
  } else {
    $errors["password2"] = "A jelszó nem lehet üres!";
  }

  if (isset($password) && isset($password2)) {
    if ($password === $password2) {
      $user["password"] = $password;
    } else {
      $errors["password2"] = "A jelszavak nem egyeznek!";
    }
  }

  if (count($errors) === 0) {
    $auth->register($user);
    header("location: login.php");
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
  <title>Regisztráció | Stadium</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous" />
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css">
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
          <li class="nav-item">
            <a class="nav-link" href="login.php">Bejelentkezés</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="signup.php">Regisztráció</a>
          </li>
        </ul>
      </div>
    </div>
  </nav>
  <div class="container">
    <div class="row justify-content-center mt-5">
      <div class="card col col-sm-8 col-md-6 col-xl-4 text-center m-2">
        <div class="card-body">
          <h4 class="card-title text-center mt-1">Regisztráció</h4>
          <hr>
          <form action="" method="post" novalidate>
            <div class="input-group has-validation">
              <span class="input-group-text">
                <i class="bi bi-person-fill"></i>
              </span>
              <input class="form-control <?= hasError("username") ? "is-invalid" : "" ?>" type="text" name="username" id="username" placeholder="Felhasználónév" value="<?= restore("username") ?>">
            </div>
            <?php if (hasError("username")) : ?>
              <div class="text-start mt-1 text-danger">
                <?= getError("username") ?>
              </div>
            <?php endif ?>
            <div class="input-group has-validation mt-3">
              <span class="input-group-text">
                <i class="bi bi-envelope-fill"></i>
              </span>
              <input class="form-control <?= hasError("email") ? "is-invalid" : "" ?>" type="email" name="email" id="email" placeholder="Email" value="<?= restore("email") ?>">
            </div>
            <?php if (hasError("email")) : ?>
              <div class="text-start mt-1 text-danger">
                <?= getError("email") ?>
              </div>
            <?php endif ?>
            <div class="input-group has-validation mt-3">
              <span class="input-group-text">
                <i class="bi bi-lock-fill"></i>
              </span>
              <input class="form-control <?= hasError("password") ? "is-invalid" : "" ?>" type="password" name="password" id="password" placeholder="Jelszó">
            </div>
            <?php if (hasError("password")) : ?>
              <div class="text-start mt-1 text-danger">
                <?= getError("password") ?>
              </div>
            <?php endif ?>
            <div class="input-group has-validation mt-3">
              <span class="input-group-text">
                <i class="bi bi-lock-fill"></i>
              </span>
              <input class="form-control <?= hasError("password2") ? "is-invalid" : "" ?>" type="password" name="password2" id="password2" placeholder="Jelszó mégegyszer">
            </div>
            <?php if (hasError("password2")) : ?>
              <div class="text-start mt-1 text-danger">
                <?= getError("password2") ?>
              </div>
            <?php endif ?>
            <button class="btn btn-primary mt-4 w-100" type="submit">Regisztráció</button>
          </form>
        </div>
      </div>
      <div class="row justify-content-center mt-2">
        <div class="col text-center">
          <a href="login.php">Bejelentkezés</a>
        </div>
      </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous"></script>
</body>

</html>