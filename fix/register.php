<?php
session_start();

$dataDir = __DIR__ . '/data';
if (!file_exists($dataDir)) mkdir($dataDir, 0777, true);

$usersFile = $dataDir . '/users.json';
if (!file_exists($usersFile)) {
    file_put_contents($usersFile, json_encode([]));
}

$err = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    $users = json_decode(file_get_contents($usersFile), true);
    if (!is_array($users)) $users = [];

    foreach ($users as $user) {
        if ($user['email'] === $email) {
            $err = "Email is already registered!";
            break;
        }
    }

    if (!$err) {
        $users[] = [
            'id' => count($users) + 1,
            'name' => $name,
            'email' => $email,
            'password' => password_hash($password, PASSWORD_DEFAULT)
        ];
        file_put_contents($usersFile, json_encode($users, JSON_PRETTY_PRINT));

        $success = "Account successfully registered!";
    
    }
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
      <h2 class="login-title">REGISTER</h2>

      <?php if ($err): ?>
        <p style="color: white; text-align: center; font-weight: bold; margin-bottom: 20px"><?php echo $err; ?></p>
      <?php endif; ?>
      <?php if ($success): ?>
      <p style="color: white; text-align: center; font-weight: bold; margin-bottom: 10px; margin-top: -10px">
        Account successfully registered! 
        <a href="login.php" style="color: #ffffffff; text-decoration: underline;">LOG IN</a>
      </p>
      <?php endif; ?>

      <form method="POST">

        <div class="input-group">
          <input type="text" name="name" placeholder="Full Name" required>
        </div>

        <div class="input-group">
          <input type="email" name="email" placeholder="email" required />
        </div>

        <div class="input-group">
          <input type="password" name="password" placeholder="password" required />
        </div>

        <button class="login-btn" type="submit">Register</button>
      </form>

      <div class="links">
        <span>Already have an accout? <a href="login.php">LOG IN</a></span>
      </div>
    </div>
  </div>

</body>
</html>