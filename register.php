<?php
include('db.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $full_name = $_POST['full_name'];
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Hash the password for security
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    $sql = "INSERT INTO users (full_name, email, password) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sss", $full_name, $email, $hashed_password);
    
    if ($stmt->execute()) {
        echo "Registration successful. <a href='login.php'>Login here</a>";
    } else {
        echo "Error: " . $stmt->error;
    }
    $stmt->close();
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
    <link rel="stylesheet" href="css/register.css">
</head>
<body>
    <section class="hero">
        <img src="images/bg1.png" alt="Background Image" class="background-image">
    </section>

    <div class="register-container">
    <form action="register.php" method="POST">
        <h2>Register</h2>
        <div class="input-group">
            <input type="text" name="full_name" placeholder="Full Name" required>
        </div>
        <div class="input-group">
            <input type="email" name="email" placeholder="Email" required>
        </div>
        <div class="input-group">
            <input type="password" name="password" placeholder="Password" required>
            </div>
            <button type="submit" class="register-btn">Sign Up</button>
        </form>
        <p class="login-link">Already have an account? <a href="login.php">Login</a></p>
    </div>
</body>
</html>
