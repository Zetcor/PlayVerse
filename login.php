<?php
session_start();

$username = $password = "";
$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["reset"])) {
  header("Location: login.php");
  exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["login"])) {
  $username = trim($_POST['username']);
  $password = trim($_POST['password']);

  $users = file("user.txt", FILE_IGNORE_NEW_LINES);
  $found = false;

  foreach ($users as $userLine) {
    $data = explode("|", $userLine);

    if ($data[10] === $username && $data[11] === $password) {
      $found = true;
      $_SESSION['user'] = [
        'fullname' => $data[0],
        'gender' => $data[1],
        'dob' => $data[2],
        'email' => $data[3],
        'phone' => $data[4],
        'street' => $data[5],
        'city' => $data[6],
        'province' => $data[7],
        'zip' => $data[8],
        'country' => $data[9],
        'username' => $data[10]
      ];
      header("Location: welcome.php");
      exit;
    }
  }

  if (!$found) {
    $message = "<div class='alert alert-danger py-2'>Invalid username or password.</div>";
  }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Login</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" />
  <link href="https://db.onlinewebfonts.com/c/84d8d4c49f66a6a5abe1e0608ba764a2?family=Source+Sans+Pro" rel="stylesheet" />
  <link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=Rajdhani:wght@300;400;500;600;700&display=swap" rel="stylesheet" />
  <script src="https://kit.fontawesome.com/203f7bcb2a.js" crossorigin="anonymous"></script>
  <link rel="stylesheet" href="style.css" />
</head>

<body>
  <!-- Header Section -->
  <div class="scroll-container position-relative">
    <nav class="navbar navbar-expand-lg sticky-top">
      <div class="container-fluid px-5">
        <a class="navbar-brand d-flex align-items-center gap-2" href="index.html">
          <i class="fa-solid fa-gamepad fa-xl" style="color: #00f5d4;"></i>
          <h1>PLAYVERSE</h1>
        </a>
        <button class="navbar-toggler text-white" type="button" data-bs-toggle="collapse" data-bs-target="#navbarContent" aria-controls="navbarContent" aria-expanded="false" aria-label="Toggle navigation">
          <i class="fa-solid fa-bars fa-lg" style="color: #00f5d4"></i>
        </button>
        <div class="collapse navbar-collapse justify-content-end" id="navbarContent">
          <ul class="navbar-nav gap-3">
            <li class="nav-item">
              <a class="nav-link active" href="index.html">HOME</a>
            </li>
            <li class="nav-item">
              <a class="nav-link" href="#">WHAT WE OFFER</a>
            </li>
            <li class="nav-item">
              <a class="nav-link" href="#">ABOUT US</a>
            </li>
            <li class="nav-item">
              <a href="login.php" class="cta-login">LOG IN</a>
            </li>
          </ul>
        </div>
      </div>
    </nav>

    <!-- Body Section -->
    <section class="profile-section d-flex flex-column align-items-center justify-content-center">
      <div class="profile-wrapper text-start">
        <h1 class="profile-title mb-4"><i class="fa-solid fa-user fa-xs" style="width: 40px;"></i>LOG IN</h1>

        <!-- Profile Card -->
        <div class="profile-card">
          <div class="row g-0">
            <!-- Left: Profile Image and Name -->
            <div class="profile-left col-md-4 d-flex flex-column align-items-center justify-content-center p-4 rounded-start">
              <img src="https://api.dicebear.com/9.x/icons/svg?seed=Luis" alt="Profile Picture" class="img-fluid rounded-circle mb-3" />
              <h2 class="mb-0 text-center">Log in to your Account</h2>
              <h4 class="mb-0 text-center">Welcome back, please enter your details</h4>
            </div>

            <!-- Right: Profile Fields -->
            <div class="profile-right col-md-8 p-5 bg-white rounded-end">
              <form action="login.php" method="POST">
                <h1 class="mb-3">Account Details</h1>

                <div class="field mb-3">
                  <label>Username</label>
                  <input type="text" name="username" class="form-control" value="<?php echo $username ?>" placeholder="Enter your username" required />
                </div>

                <div class="field mb-3">
                  <label>Password</label>
                  <input type="password" name="password" class="form-control" placeholder="Enter your password" required />
                </div>

                <?php if (!empty($message)) echo "<div class='mb-3' id='error-section'>$message</div>"; ?>

                <!-- Buttons -->
                <div class="d-flex flex-column flex-md-row-reverse mt-4 gap-3">

                  <div class="w-100 w-md-50">
                    <button type="submit" name="login" class="btn w-100 custom-register-btn">
                      Login
                    </button>
                  </div>

                  <div class="w-100 w-md-50">
                    <button type="submit" name="reset" class="btn btn-outline-danger w-100 custom-clear-btn" formnovalidate>
                      Clear
                    </button>
                  </div>
                </div>

                <div class="acc-redirect text-center mt-3">
                  <p class="mt-4 mb-0" style="font-size: 0.9rem">
                    Don't have an account?
                    <a href="register.php" class="text-decoration-none">Register</a>
                  </p>
                </div>

              </form>

            </div>
          </div>

        </div>
      </div>
    </section>

    <!-- Footer Section -->
    <footer class="footer">
      <div class="name-column">
        <h2>Chua, Jenilyn Denise T.</h2>
        <p>
          ©2025 All Rights Reserved
        </p>
      </div>
    </footer>
  </div>
  <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.min.js"></script>
</body>

</html>