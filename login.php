<?php
session_start();

$username = $password = "";
$message = "";

$conn = new mysqli("localhost", "root", "", "PlayVerse");
if ($conn->connect_error) {
	die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["reset"])) {
	header("Location: login.php");
	exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["login"])) {
	$username = trim($_POST['username']);
	$password = trim($_POST['password']);

	$stmt = $conn->prepare("SELECT * FROM customers WHERE username = ? AND password = ?");
	$stmt->bind_param("ss", $username, $password);
	$stmt->execute();
	$result = $stmt->get_result();

	if ($result->num_rows == 1) {
		$user = $result->fetch_assoc();

		$_SESSION['user'] = [
			'fullname' => $user['full_name'],
			'gender' => $user['gender'],
			'dob' => $user['birthdate'],
			'email' => $user['email'],
			'phone' => $user['contact_no'],
			'street' => $user['street'],
			'city' => $user['city'],
			'province' => $user['province'],
			'zip' => $user['zipcode'],
			'country' => $user['country'],
			'username' => $user['username']
		];

		header("Location: welcome.php");
		exit;
	} else {
		$message = "<div class='alert alert-danger py-2'>Invalid username or password.</div>";
	}

	$stmt->close();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
	<meta charset="UTF-8" />
	<meta name="viewport" content="width=device-width, initial-scale=1.0" />
	<title>Offers</title>
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
				<h2>PLAYVERSE</h2>
				<p>©2025 All Rights Reserved</p>
			</div>
		</footer>
	</div>
	<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js"></script>
	<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.min.js"></script>
</body>

</html>