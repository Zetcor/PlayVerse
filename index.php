<?php
session_start();

$host = 'localhost';
$user = 'root';
$password = '';
$dbname = 'PlayVerse';

$totalQuantity = 0;

if (isset($_SESSION['customer_id'])) {
	$customer_id = $_SESSION['customer_id'];

	$conn = new mysqli($host, $user, $password, $dbname);
	if ($conn->connect_error) {
		die("Connection failed: " . $conn->connect_error);
	}

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

		/* Promotions Section */
		.promotions-section {
			background-color: var(--light-gray);
			/* same as --navy */
			color: var(--navy);
		}

		.promotions-section h2 {
			color: var(--purple);
			font-family: 'Rajdhani', sans-serif;
		}

		.promotions-section p.lead {
			color: var(--light-gray);
			font-weight: 400;
		}

		.promotions-section .card {
			background-color: var(--navy);
			border: 1px solid rgba(255, 255, 255, 0.1);
			border-radius: var(--border-radius);
			box-shadow: var(--box-shadow);
			transition: var(--transition);
		}

		.promotions-section .card:hover {
			transform: scale(1.02);
			box-shadow: 0 8px 20px rgba(0, 0, 0, 0.3);
		}

		.promotions-section .card-title {
			font-family: 'Rajdhani', sans-serif;
			font-size: 1.5rem;
			color: var(--teal);
		}

		.promotions-section .card-text {
			font-size: 1rem;
			color: var(--light-gray);
		}

		/* Container for image to fix size */
		.promo-img-box {
			height: 400px;
			/* or 250px depending on preference */
			width: 100%;
			overflow: hidden;
			border-top-left-radius: var(--border-radius);
			border-top-right-radius: var(--border-radius);
		}

		/* Ensure image fills the container */
		.promo-img {
			height: 100%;
			width: 100%;
			object-fit: cover;
			display: block;
		}

		.promotions-section .btn {
			font-family: 'Rajdhani', sans-serif;
			font-weight: 600;
			border-radius: 4px;
			transition: var(--transition);
			border: 2px solid var(--teal);
			color: var(--teal);
		}

		.promotions-section .btn:hover {
			background-color: var(--pink);
			color: var(--navy);
			border-color: var(--pink);
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
							<a class="nav-link active" aria-current="page" href="index.php"><i class="fa-solid fa-house"></i>HOME</a>
						</li>

						<li class="nav-item">
							<a class="nav-link" href="offers.php"><i class="fa-solid fa-briefcase"></i>WHAT WE OFFER</a>
						</li>
						<li class="nav-item">
							<a class="nav-link" href="about.php"><i class="fa-solid fa-users"></i>ABOUT US</a>
						</li>
						<li class="nav-item">
							<a class="nav-link" href="cart.php">
								<i class="fa-solid fa-cart-shopping"></i>CART
								<?php if (isset($_SESSION['customer_id']) && $totalQuantity > 0): ?>
									<span class="badge bg-danger rounded-pill ms-1"><?= $totalQuantity ?></span>
								<?php endif; ?>
							</a>
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
					<a href="offers.php" class="cta-button"> Browse PlayVerse </a>
				</div>
			</div>
		</section>

		<!-- Promotions Section -->
		<section class="promotions-section py-5 text-light">
			<div class="container">
				<div class="text-center mb-5">
					<h2 class="display-5 fw-bold">🔥 Hot Promotions</h2>
					<p class="lead" style="color: #0a1128">
						Get the best deals on your favorite computer components
					</p>
				</div>
				<div class="row g-4">
					<!-- Promotion 1 -->
					<div class="col-md-4">
						<div class="card h-100 border-0 promotion-card">
							<div class="promo-img-box">
								<a href="product.php">
									<img
										src="imgs/rtx4080.jpg"
										class="promo-img"
										alt="Promotion GPU" />
								</a>
							</div>
							<div class="card-body">
								<h5 class="card-title">RTX 4080 Gaming GPU</h5>
								<p class="card-text">
									Now ₱5,000 off! Experience ultra-smooth gaming performance.
								</p>
								<a href="#" class="btn btn-outline-dark btn-sm">Shop Now</a>
							</div>
						</div>
					</div>

					<!-- Promotion 2 -->
					<div class="col-md-4">
						<div class="card h-100 text-light border-0">
							<div class="promo-img-box">
								<a href="product.php">
									<img
										src="imgs/ssd.jpg"
										class="promo-img"
										alt="Promotion SSD" />
								</a>
							</div>
							<div class="card-body">
								<h5 class="card-title">SAMSUNG 980 SSD</h5>
								<p class="card-text">
									Get this ultra-fast SSD, now 2,000 off!
								</p> <br>
								<a href="#" class="btn btn-outline-light btn-sm">Grab Deal</a>
							</div>
						</div>
					</div>

					<!-- Promotion 3 -->
					<div class="col-md-4">
						<div class="card h-100 border-0 promotion-card">
							<div class="promo-img-box">
								<a href="product.php">
									<img
										src="imgs/i9.jpg"
										class="promo-img"
										alt="Promotion CPU" />
								</a>
							</div>
							<div class="card-body">
								<h5 class="card-title">Intel Core i9 14th Gen</h5>
								<p class="card-text">
									Performance that powers every frame. Now with ₱3,500
									discount.
								</p>
								<a href="#" class="btn btn-outline-light btn-sm">Buy Now</a>
							</div>
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