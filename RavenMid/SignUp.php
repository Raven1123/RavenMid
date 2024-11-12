<?php
session_start();
include 'db.php'; 

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['signup'])) {
    // Collect form data
    $email = $_POST['email'];
    $password = $_POST['password'];
    $first_name = $_POST['first_name'];
    $last_name = $_POST['last_name'];
    $organization = $_POST['organization'];
    $position = $_POST['position'];
    $address = $_POST['address']; // Collect address
    // Check if the email already exists in the database
    $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        echo "<script>alert('Email is already taken!');</script>";
    } else {
        // Hash the password before saving it to the database
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        // Insert new user into the database
        $stmt = $conn->prepare("INSERT INTO users (email, password, first_name, last_name, organization, position, address) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("sssssss", $email, $hashed_password, $first_name, $last_name, $organization, $position, $address);

        if ($stmt->execute()) {
            echo "<script>alert('Sign up successful!'); window.location.href = 'signin.php';</script>";
        } else {
            echo "<script>alert('Error: " . $stmt->error . "');</script>";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Sign Up</title>
        <style>
            body {
                font-family: Arial, sans-serif;
                background: linear-gradient(to right, #FF69B4, #0000FF);
                display: flex;
                justify-content: flex-end;
                align-items: center;
                height: 100vh;
                margin: 0;
            }
            .container {
                background: #ffffff;
                padding: 30px;
                border-radius: 8px;
                width: 500px; /* Increased width to 500px */
                box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
                text-align: center;
            }
            h2 {
                color: #444;
                margin-bottom: 15px;
            }
            input[type="email"], input[type="password"], input[type="text"], input[type="tel"] {
                width: 100%;
                padding: 10px;
                margin: 10px 0;
                border-radius: 5px;
                border: 1px solid #ddd;
                font-size: 14px;
            }
            button {
                width: 100%;
                padding: 12px;
                background-color: #4CAF50;
                color: #fff;
                border: none;
                border-radius: 5px;
                cursor: pointer;
                font-size: 16px;
                margin-top: 10px;
            }
            .switch-link {
                color: #4CAF50;
                cursor: pointer;
                margin-top: 10px;
                display: inline-block;
            }
        </style>
    </head>
    <body>
        <div class="container">
            <h2>Sign Up</h2>
            <form action="signup.php" method="post">
                <input type="text" name="first_name" placeholder="First Name" required>
                <input type="text" name="last_name" placeholder="Last Name" required>
                <input type="email" name="email" placeholder="Email" required>
                <input type="password" name="password" placeholder="Password" required>
                <input type="text" name="organization" placeholder="Organization" required>
                <input type="text" name="position" placeholder="Position" required>
                <input type="text" name="address" placeholder="Address" required> 
                <button type="submit" name="signup">Sign Up</button>
            </form>
            <p class="switch-link">Already have an account? <a href="signin.php">Sign In</a></p>
        </div>
    </body>
</html>