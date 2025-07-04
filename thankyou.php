<?php
session_start();

if (!isset($_SESSION['user']['username'])) {
	header("Location: register.php");
	exit;
}

$conn = new mysqli("localhost", "root", "", "PlayVerse");
if ($conn->connect_error) {
	die("Connection failed: " . $conn->connect_error);
}

$transactionId = isset($_GET['transaction_id']) ? (int)$_GET['transaction_id'] : 0;

$username = $_SESSION['user']['username'];
$stmt = $conn->prepare("SELECT * FROM customers WHERE username = ?");
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows !== 1) {
	die("User not found.");
}

$user = $result->fetch_assoc();
$stmt->close();

$totalQuantity = 0;

if (isset($_SESSION['customer_id'])) {
	$customer_id = $_SESSION['customer_id'];

	$stmtCart = $conn->prepare("SELECT cart_id FROM cart WHERE customer_id = ?");
	$stmtCart->bind_param("i", $customer_id);
	$stmtCart->execute();
	$stmtCart->bind_result($cart_id);
	if ($stmtCart->fetch()) {
		$stmtCart->close();

		$stmtQty = $conn->prepare("SELECT SUM(quantity) FROM cart_items WHERE cart_id = ?");
		$stmtQty->bind_param("i", $cart_id);
		$stmtQty->execute();
		$stmtQty->bind_result($totalQuantity);
		$stmtQty->fetch();
		$stmtQty->close();

		if (!$totalQuantity) {
			$totalQuantity = 0;
		}
	} else {
		$stmtCart->close();
	}
}

if ($transactionId) {
	$orderQuery = "SELECT * FROM transactions WHERE transaction_id = ? LIMIT 1";
	$stmt = $conn->prepare($orderQuery);
	$stmt->bind_param("i", $transactionId);
} else {
	$orderQuery = "
		SELECT * FROM transactions
		WHERE customer_id = ?
		ORDER BY transaction_date DESC
		LIMIT 1
	";
	$stmt = $conn->prepare($orderQuery);
	$stmt->bind_param("i", $user['customer_id']);
}
$stmt->execute();
$orderResult = $stmt->get_result();
$order = $orderResult->fetch_assoc();
$stmt->close();

$cartItems = [];
if ($order) {
	$txId = $order['transaction_id'];

	$itemQuery = "
		SELECT p.name,
			   ti.price        AS unit_price,
			   ti.quantity,
			   (ti.price * ti.quantity) AS subtotal
		FROM transaction_items ti
		JOIN products p ON ti.product_id = p.product_id
		WHERE ti.transaction_id = ?
	";
	$stmt = $conn->prepare($itemQuery);
	$stmt->bind_param("i", $txId);
	$stmt->execute();
	$itemsResult = $stmt->get_result();

	while ($row = $itemsResult->fetch_assoc()) {
		$cartItems[] = $row;
	}
	$stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
	<meta charset="UTF-8" />
	<meta name="viewport" content="width=device-width, initial-scale=1.0" />
	<title>Thank You</title>
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

		.custom-card {
			background-color: var(--light-gray);
			font-size: 2.25rem !important;
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
							<a class="nav-link" href="offers.php"><i class="fa-solid fa-briefcase"></i>WHAT WE OFFER</a>
						</li>
						<li class="nav-item">
							<a class="nav-link" href="about.php"><i class="fa-solid fa-users"></i>ABOUT US</a>
						</li>
						<a class="nav-link" href="cart.php">
							<i class="fa-solid fa-cart-shopping"></i>CART
							<?php if (isset($_SESSION['customer_id']) && $totalQuantity > 0): ?>
								<span class="badge bg-danger rounded-pill ms-1"><?= $totalQuantity ?></span>
							<?php endif; ?>
						</a>

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

		<!-- Body Section -->
		<section class="profile-section d-flex flex-column align-items-center justify-content-center">
			<div class="profile-wrapper text-start fs-5">
				<div class="text-center mt-5 p-5 bg-light rounded shadow">
					<?php $full_name = explode(' ', $user['full_name']); ?>
					<h1 class="mb-3">
						<i class="fa-solid fa-check-circle" style="color: var(--teal);"></i>
						Thank You for Your Purchase, <i><?= htmlspecialchars($full_name[0]); ?>!</i>
					</h1>
					<p class="lead">We appreciate your trust in Playverse. You'll receive a confirmation email shortly.</p>
				</div>

				<div class="card p-5 my-5 rounded shadow fs-5">
					<h2 class="mb-3">Order Confirmation</h2>
					<p class="lead text-success"><strong>Your order has been placed successfully!</strong></p>

					<?php if ($order): ?>
						<div class="row mb-3">
							<div class="col-md-6"><strong>Transaction ID:</strong> <?= $order['transaction_id']; ?></div>
							<div class="col-md-6"><strong>Payment Mode:</strong> <?= $order['mode_of_payment']; ?></div>
							<div class="col-md-6"><strong>Date:</strong> <?= date("F j, Y", strtotime($order['transaction_date'])); ?></div>
							<div class="col-md-6"><strong>Total Amount:</strong> ₱<?= number_format($order['total_amount'], 2); ?></div>
							<div class="col-md-6"><strong>Shipping Address:</strong> <?= htmlspecialchars($order['shipping_address']); ?></div>

						</div>

						<hr class="my-4" />

						<h2 class="mb-3">Order Summary</h2>
						<div class="table-responsive">
							<table class="table table-bordered table-striped">
								<thead class="table-light">
									<tr>
										<th>Product Name</th>
										<th>Price</th>
										<th>Quantity</th>
										<th>Subtotal</th>
									</tr>
								</thead>
								<tbody>
									<?php foreach ($cartItems as $item): ?>
										<tr>
											<td><?= htmlspecialchars($item['name']); ?></td>
											<td>₱<?= number_format($item['unit_price'], 2); ?></td>
											<td><?= $item['quantity']; ?></td>
											<td>₱<?= number_format($item['subtotal'], 2); ?></td>
										</tr>
									<?php endforeach; ?>
								</tbody>

							</table>
						</div>
					<?php else: ?>
						<p class="text-danger">No recent order found.</p>
					<?php endif; ?>

					<hr class="my-4" />

					<div class="d-flex justify-content-end gap-3">
						<a href="index.php" class="cta-button"
							style="background-color: var(--navy); border-color: var(--navy); color: var(--teal); padding: 0.4rem 1.4rem; font-size: 1rem; border-width: 3px;">
							<i class="fa-solid fa-house"></i> Go to Homepage
						</a>
						<a href="offers.php" class="cta-button"
							style="padding: 0.4rem 1.4rem; font-size: 1rem; border-width: 3px;">
							<i class="fa-solid fa-tags"></i> Browse More Offers
						</a>
					</div>


				</div>
			</div>
		</section>

		<!-- Footer Section -->
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