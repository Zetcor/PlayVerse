<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Homepage</title>
  <link
    href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css"
    rel="stylesheet" />
  <link
    href="https://db.onlinewebfonts.com/c/84d8d4c49f66a6a5abe1e0608ba764a2?family=Source+Sans+Pro"
    rel="stylesheet" />
  <link
    href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=Rajdhani:wght@300;400;500;600;700&display=swap"
    rel="stylesheet" />
  <script
    src="https://kit.fontawesome.com/203f7bcb2a.js"
    crossorigin="anonymous"></script>
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

        <button
          class="navbar-toggler text-white"
          type="button"
          data-bs-toggle="collapse"
          data-bs-target="#navbarContent"
          aria-controls="navbarContent"
          aria-expanded="false"
          aria-label="Toggle navigation">
          <i class="fa-solid fa-bars fa-lg" style="color: #00f5d4"></i>
        </button>

        <div
          class="collapse navbar-collapse justify-content-end"
          id="navbarContent">
          <ul class="navbar-nav gap-3">
            <li class="nav-item">
              <a class="nav-link active" aria-current="page" href="index.html">HOME</a>
            </li>

            <li class="nav-item">
              <a class="nav-link" href="#">WHAT WE OFFER</a>
            </li>
            <li class="nav-item">
              <a class="nav-link" href="#">ABOUT US</a>
            </li>
            <li class="nav-item">
              <a href="login.php" class="cta-login"> LOGIN </a>
            </li>
          </ul>
        </div>
      </div>
    </nav>

    <!-- Body Section -->
    <section class="hero d-flex align-items-center text-center">
      <div class="container">
        <div class="hero-content px-3 py-4 mx-auto">
          <h1 class="display-4 display-md-3 display-lg-1 fw-bold text-light">
            WELCOME TO <span class="highlight">PLAYVERSE</span>
          </h1>
          <p class="lead fw-medium text-light my-4">
            Welcome to Playverse, where you can have the opportunity to see
            the world of competitive gaming. Explore our offers and get to
            know us more.
          </p>
          <a href="#" class="cta-button"> Explore Now </a>
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