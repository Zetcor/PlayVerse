<?php
session_start();

$totalQuantity = 0;
$username = '';

if (isset($_SESSION['customer_id'])) {
	$customer_id = $_SESSION['customer_id'];

	$conn = new mysqli("localhost", "root", "", "PlayVerse");
	if ($conn->connect_error) {
		die("Connection failed: " . $conn->connect_error);
	}

	// Fetch username
	$user_sql = "SELECT username FROM customers WHERE customer_id = ?";
	$stmtUser = $conn->prepare($user_sql);
	$stmtUser->bind_param("i", $customer_id);
	$stmtUser->execute();
	$stmtUser->bind_result($username);
	$stmtUser->fetch();
	$stmtUser->close();

	$cart_id = null;
	$cart_sql = "SELECT cart_id FROM cart WHERE customer_id = ?";
	$stmt = $conn->prepare($cart_sql);
	$stmt->bind_param("i", $customer_id);
	$stmt->execute();
	$stmt->bind_result($cart_id);

	if (!$stmt->fetch()) {
		$stmt->close();
		$insert_cart_sql = "INSERT INTO cart (customer_id) VALUES (?)";
		$stmt2 = $conn->prepare($insert_cart_sql);
		$stmt2->bind_param("i", $customer_id);
		$stmt2->execute();
		$cart_id = $stmt2->insert_id;
		$stmt2->close();
	} else {
		$stmt->close();
	}

	// Calculate total quantity
	$sqlQty = "SELECT SUM(quantity) FROM cart_items WHERE cart_id = ?";
	$stmtQty = $conn->prepare($sqlQty);
	$stmtQty->bind_param("i", $cart_id);
	$stmtQty->execute();
	$stmtQty->bind_result($totalQuantity);
	$stmtQty->fetch();
	$stmtQty->close();

	if (!$totalQuantity) {
		$totalQuantity = 0;
	}
}
?>


<!DOCTYPE html>
<html lang="en">

<head>
	<meta charset="UTF-8" />
	<meta name="viewport" content="width=device-width, initial-scale=1.0" />
	<title>About Us - Playverse</title>
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
			background: linear-gradient(rgba(10, 17, 40, 0.8),
					rgba(10, 17, 40, 0.9));
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

		.hero-content-wrapper {
			position: relative;
			z-index: 1;
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
			background: linear-gradient(rgba(10, 17, 40, 0.8),
					rgba(10, 17, 40, 0.9));
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

		.custom-card {
			background-color: var(--light-gray);
			font-size: 2.25rem !important;
		}

		.product-img-box {
			width: 100%;
			max-width: 500px;
			height: 500px;
			/* Adjust height as needed */
			overflow: hidden;
			border-radius: var(--border-radius);
			box-shadow: var(--box-shadow);
			background-color: #fff;
			display: flex;
			justify-content: center;
			align-items: center;
		}

		.product-img {
			width: 100%;
			height: 100%;
			object-fit: contain;
			display: block;
		}

		.cart-section {
			background-color: var(--light-gray);
			min-height: 100vh;
		}

		.cart-img {
			width: 120px;
			height: 120px;
			object-fit: contain;
			border-radius: var(--border-radius);
			box-shadow: var(--box-shadow);
			background: #fff;
		}

		.cta-cart {
			background-color: var(--purple);
			color: var(--light-gray);
			margin-top: 8px;
			padding: 8px 30px;
			border: solid 5px var(--purple);
			transition: all 0.3s ease;
			border-radius: 4px;
			font-weight: 700;
			display: inline-block;
			position: relative;
			overflow: hidden;
			text-decoration: none;
			font-family: 'Rajdhani', sans-serif;
			font-size: 1.2rem;
		}

		.cta-cart:hover {
			background-color: var(--pink);
			border: solid 5px var(--pink);
			border-style: outset;
			color: var(--navy);
		}

		.cta-cart:active {
			transform: translateY(-1px);
			box-shadow: 0 5px 10px rgba(0, 0, 0, 0.2);
		}

		/* Checkout Section */
		.checkout-section {
			background-color: var(--light-gray);
			min-height: 100vh;
		}

		.checkout-section h4,
		.checkout-section h5 {
			color: var(--navy);
		}

		.checkout-section .form-label {
			font-weight: 600;
			color: var(--navy);
		}

		.checkout-section .form-control,
		.checkout-section textarea {
			border-radius: 6px;
			box-shadow: none;
			border: 1px solid #ccc;
		}

		.checkout-section .form-check-label {
			color: var(--navy);
		}

		.checkout-section .form-check-input {
			margin-right: 10px;
		}

		/* Adjust spacing and box look for summary */
		.checkout-section .shadow {
			box-shadow: var(--box-shadow);
			border-radius: var(--border-radius);
		}

		.checkout-section .d-flex.justify-content-between {
			font-size: 1rem;
		}

		/* Responsive tweaks */
		@media (max-width: 768px) {
			.checkout-section .row {
				flex-direction: column;
			}
		}

		.summary-img-box {
			width: 60px;
			height: 60px;
			border-radius: var(--border-radius);
			overflow: hidden;
			background-color: #fff;
			box-shadow: var(--box-shadow);
			display: flex;
			align-items: center;
			justify-content: center;
		}

		.summary-img {
			width: 100%;
			height: 100%;
			object-fit: contain;
		}

		/* About Us Section */
		.about-section {
			background-color: var(--light-gray);
			padding: 60px 0;
		}

		.company-info {
			background-color: #fff;
			border-radius: var(--border-radius);
			box-shadow: var(--box-shadow);
			padding: 30px;
			margin-top: -10px;
			margin: 0 12px 10px;
		}

		.contact-form {
			padding: 30px;
			margin-top: -10px;
			margin: 0 12px 10px;
		}

		.contact-form .form-control {
			border-radius: 6px;
			border: 1px solid #ccc;
			padding: 10px;
			box-shadow: none;
		}

		.contact-form .btn-primary {
			background-color: var(--purple);
			border: none;
			font-weight: 600;
			transition: var(--transition);
		}

		.contact-form .btn-primary:hover {
			background-color: var(--pink);
			color: var(--navy);
		}

		.dropdown-menu {
			background-color: var(--navy);
			border: none;
			border-radius: 6px;
			box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
			padding: 10px 0;
		}

		.dropdown-item {
			color: var(--light-gray);
			font-family: 'Rajdhani', sans-serif;
			font-size: 0.95rem;
			padding: 10px 20px;
			transition: background-color 0.3s ease, color 0.3s ease;
		}

		.dropdown-item:hover {
			background-color: hsla(0, 0%, 95%, 0.25);
			color: var(--light-gray);
		}

		.dropdown-toggle::after {
			color: var(--light-gray);
			font-size: 0.75rem;
		}

		.dropdown-toggle:hover::after {
			color: var(--pink);
		}

		.dropdown-item[href="logout.php"]:hover {
			background-color: #dc3545;
			color: white;
		}

		.about-row-match-height {
			display: flex;
			flex-wrap: wrap;
			align-items: stretch;
		}

		.about-row-match-height .text-col {
			display: flex;
			flex-direction: column;
			justify-content: center;
		}

		.about-row-match-height .image-col {
			display: flex;
			align-items: stretch;
			justify-content: center;
		}

		.about-row-match-height .image-col img {
			width: 700px;
			height: 300px;
			object-fit: cover;
			border-radius: var(--border-radius);
			box-shadow: var(--box-shadow);
			background: #fff;
		}
	</style>
</head>

<body>
	<!-- Navbar -->
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
						<a class="nav-link" href="offers.php"><i class="fa-solid fa-briefcase"></i>WHAT WE OFFER</a>
					</li>
					<li class="nav-item">
						<a class="nav-link active" aria-current="page" href="about.php"><i class="fa-solid fa-users"></i>ABOUT US</a>
					</li>
					<li class="nav-item">
						<a class="nav-link" href="cart.php">
							<i class="fa-solid fa-cart-shopping"></i>CART
							<?php if (isset($_SESSION['customer_id']) && $totalQuantity > 0): ?>
								<span class="badge bg-danger rounded-pill ms-1"><?= $totalQuantity ?></span>
							<?php endif; ?>
						</a>
					</li>
					<li class="nav-item dropdown">
						<?php if (isset($_SESSION['customer_id']) && $username): ?>
							<a class="cta-login dropdown-toggle" href="#" id="userDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
								<i class="fa-solid fa-circle-user"></i> <?= htmlspecialchars($username) ?>
							</a>
							<ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
								<li>
									<a class="dropdown-item" href="welcome.php">
										<i class="fa-solid fa-user"></i> My Profile
									</a>
								</li>
								<li>
									<a class="dropdown-item" href="logout.php">
										<i class="fa-solid fa-right-from-bracket"></i> Logout
									</a>
								</li>
							</ul>
						<?php else: ?>
							<a href="login.php" class="cta-login">
								<i class="fa-solid fa-circle-user"></i> LOGIN
							</a>
						<?php endif; ?>
					</li>
				</ul>
			</div>
		</div>
	</nav>

	<!-- About Us Section -->
	<section class="about-section">
		<div class="container">
			<div class="text-center mb-5">
				<h2 class="display-5 fw-bold" style="color: var(--purple)">
					ABOUT US
				</h2>
				<p
					class="lead"
					style="color: var(--navy); max-width: 800px; margin: 0 auto">
					Welcome to PLAYVERSE – where gaming meets passion and innovation. We
					provide top-tier gaming products and exceptional service to elevate
					your experience. Whether you’re a competitive player or a casual
					enthusiast, we’re here to support your journey.
				</p>
			</div>

			<!-- Two Horizontal Sections with Alternating Image/Text -->
			<div class="container mb-4">
				<!-- First Row: Text Left, Image Right -->
				<div class="row about-row-match-height mb-4">
					<div class="col-md-6 text-col">
						<div class="p-4 bg-white rounded shadow h-100">
							<p>
								"We combine technology, passion, and precision to deliver a gaming experience like no other.
								We’re committed to helping gamers reach their full potential with carefully selected gear
								and trusted support."
							</p>
							<p class="text-end" style="color: var(--purple);">
								— Jenilyn Denise T. Chua
								<br><small>PlayVerse Author</small>
							</p>
						</div>
					</div>
					<div class="col-md-6 image-col">
						<img src="imgs/chua.JPG" alt="Why Choose Us" />
					</div>
				</div>

				<!-- Second Row: Image Left, Text Right -->
				<div class="row about-row-match-height mb-3 flex-md-row-reverse">
					<div class="col-md-6 text-col">
						<div class="p-4 bg-white rounded shadow h-100">
							<p>
								"We’re more than just a brand—we’re gamers too. From the latest console releases to high-end PC builds,
								our passion drives everything we do. Join us on the journey to level up your play."
							</p>
							<p class="text-end" style="color: var(--purple);">
								— Vince Nicholai J. Cortez
								<br><small>PlayVerse Author</small>
							</p>
						</div>
					</div>
					<div class="col-md-6 image-col">
						<img src="imgs/cortez.JPG" alt="Gaming Passion" />
					</div>
				</div>

			</div>

			<div class="row g-1">
				<!-- Company Info -->
				<div class="col-md-6">
					<div class="company-info">
						<h2 style="color: var(--navy)">Our Mission</h2>
						<p>
							Our mission is to empower professional and casual gamers by providing premium equipment,
							fast support, and a seamless shopping experience. From GPUs and
							consoles to accessories, we deliver what you need to stay ahead
							in the game.
						</p>

						<h2 style="color: var(--navy)" class="mt-4">Get in Touch</h2>
						<p>
							<i class="fa-solid fa-envelope"></i> Email:
							support@playverse.com
						</p>
						<p><i class="fa-solid fa-phone"></i> Phone: +63 900 123 4567</p>
						<p>
							<i class="fa-solid fa-location-dot"></i> Quezon City,
							Philippines
						</p>
					</div>
				</div>

				<!-- Contact Form -->
				<div class="col-md-6">
					<div class="contact-form bg-white p-4 rounded shadow">
						<h2 class="mb-3" style="color: var(--navy)">Contact Us</h2>
						<form>
							<div class="mb-3">
								<label for="contactName" class="form-label">Full Name</label>
								<input
									type="text"
									class="form-control"
									id="contactName"
									required />
							</div>
							<div class="mb-3">
								<label for="contactEmail" class="form-label">Email</label>
								<input
									type="email"
									class="form-control"
									id="contactEmail"
									required />
							</div>
							<div class="mb-4">
								<label for="contactMessage" class="form-label">Message</label>
								<textarea
									class="form-control"
									id="contactMessage"
									rows="3"
									required></textarea>
							</div>
							<button type="submit" class="btn btn-primary w-100">
								Send Message
							</button>
						</form>
					</div>
				</div>
			</div>
		</div>
	</section>

	<!-- Footer -->
	<footer class="footer">
		<div class="name-column text-center">
			<h2>PLAYVERSE</h2>
			<p>©2025 All Rights Reserved</p>
		</div>
	</footer>

	<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>