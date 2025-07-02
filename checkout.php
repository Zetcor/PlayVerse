<?php
session_start();

$conn = new mysqli("localhost", "root", "", "PlayVerse");
if ($conn->connect_error) {
	die("Connection failed: " . $conn->connect_error);
}

$customer      = null;
$cart_items    = [];
$totalPrice    = 0;
$totalQuantity = 0;
$cart_id       = null;

if (
	$_SERVER["REQUEST_METHOD"] === "POST" &&
	isset($_SESSION['customer_id']) &&
	isset($_POST['place_order'])
) {
	$customer_id      = $_SESSION['customer_id'];
	$cart_id          = (int)$_POST['cart_id'];
	$shipping_address = $_POST['shipping_address'];
	$total_amount     = $_POST['total_amount'];
	$payment_method   = $_POST['payment_method'];
	$transaction_date = date('Y-m-d');

	$stmt = $conn->prepare("
        INSERT INTO transactions
              (customer_id, cart_id, shipping_address,
               total_amount, mode_of_payment, transaction_date)
        VALUES (?, ?, ?, ?, ?, ?)
    ");
	$stmt->bind_param(
		"iisdss",
		$customer_id,
		$cart_id,
		$shipping_address,
		$total_amount,
		$payment_method,
		$transaction_date
	);

	if ($stmt->execute()) {
		$transaction_id = $stmt->insert_id;

		$cart_items = [];
		$ci = $conn->prepare("
            SELECT ci.product_id, ci.quantity, p.price
            FROM cart_items ci
            JOIN products p ON ci.product_id = p.product_id
            WHERE ci.cart_id = ?
        ");
		$ci->bind_param("i", $cart_id);
		$ci->execute();
		$res = $ci->get_result();
		while ($row = $res->fetch_assoc()) {
			$cart_items[] = $row;
		}
		$ci->close();

		$ti = $conn->prepare("
            INSERT INTO transaction_items
                   (transaction_id, product_id, quantity, price)
            VALUES (?, ?, ?, ?)
        ");
		foreach ($cart_items as $item) {
			$ti->bind_param(
				"iiid",
				$transaction_id,
				$item['product_id'],
				$item['quantity'],
				$item['price']
			);
			$ti->execute();
		}
		$ti->close();

		$clear = $conn->prepare("DELETE FROM cart_items WHERE cart_id = ?");
		$clear->bind_param("i", $cart_id);
		$clear->execute();
		$clear->close();

		echo "<script>
                alert('Order placed successfully!');
                window.location.href='thankyou.php?transaction_id={$transaction_id}';
              </script>";
		exit;
	}
}

if (isset($_SESSION['customer_id'])) {
	$customer_id = $_SESSION['customer_id'];

	$stmt = $conn->prepare("SELECT * FROM customers WHERE customer_id = ?");
	$stmt->bind_param("i", $customer_id);
	$stmt->execute();
	$customer = $stmt->get_result()->fetch_assoc();
	$stmt->close();

	$stmt = $conn->prepare("SELECT cart_id FROM cart WHERE customer_id = ?");
	$stmt->bind_param("i", $customer_id);
	$stmt->execute();
	$stmt->bind_result($cart_id);
	if (!$stmt->fetch()) {
		$stmt->close();
		$stmt = $conn->prepare("INSERT INTO cart (customer_id) VALUES (?)");
		$stmt->bind_param("i", $customer_id);
		$stmt->execute();
		$cart_id = $stmt->insert_id;
	}
	$stmt->close();

	$stmt = $conn->prepare("
        SELECT ci.product_id, ci.quantity, p.name, p.price, p.image
        FROM cart_items ci
        JOIN products p ON ci.product_id = p.product_id
        WHERE ci.cart_id = ?
    ");
	$stmt->bind_param("i", $cart_id);
	$stmt->execute();
	$resItems = $stmt->get_result();
	while ($row = $resItems->fetch_assoc()) {
		$row['subtotal'] = $row['price'] * $row['quantity'];
		$totalPrice     += $row['subtotal'];
		$cart_items[]    = $row;
	}
	$stmt->close();

	$totalQuantity = array_sum(array_column($cart_items, 'quantity'));
	$ship_address  = "{$customer['street']}, {$customer['city']}, " .
		"{$customer['province']}, {$customer['country']} " .
		"{$customer['zipcode']}";
}
?>


<!DOCTYPE html>
<html lang="en">

<head>
	<meta charset="UTF-8" />
	<meta name="viewport" content="width=device-width, initial-scale=1.0" />
	<title>Checkout</title>
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
	<link rel="stylesheet" href="checkout.css" />

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

	<!-- Checkout Section -->
	<!-- Checkout Section -->
	<section class="checkout-section py-5">
		<div class="container">
			<h2 class="text-center display-5 fw-bold mb-5" style="color:#8f43ec;">CHECKOUT</h2>

			<form method="POST" action="">
				<div class="row g-5">
					<!-- Customer Info -->
					<div class="col-md-7">
						<div class="bg-white p-4 rounded shadow">
							<h2 style="color:#0a1128;">Customer Information</h2>

							<input type="hidden" name="cart_id" value="<?= $cart_id ?>">
							<input type="hidden" name="total_amount" value="<?= number_format($totalPrice + ($totalPrice * 0.12), 2, '.', '') ?>">

							<div class="mb-3">
								<label for="name" class="form-label">Full Name</label>
								<input type="text" class="form-control" id="name" value="<?= htmlspecialchars($customer['full_name'] ?? '') ?>" readonly />
							</div>
							<div class="mb-3">
								<label for="email" class="form-label">Email Address</label>
								<input type="email" class="form-control" id="email" value="<?= htmlspecialchars($customer['email'] ?? '') ?>" readonly />
							</div>
							<div class="mb-3">
								<label for="phone" class="form-label">Phone Number</label>
								<input type="tel" class="form-control" id="phone" value="<?= htmlspecialchars($customer['contact_no'] ?? '') ?>" readonly />
							</div>

							<div class="mb-3">
								<label for="shippingadd" class="form-label">Shipping Address</label>
								<textarea class="form-control" name="shipping_address" id="shippingadd" rows="2"><?= htmlspecialchars($ship_address) ?></textarea>
							</div>

							<h2 class="mt-4" style="color:#0a1128;">Payment Method</h2>
							<select name="payment_method" class="form-select mb-3" required>
								<option value="" disabled selected>Select method</option>
								<option value="Credit/Debit">Credit/Debit Card</option>
								<option value="Paypal">PayPal</option>
								<option value="Gcash">GCash</option>
							</select>
						</div>
					</div>

					<!-- Order Summary -->
					<div class="col-md-5">
						<div class="bg-white p-4 rounded shadow">
							<h2 style="color:#0a1128;">Order Summary</h2>

							<?php if (count($cart_items)): ?>
								<?php foreach ($cart_items as $item): ?>
									<div class="d-flex align-items-center mb-3">
										<div class="summary-img-box me-3">
											<img src="imgs/<?= htmlspecialchars($item['image']) ?>" class="summary-img">
										</div>
										<div class="flex-grow-1 d-flex justify-content-between">
											<span><?= htmlspecialchars($item['name']) ?> (x<?= $item['quantity'] ?>)</span>
											<span>₱<?= number_format($item['subtotal'], 2) ?></span>
										</div>
									</div>
								<?php endforeach; ?>

								<hr>
								<div class="d-flex justify-content-between fs-6">
									<span>Subtotal</span>
									<span>₱<?= number_format($totalPrice, 2) ?></span>
								</div>
								<div class="d-flex justify-content-between fs-6">
									<?php $vat = $totalPrice * 0.12; ?>
									<span>VAT(12%)</span>
									<span>₱<?= number_format($vat, 2) ?></span>
								</div>
								<hr>
								<div class="d-flex justify-content-between fw-bold fs-4 mt-4">
									<span>Total</span>
									<span class="text-success">₱<?= number_format($totalPrice + $vat, 2) ?></span>
								</div>

								<button type="submit" name="place_order" class="btn cta-cart w-100 mt-4">Place Order</button>

							<?php else: ?>
								<div class="alert alert-warning">Your cart is empty.</div>
							<?php endif; ?>
						</div>
					</div>
				</div>
			</form>
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