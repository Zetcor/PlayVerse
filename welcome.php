<?php
session_start();

if (!isset($_SESSION['user'])) {
  header("Location: register.php");
  exit;
}

$user = $_SESSION['user'];
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Welcome</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" />
  <link href="https://db.onlinewebfonts.com/c/84d8d4c49f66a6a5abe1e0608ba764a2?family=Source+Sans+Pro" rel="stylesheet" />
  <link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=Rajdhani:wght@300;400;500;600;700&display=swap" rel="stylesheet" />
  <script src="https://kit.fontawesome.com/203f7bcb2a.js" crossorigin="anonymous"></script>
  <link rel="stylesheet" href="welcome.css" />
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
      <div class="profile-wrapper text-start fs-5">
        <div class="text-center mt-5 p-5 bg-light rounded shadow">
          <h1 class="mb-3"><i class="fa-solid fa-user-check" style="color: #00f5d4;"></i> Welcome, <i><?= htmlspecialchars($user['username']); ?></i>, to your dashboard!</h1>
          <p class="lead">You're now logged in. Explore what Playverse has to offer!</p>
        </div>

        <div class="card p-5 my-5 rounded shadow fs-5">
          <h2 class="mb-3">Personal Information</h2>
          <div class="row">
            <div class="col-md-6"><strong>Full Name:</strong> <?= htmlspecialchars($user['fullname']); ?></div>
            <div class="col-md-6"><strong>Gender:</strong> <?= htmlspecialchars($user['gender']); ?></div>
            <div class="col-md-6"><strong>Date of Birth:</strong> <?= htmlspecialchars($user['dob']); ?></div>
            <div class="col-md-6"><strong>Email:</strong> <?= htmlspecialchars($user['email']); ?></div>
            <div class="col-md-6"><strong>Phone Number:</strong> <?= htmlspecialchars($user['phone']); ?></div>
          </div>

          <hr class="my-4" />

          <h2 class="mb-3">Address Details</h2>
          <div class="row">
            <div class="col-md-6"><strong>Street:</strong> <?= htmlspecialchars($user['street']); ?></div>
            <div class="col-md-6"><strong>City:</strong> <?= htmlspecialchars($user['city']); ?></div>
            <div class="col-md-6"><strong>Province/State:</strong> <?= htmlspecialchars($user['province']); ?></div>
            <div class="col-md-6"><strong>Zip Code:</strong> <?= htmlspecialchars($user['zip']); ?></div>
            <div class="col-md-6"><strong>Country:</strong> <?= htmlspecialchars($user['country']); ?></div>
          </div>

          <div class="mt-3 mb-1 text-end">
            <a href="logout.php" class="btn btn-danger px-4 py-2">Log Out</a>
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