<?php
session_start();

if (!isset($_SESSION['customer_id'])) {
	header("Location: login.php");
	exit();
}

$customer_id = $_SESSION['customer_id'];

$conn = new mysqli("localhost", "root", "", "PlayVerse");
if ($conn->connect_error) {
	die("Connection failed: " . $conn->connect_error);
}

// ✅ Get username for navbar
$username = '';
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

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['remove_item'], $_POST['product_id'])) {
	$product_id = intval($_POST['product_id']);
	$stmtRemove = $conn->prepare("DELETE FROM cart_items WHERE cart_id = ? AND product_id = ?");
	$stmtRemove->bind_param("ii", $cart_id, $product_id);
	$stmtRemove->execute();
	$stmtRemove->close();

	header("Location: cart.php");
	exit();
}

if (isset($_GET['add']) || isset($_GET['sub'])) {
	$changeProductId = isset($_GET['add']) ? intval($_GET['add']) : intval($_GET['sub']);
	$changeType = isset($_GET['add']) ? 'add' : 'sub';

	$stmtQty = $conn->prepare("SELECT quantity FROM cart_items WHERE cart_id = ? AND product_id = ?");
	$stmtQty->bind_param("ii", $cart_id, $changeProductId);
	$stmtQty->execute();
	$stmtQty->bind_result($currentQty);
	if ($stmtQty->fetch()) {
		$stmtQty->close();

		$newQty = $changeType === 'add' ? $currentQty + 1 : max(1, $currentQty - 1);

		$stmtUpdate = $conn->prepare("UPDATE cart_items SET quantity = ? WHERE cart_id = ? AND product_id = ?");
		$stmtUpdate->bind_param("iii", $newQty, $cart_id, $changeProductId);
		$stmtUpdate->execute();
		$stmtUpdate->close();
	} else {
		$stmtQty->close();
	}

	header("Location: cart.php#product-$changeProductId");
	exit();
}

$totalQuantity = 0;
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

$cart_items = [];
$totalPrice = 0;

$sqlItems = "
	SELECT 
		p.product_id,
		p.name,
		p.price,
		p.description,
		p.category,
		p.rating,
		p.date_added,
		p.image,
		ci.quantity
	FROM cart_items ci
	JOIN products p ON ci.product_id = p.product_id
	WHERE ci.cart_id = ?
";

$stmtItems = $conn->prepare($sqlItems);
$stmtItems->bind_param("i", $cart_id);
$stmtItems->execute();
$resultItems = $stmtItems->get_result();

while ($row = $resultItems->fetch_assoc()) {
	$cart_items[] = $row;
	$totalPrice += $row['price'] * $row['quantity'];
}

$stmtItems->close();
?>


<!DOCTYPE html>
<html lang="en">

<head>
	<meta charset="UTF-8" />
	<meta name="viewport" content="width=device-width, initial-scale=1.0" />
	<title>Shopping Cart</title>
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
	<link rel="stylesheet" href="cart.css" />
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

		.cart-section {
			background-color: var(--light-gray);
			min-height: 100vh;
		}

		.cart-img {
			width: 140px;
			height: 140px;
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

		.quantity-wrapper {
			margin-top: 0.5rem;
			width: 140px;
		}

		.input-group input[type='number']::-webkit-inner-spin-button,
		.input-group input[type='number']::-webkit-outer-spin-button {
			-webkit-appearance: none;
			margin: 0;
		}

		[id] {
			scroll-margin-top: 100px;
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
						<a class="nav-link" href="offers.php"><i class="fa-solid fa-briefcase"></i>WHAT WE OFFER</a>
					</li>
					<li class="nav-item">
						<a class="nav-link" href="about.php"><i class="fa-solid fa-users"></i>ABOUT US</a>
					</li>
					<li class="nav-item">
						<a class="nav-link active" aria-current="page" href="cart.php">
							<i class="fa-solid fa-cart-shopping"></i>CART
							<?php if ($totalQuantity > 0): ?>
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

	<section class="cart-section py-5">
		<div class="container">
			<div class="text-center mb-4">
				<h2 class="display-5 fw-bold" style="color: #8f43ec">SHOPPING CART</h2>
			</div>

			<?php if (count($cart_items) > 0): ?>
				<?php foreach ($cart_items as $item): ?>
					<div
						id="product-<?= $item['product_id'] ?>"
						class="cart-item d-flex flex-column flex-md-row align-items-center gap-4 p-4 mb-4 bg-white shadow rounded">

						<img src="imgs/<?= htmlspecialchars($item['image']) ?>"
							alt="<?= htmlspecialchars($item['name']) ?>" class="cart-img" />

						<div class="flex-grow-1">
							<h2 class="mb-2" style="color: var(--navy)"><?= htmlspecialchars($item['name']) ?></h2>
							<p class="mb-1"><strong>Price:</strong> ₱<?= number_format($item['price'], 2) ?></p>
							<div class="quantity-wrapper">
								<label class="form-label mb-1"><strong>Quantity:</strong></label>
								<div class="input-group input-group-sm justify-content-center">
									<a href="?sub=<?= $item['product_id'] ?>#product-<?= $item['product_id'] ?>"
										class="btn btn-outline-dark py-0 px-3 fs-5">-</a>
									<span class="form-control text-center border-dark"><?= $item['quantity'] ?></span>
									<a href="?add=<?= $item['product_id'] ?>#product-<?= $item['product_id'] ?>"
										class="btn btn-outline-dark py-0 px-3 fs-5">+</a>
								</div>
							</div>
						</div>

						<form method="POST" action="cart.php" class="d-inline">
							<input type="hidden" name="product_id" value="<?= $item['product_id'] ?>">
							<button type="submit" name="remove_item" class="btn btn-danger btn-sm mt-3 mt-md-0">
								<i class="fa-solid fa-trash"></i> Remove
							</button>
						</form>
					</div>
				<?php endforeach; ?>
			<?php else: ?>
				<div class="alert alert-info text-center">Your cart is empty.</div>
			<?php endif; ?>

			<!-- Total and Checkout -->
			<div class="text-end mt-4">
				<h1 class="text-success mt-2">Subtotal: ₱<?= number_format($totalPrice, 2) ?></h1>
				<?php if ($totalPrice > 0): ?>
					<a href="checkout.php" class="cta-cart mt-2 d-inline-block">Proceed to Checkout</a>
				<?php endif; ?>
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