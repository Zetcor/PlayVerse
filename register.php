<?php
session_start();

$conn = new mysqli("localhost", "root", "", "PlayVerse");
if ($conn->connect_error) {
	die("Connection failed: " . $conn->connect_error);
}

$fullname = $gender = $dob = $email = $phone = "";
$street = $city = $province = $zip = $country = "";
$username = $password = $confirm_password = "";
$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["reset"])) {
	header("Location: register.php");
	exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["register"])) {
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

	// Validation
	if (!preg_match("/^[A-Za-z ]{2,50}$/", $fullname)) {
		$message .= "<div class='alert alert-danger py-2'>Full Name must be 2–50 characters, letters and spaces only.</div>";
	}

	$birthdate = date_diff(date_create($dob), date_create('today'))->y;
	if ($birthdate < 18) {
		$message .= "<div class='alert alert-danger py-2'>You must be at least 18 years old.</div>";
	}

	if (!preg_match("/^[A-Za-z][A-Za-z0-9._-]{4,}@([A-Za-z]{2,}+\.)+[A-Za-z]{2,}$/", $email)) {
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
		$message .= "<div class='alert alert-danger py-2'>Password must be at least 8 characters with uppercase, lowercase, number, and special character.</div>";
	}

	if ($password !== $confirm_password) {
		$message .= "<div class='alert alert-danger py-2'>Passwords do not match.</div>";
	}

	if (empty($message)) {
		$check = $conn->prepare("SELECT customer_id FROM customers WHERE username = ? OR email = ?");
		$check->bind_param("ss", $username, $email);
		$check->execute();
		$check->store_result();

		if ($check->num_rows > 0) {
			$message .= "<div class='alert alert-danger py-2'>Username or Email already exists. Choose another.</div>";
		} else {
			$check->close();

			$stmt = $conn->prepare("INSERT INTO customers 
        (full_name, gender, birthdate, contact_no, email, username, password, street, city, province, zipcode, country) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

			$stmt->bind_param(
				"ssssssssssss",
				$fullname,
				$gender,
				$dob,
				$phone,
				$email,
				$username,
				$password,
				$street,
				$city,
				$province,
				$zip,
				$country
			);

			if ($stmt->execute()) {
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
					'username' => $username
				];

				echo "<script>alert('Registration successful! Proceeding to login...'); window.location.href = 'login.php';</script>";
				exit;
			} else {
				$message .= "<div class='alert alert-danger py-2'>Database error: " . $stmt->error . "</div>";
			}

			$stmt->close();
		}
	}
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
	<meta charset="UTF-8" />
	<meta name="viewport" content="width=device-width, initial-scale=1.0" />
	<title>Register</title>
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
	<style>
		:root {
			--light-gray: #f2f2f2;
			--purple: #8f43ec;
			--pink: #f15bb5;
			--teal: #00f5d4;
			--navy: #0a1128;

			--border-radius: 8px;
			--box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
			--transition: all 0.3s ease;
		}

		* {
			box-sizing: border-box;
			margin: 0;
			padding: 0;
		}

		body {
			font-family: 'Source Sans Pro', sans-serif;
			color: var(--navy);
			background-color: var(--light-gray);
			line-height: 1.6;
		}

		h1,
		h2,
		h3,
		h4,
		h5,
		h6 {
			font-family: 'Rajdhani', serif;
			font-weight: 600;
			color: var(--navy);
			margin-bottom: 1rem;
		}

		h1 {
			font-size: 2em;
		}

		h2 {
			font-size: 1.5em;
		}

		h3 {
			font-size: 1.17em;
		}

		h4 {
			font-size: 1em;
		}

		h5 {
			font-size: 0.83em;
		}

		h6 {
			font-size: 0.67em;
		}

		p {
			margin-bottom: 1rem;
			font-size: 1.25rem;
		}

		a {
			color: var(--light-gray);
			text-decoration: none;
			transition: var(--transition);
		}

		a:hover {
			color: var(--pink);
		}

		ul {
			list-style: none;
		}

		.fa-solid {
			margin-right: 8px;
		}

		.container {
			width: 100%;
			max-width: 1200px;
			margin: 0 auto;
			padding: 0 1rem;
		}

		/* Navbar Styles */
		.navbar {
			background-color: var(--navy);
			border-bottom: 1px solid rgba(155, 93, 229, 0.5);
			backdrop-filter: blur(10px);
			padding-top: 16px;
			padding-bottom: 16px;
			font-family: 'Rajdhani';
			z-index: 1000;
			font-weight: 700;
		}

		.navbar-brand {
			display: flex;
			align-items: center;
			gap: 10px;
			font-family: 'Rajdhani';
			text-decoration: none;
		}

		.navbar-brand h1 {
			margin: 0;
			font-size: 2rem;
			font-weight: 700;
			color: var(--teal);
		}

		/* Navbar Links */
		.navbar-nav {
			align-items: center;
		}

		.nav-link {
			color: var(--light-gray) !important;
			font-weight: 600;
			font-size: 1.1rem;
			padding: 5px 20px;
			position: relative;
			text-transform: uppercase;
			font-family: 'Rajdhani';
			transition: color 0.3s ease, background-color 0.3s ease;
		}

		.nav-link:hover,
		.nav-link:focus {
			border-bottom: 3px solid var(--pink);
		}

		/* Active Link Style */
		.nav-link.active {
			background-color: hsla(0, 0%, 95%, 0.2);
			border-radius: 4px;
			border-bottom: 3px solid var(--pink);
			color: var(--light-gray) !important;
		}

		/* Remove default list styling */
		.navbar-nav li {
			list-style: none;
		}

		/* Footer Styles */
		footer {
			background-color: var(--navy);
			color: var(--light-gray);
			padding: 20px 50px;
			border-top: 1px solid rgba(155, 93, 229, 0.5);
		}

		.name-column {
			min-width: 100px;
		}

		.name-column h2 {
			color: var(--light-gray);
			text-align: center;
			align-items: center;
			margin: 5px 0;
		}

		.name-column p {
			color: var(--teal);
			font-size: 1.1em;
			text-align: center;
			align-items: center;
			margin: 5px 0;
		}

		/* Hero Section */
		.hero {
			min-height: 90vh;
			background: linear-gradient(rgba(10, 17, 40, 0.8), rgba(10, 17, 40, 0.9));
			background-repeat: no-repeat;
			position: relative;
			overflow: hidden;
		}

		.hero::before {
			content: '';
			position: absolute;
			top: 0;
			left: 0;
			width: 100%;
			height: 100%;
			background: radial-gradient(circle at center,
					rgba(155, 93, 229, 0.2) 0%,
					rgb(10, 17, 40) 70%);
			z-index: 0;
		}

		.hero-content p {
			font-size: 1.25rem;
			font-weight: 500;
			color: var(--light-gray);
		}

		.hero-content {
			position: relative;
			z-index: 1;
			padding: 5% 1rem 10%;
			max-width: 1000px;
		}

		.hero-content h1 {
			font-size: 4rem;
			white-space: normal;
			word-break: break-word;
			overflow: hidden;
			text-shadow: 0 0 20px rgba(0, 0, 0, 0.5);
			color: var(--light-gray);
		}

		.hero-content p {
			font-size: 1.25rem;
			font-weight: 500;
			color: var(--light-gray);
		}

		.highlight {
			color: var(--teal);
			display: inline-block;
			position: relative;
		}

		/* CTA Button */
		.cta-button {
			background-color: var(--purple);
			color: var(--light-gray);
			font-family: 'Rajdhani', sans-serif;
			padding: 0.5rem 2rem;
			font-size: 1.2rem;
			border: solid 5px var(--purple);
			letter-spacing: 1px;
			transition: all 0.3s ease;
			border-radius: 4px;
			display: inline-block;
			position: relative;
			overflow: hidden;
			font-weight: 600;
			text-decoration: none;
		}

		.cta-button:hover {
			background-color: var(--pink);
			border: solid 5px var(--pink);
			border-style: outset;
			color: var(--navy);
		}

		.cta-button:active {
			transform: translateY(-1px);
			box-shadow: 0 5px 10px rgba(0, 0, 0, 0.2);
		}

		.cta-login {
			background-color: var(--purple);
			color: var(--light-gray);
			margin-top: 8px;
			padding: 2px 15px;
			border: solid 5px var(--purple);
			transition: all 0.3s ease;
			border-radius: 4px;
			font-weight: 700;
			display: inline-block;
			position: relative;
			overflow: hidden;
			text-decoration: none;
		}

		.cta-login:hover {
			background-color: var(--pink);
			border: solid 5px var(--pink);
			border-style: outset;
			color: var(--navy);
		}

		.cta-login:active {
			transform: translateY(-1px);
			box-shadow: 0 5px 10px rgba(0, 0, 0, 0.2);
		}

		.profile-section {
			padding: 40px 15px;
			min-height: 90vh;
			background: linear-gradient(rgba(10, 17, 40, 0.8), rgba(10, 17, 40, 0.9));
			background-repeat: no-repeat;
			position: relative;
			overflow: hidden;
		}

		.profile-section::before {
			content: '';
			position: absolute;
			top: 0;
			left: 0;
			width: 100%;
			height: 100%;
			background: radial-gradient(circle at center,
					rgba(155, 93, 229, 0.2) 0%,
					rgb(10, 17, 40) 70%);
			z-index: 0;
		}

		.profile-wrapper {
			max-width: 1100px;
			width: 100%;
			position: relative;
			z-index: 2;
		}

		.profile-title {
			font-size: 2.5rem;
			font-weight: 700;
			color: var(--light-gray);
			margin-bottom: 15px;
			position: relative;
			z-index: 2;
		}

		.profile-card {
			max-width: 1100px;
			width: 100%;
			border-radius: 8px;
			overflow: hidden;
			border: none;
			box-shadow: 3px 3px 8px var(--purple);
			margin-bottom: 15px;
		}

		.profile-card ul {
			list-style: disc;
		}

		.profile-card cite {
			font-size: 18px;
		}

		.profile-left {
			background-color: #e9e9e9;
		}

		.profile-left img {
			width: 180px;
			height: 180px;
			border-radius: 50%;
			object-fit: cover;
			border: 4px solid white;
		}

		.profile-left h2 {
			font-weight: 700;
			font-size: 24px;
			margin: 10px 0 0;
		}

		.profile-left p {
			margin-top: 4px;
			color: #555;
		}

		.profile-right {
			background-color: #ffffff;
		}

		.profile-right h1 {
			font-size: 36px;
			font-weight: 700;
		}

		.field label {
			display: block;
			font-size: 1.12rem;
			font-family: 'Rajdhani', serif;
			font-weight: 700;
			margin-bottom: 3px;
			color: #333;
		}

		.field input {
			width: 100%;
			padding: 12px;
			border: 1px solid #ccc;
			border-radius: 6px;
		}

		.field p,
		.field ul {
			margin-bottom: 3px;
		}

		.raw p {
			display: block;
			font-size: 15px;
		}

		.custom-register-btn {
			background-color: #8f43ec !important;
			color: #fff !important;
			border: none !important;
			font-weight: 600;
			height: 45px;
			transition: var(--transition);
		}

		.custom-register-btn:hover {
			background-color: #3a146f !important;
			color: #fff !important;
		}

		.custom-clear-btn {
			height: 45px;
		}

		.acc-redirect a {
			color: #4f4fff;
			transition: var(--transition);
		}

		.acc-redirect a:hover {
			color: var(--pink);
		}
	</style>
</head>

<body>
	<!-- Header Section -->
	<div class="scroll-container position-relative">
		<nav class="navbar navbar-expand-lg sticky-top">
			<div class="container-fluid px-5">
				<a
					class="navbar-brand d-flex align-items-center gap-2"
					href="index.php">
					<img src="imgs/logo.png" alt="logo" style="height: 40px" />
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
							<a class="nav-link" href="index.php"><i class="fa-solid fa-house"></i>HOME</a>
						</li>
						<li class="nav-item">
							<a class="nav-link" href="offers.html"><i class="fa-solid fa-briefcase"></i>WHAT WE OFFER</a>
						</li>
						<li class="nav-item">
							<a class="nav-link" href="about.html"><i class="fa-solid fa-users"></i>ABOUT US</a>
						</li>
						<li class="nav-item">
							<a class="nav-link" href="cart.html"><i class="fa-solid fa-cart-shopping"></i>CART</a>
						</li>
						<li class="nav-item">
							<a href="login.php" class="cta-login"><i class="fa-solid fa-circle-user"></i>LOGIN
							</a>
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
				<h2>PLAYVERSE</h2>
				<p>©2025 All Rights Reserved</p>
			</div>
		</footer>
	</div>
	<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js"></script>
	<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.min.js"></script>
</body>

</html>