<?php

$firstName = "";

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

  $firstName = trim($_POST["firstname"]);

  // Regex: Only letters and spaces, 2 to 50 characters

  if (!preg_match("/^[A-Za-z\s]{1,3}$/", $firstName)) {

    $message = "<div class='alert alert-danger'>Invalid first name. Only letters and spaces (1–3 characters) allowed.</div>";

  }

}

?>

<!DOCTYPE html>

<html lang="en">

<head>

  <meta charset="UTF-8">

  <title>First Name Validation</title>

  <!-- Bootstrap CSS -->

  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

  <style>

    /* Make the footer stick to the bottom */

    body {

      display: flex;

      flex-direction: column;

      min-height: 100vh;

    }

    main {

      flex: 1;

    }

    footer {

      position: relative;

      bottom: 0;

      width: 100%;

    }

  </style>

</head>

<body class="bg-light">

  <!-- Header -->

  <header class="bg-primary text-white text-center p-3">

    <h1>Customer Form</h1>

  </header>

  <!-- Main Body -->

  <main class="container my-4">

    <h2 class="mb-3">Enter Your First Name</h2>

    <form method="POST" class="p-3 bg-white rounded shadow-sm">

      <div class="mb-3">

        <label for="firstname" class="form-label">First Name:</label>

        <input type="text" name="firstname" id="firstname" class="form-control" required>

      </div>

      <button type="submit" class="btn btn-primary">Submit</button>

    </form>

  </main>

  <!-- Message Area -->

  <div class="container mb-4">

    <?php echo $message; ?>

  </div>

  <!-- Sticky Footer -->

  <footer class="bg-dark text-white text-center p-3 mt-auto">

    CIP 1102 | Summer 2025 &copy;

  </footer>

</body>

</html>