<?php
session_start();

$totalQuantity = 0;
$username = null;

$conn = new mysqli("localhost", "root", "", "PlayVerse");
if ($conn->connect_error) {
	die("Connection failed: " . $conn->connect_error);
}

// If customer is logged in
if (isset($_SESSION['customer_id'])) {
	$customer_id = $_SESSION['customer_id'];

	// Get username
	$get_user = $conn->prepare("SELECT username FROM customers WHERE customer_id = ?");
	$get_user->bind_param("i", $customer_id);
	$get_user->execute();
	$get_user->bind_result($username);
	$get_user->fetch();
	$get_user->close();

	// Get or create cart
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

	// Get total quantity in cart
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

// Get product ID from URL
if (!isset($_GET['id'])) {
	die("Product ID not specified.");
}

$product_id = intval($_GET['id']);

// Fetch product from database
$product = null;
$sql = "SELECT * FROM products WHERE product_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $product_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
	$product = $result->fetch_assoc();
} else {
	die("Product not found.");
}

$stmt->close();

// Handle "Add to Cart" form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['product_id'], $_POST['quantity'])) {
	$product_id = intval($_POST['product_id']);
	$quantity = max(1, intval($_POST['quantity']));

	// Check if product already exists in the cart
	$stmt = $conn->prepare("SELECT quantity FROM cart_items WHERE cart_id = ? AND product_id = ?");
	$stmt->bind_param("ii", $cart_id, $product_id);
	$stmt->execute();
	$stmt->bind_result($existing_qty);

	if ($stmt->fetch()) {
		$stmt->close();
		$new_qty = $existing_qty + $quantity;

		// Update quantity in cart
		$upd = $conn->prepare("UPDATE cart_items SET quantity = ? WHERE cart_id = ? AND product_id = ?");
		$upd->bind_param("iii", $new_qty, $cart_id, $product_id);
		$upd->execute();
		$upd->close();
	} else {
		$stmt->close();
		// Insert new item into cart
		$ins = $conn->prepare("INSERT INTO cart_items (cart_id, product_id, quantity) VALUES (?, ?, ?)");
		$ins->bind_param("iii", $cart_id, $product_id, $quantity);
		$ins->execute();
		$ins->close();
	}

	// Update total quantity in the cart
	$sqlQty = "SELECT SUM(quantity) FROM cart_items WHERE cart_id = ?";
	$stmtQty = $conn->prepare($sqlQty);
	$stmtQty->bind_param("i", $cart_id);
	$stmtQty->execute();
	$stmtQty->bind_result($totalQuantity);
	$stmtQty->fetch();
	$stmtQty->close();
}
?>


<!DOCTYPE html>
<html lang="en">

<head>
	<meta charset="UTF-8" />
	<meta name="viewport" content="width=device-width, initial-scale=1.0" />
	<title>Product</title>
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
	<link rel="stylesheet" href="product.css" />

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
			color: var(--light-gray);
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

		.quantity-wrapper {
			margin-top: 0.5rem;
			width: 140px;
		}

		.input-group input[type='number']::-webkit-inner-spin-button,
		.input-group input[type='number']::-webkit-outer-spin-button {
			-webkit-appearance: none;
			margin: 0;
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
						<a class="nav-link active" aria-current="page" href="offers.php"><i class="fa-solid fa-briefcase"></i>WHAT WE OFFER</a>
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

	<!-- Product Detail Section -->
	<section class="hero mb-0 py-5">
		<div class="hero-content-wrapper">
			<div class="container">
				<div class="row g-5 align-items-center">
					<!-- Image -->
					<div class="col-md-6">
						<div class="product-img-box">
							<img src="imgs/<?= htmlspecialchars($product['image']) ?>" alt="<?= htmlspecialchars($product['name']) ?>" class="product-img" />
						</div>
					</div>

					<!-- Info -->
					<div class="col-md-6 text-light">
						<h2 class="display-5 fw-bold"><?= htmlspecialchars($product['name']) ?></h2>
						<p class="fw-bold fs-4" style="color: var(--teal)">₱<?= number_format($product['price'], 2) ?></p>
						<p><?= nl2br(htmlspecialchars($product['description'])) ?></p>

						<form method="post">
							<div class="quantity-wrapper mb-4">
								<label class="form-label mb-1"><strong>Quantity:</strong></label>
								<div class="input-group input-group-sm">
									<button class="btn btn-outline-light py-0 px-3 fs-5" type="button" onclick="this.nextElementSibling.stepDown()">-</button>
									<input type="number" class="form-control text-center border-light" name="quantity" value="1" min="1">
									<button class="btn btn-outline-light py-0 px-3 fs-5" type="button" onclick="this.previousElementSibling.stepUp()">+</button>
								</div>
							</div>
							<input type="hidden" name="product_id" value="<?= $product['product_id'] ?>" />
							<button type="submit" class="cta-cart">Add to Cart</button>
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