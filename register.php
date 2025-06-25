<?php
$fullname = $gender = $dob = $email = $phone = "";
$street = $city = $province = $zip = $country = "";
$username = $password = $confirm_password = "";
$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["reset"])) {
  header("Location: register.php");
  exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
  $fullname = trim($_POST['fullname']);
  $gender = $_POST['gender'];
  $dob = $_POST['dob'];
  $email = trim($_POST['email']);
  $phone = trim($_POST['phone']);

  $street = trim($_POST['street']);
  $city = trim($_POST['city']);
  $province = trim($_POST['province']);
  $zip = trim($_POST['zip']);
  $country = trim($_POST['country']);

  $username = trim($_POST['username']);
  $password = $_POST['password'];
  $confirm_password = $_POST['confirm_password'];

  if (!preg_match("/^[A-Za-z ]{2,50}$/", $fullname)) {
    $message .= "<div class='alert alert-danger py-2'>Full Name must be 2–50 characters, letters and spaces only.</div>";
  }

  $birthdate = date_diff(date_create($dob), date_create('today'))->y;
  if ($birthdate < 18) {
    $message .= "<div class='alert alert-danger py-2'>You must be at least 18 years old.</div>";
  }

  if (!preg_match("/^[A-Za-z][A-Za-z0-9._-]{4,}@([A-Za-z]{2,}+\.)+[A-Za-z]{2,}$/", $email)) { //^[\w\.-]+@[\w\.-]+\.[a-zA-Z]{2,}$
    $message .= "<div class='alert alert-danger py-2'>Enter a valid email address.</div>";
  }

  if (!preg_match("/^09\d{9}$/", $phone)) {
    $message .= "<div class='alert alert-danger py-2'>Phone must be 11 digits and start with 09.</div>";
  }

  if (!preg_match("/^[a-zA-Z0-9\s.,#-]{5,100}$/", $street)) {
    $message .= "<div class='alert alert-danger py-2'>Street must be 5–100 characters with valid symbols.</div>";
  }

  if (!preg_match("/^[A-Za-z ]{2,50}$/", $city)) {
    $message .= "<div class='alert alert-danger py-2'>City must be 2–50 characters, letters and spaces only.</div>";
  }

  if (!preg_match("/^[A-Za-z ]{2,50}$/", $province)) {
    $message .= "<div class='alert alert-danger py-2'>Province/State must be 2–50 characters, letters and spaces only.</div>";
  }

  if (!preg_match("/^\d{4}$/", $zip)) {
    $message .= "<div class='alert alert-danger py-2'>Zip code must be exactly 4 digits.</div>";
  }

  if (!preg_match("/^[A-Za-z ]+$/", $country)) {
    $message .= "<div class='alert alert-danger py-2'>Country must contain letters and spaces only.</div>";
  }

  if (!preg_match("/^\w{5,20}$/", $username)) {
    $message .= "<div class='alert alert-danger py-2'>Username must be 5–20 characters with letters, numbers, or underscores.</div>";
  }

  if (!preg_match("/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).{8,}$/", $password)) {
    $message .= "<div class='alert alert-danger py-2'>Password must be atleast 8 characters with uppercase, lowercase, number, and special character.</div>";
  }

  if ($password !== $confirm_password) {
    $message .= "<div class='alert alert-danger py-2'>Passwords do not match.</div>";
  }

  if (empty($message)) {
    $line = implode("|", [$fullname, $gender, $dob, $email, $phone, $street, $city, $province, $zip, $country, $username, $password]) . "\n";
    file_put_contents("user.txt", $line, FILE_APPEND);

    $_SESSION['user'] = [
      'fullname' => $fullname,
      'gender' => $gender,
      'dob' => $dob,
      'email' => $email,
      'phone' => $phone,
      'street' => $street,
      'city' => $city,
      'province' => $province,
      'zip' => $zip,
      'country' => $country,
      'username' => $username,
      'password' => $password,
      'confirm_password' => $confirm_password
    ];

    echo "<script>alert('Registration successful! Proceeding to login...'); window.location.href = 'login.php'</script>";
    exit;
  }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Register</title>
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
        <h1 class="profile-title mb-4"><i class="fa-solid fa-user fa-xs" style="width: 40px;"></i>REGISTER</h1>

        <!-- Profile Card -->
        <div class="profile-card">
          <div class="row g-0">
            <!-- Left: Profile Image and Name -->
            <div class="profile-left col-md-4 d-flex flex-column align-items-center justify-content-center p-4 rounded-start">
              <img src="https://api.dicebear.com/9.x/icons/svg?seed=Luis" alt="Profile Picture" class="img-fluid rounded-circle mb-3" />
              <h2 class="mb-0 text-center">Create an Account</h2>
              <h4 class="mb-0 text-center">Register now to get started with an account</h4>
            </div>

            <!-- Right: Profile Fields -->
            <div class="profile-right col-md-8 p-5 bg-white rounded-end">
              <form action="register.php#error-section" method="POST">
                <h1 class="mb-3">Personal Information</h1>

                <div class="field mb-3">
                  <label>Full Name</label>
                  <input type="text" name="fullname" class="form-control" placeholder="Enter your full name"
                    value="<?php echo $fullname ?>" required />
                </div>

                <div class="row">
                  <div class="field mb-3 col-md-6">
                    <label>Gender</label>
                    <select class="form-select" name="gender" required>
                      <option value="">Select your gender</option>
                      <option <?php if ($gender == "Male") echo "selected"; ?>>Male</option>
                      <option <?php if ($gender == "Female") echo "selected"; ?>>Female</option>
                      <option <?php if ($gender == "Others") echo "selected"; ?>>Others</option>
                    </select>
                  </div>
                  <div class="field mb-3 col-md-6">
                    <label>Date of Birth</label>
                    <input type="date" name="dob" class="form-control" value="<?php echo $dob ?>" required />
                  </div>
                </div>

                <div class="row">
                  <div class="field mb-3 col-md-6">
                    <label>Email</label>
                    <input type="email" name="email" class="form-control" placeholder="Enter your email address"
                      value="<?php echo $email ?>" required />
                  </div>
                  <div class="field mb-3 col-md-6">
                    <label>Phone Number</label>
                    <input type="text" name="phone" class="form-control" placeholder="Enter your contact number"
                      value="<?php echo $phone ?>" required />
                  </div>
                </div>

                <h1 class="mt-3 mb-3">Address Details</h1>

                <div class="row">
                  <div class="field mb-3 col-md-6">
                    <label>Street</label>
                    <input type="text" name="street" class="form-control" placeholder="Enter your street"
                      value="<?php echo $street ?>" required />
                  </div>
                  <div class="field mb-3 col-md-6">
                    <label>City</label>
                    <input type="text" name="city" class="form-control" placeholder="Enter your city"
                      value="<?php echo $city ?>" required />
                  </div>
                </div>

                <div class="row">
                  <div class="field mb-3 col-md-6">
                    <label>Province/State</label>
                    <input type="text" name="province" class="form-control" placeholder="Enter your province"
                      value="<?php echo $province ?>" required />
                  </div>
                  <div class="field mb-3 col-md-6">
                    <label>Zip Code</label>
                    <input type="text" name="zip" class="form-control" placeholder="Enter your zipcode"
                      value="<?php echo $zip ?>" required />
                  </div>

                  <div class="field mb-3">
                    <label>Country</label>
                    <input type="text" name="country" class="form-control" placeholder="Enter your country"
                      value="<?php echo $country ?>" required />
                  </div>
                </div>

                <h1 class="mt-3 mb-3">Account Details</h1>

                <div class="field mb-3">
                  <label>Username</label>
                  <input type="text" name="username" class="form-control" placeholder="Enter your username"
                    value="<?php echo $username ?>" required />
                </div>

                <div class="field mb-3">
                  <label>Password</label>
                  <input type="password" name="password" class="form-control" placeholder="Enter your password" required />
                </div>

                <div class="field mb-4">
                  <label>Confirm Password</label>
                  <input type="password" name="confirm_password" class="form-control" placeholder="Re-enter Password" required />
                </div>

                <?php if (!empty($message)) echo "<div class='mb-3' id='error-section'>$message</div>"; ?>

                <!-- Buttons -->
                <div class="d-flex flex-column flex-md-row-reverse mt-4 gap-3">
                  <div class="w-100 w-md-50">
                    <button type="submit" name="register" class="btn w-100 custom-register-btn">
                      Register
                    </button>
                  </div>

                  <div class="w-100 w-md-50">
                    <button type="submit" name="reset" class="btn btn-outline-danger w-100 custom-clear-btn" formnovalidate>
                      Clear
                    </button>
                  </div>
                </div>

                <div class="acc-redirect  text-center mt-3">
                  <p class="mt-4 mb-0" style="font-size: 0.9rem">
                    Already have an account?
                    <a href="login.php" class="text-decoration-none">Log in</a>
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