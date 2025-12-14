<?php
session_start();
$dataDir = __DIR__ . '/data';
$usersFile = $dataDir . '/users.json';
$err = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    $users = json_decode(file_get_contents($usersFile), true);
    foreach ($users as $user) {
        if ($user['email'] === $email && password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['name'] = $user['name'];
            header('Location: dashboard1.php');
            exit;
        }
    }
    $err = "Invalid email or password!";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Sign In</title>
  <!-- Make sure this file is named exactly "style.css" and is in the same folder -->
  <link rel="stylesheet" href="style.css" />
</head>
<body>

  <!-- Background Image (styled in CSS) -->
  <div class="bg-image"></div>

  <!-- Centered sign-in container -->
  <div class="container">
    <div class="login-box">
      <h2 class="login-title">LOG IN</h2>

      <form method="POST">
        <div class="input-group">
          <input type="email" name= email placeholder="Email" required />
        </div>

        <div class="input-group">
          <input type="password" name= password placeholder="Password" required />
        </div>

        <button class="login-btn" type="submit">Log in</button>
      </form>

      <div class="links">
        <span>Don't have an accout? <a href="register.php">REGISTER</a></span>
      </div>
    </div>
  </div>

</body>
</html>


