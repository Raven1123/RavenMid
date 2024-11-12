<?php
session_start();
include 'db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['signin'])) {
    
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Prepare SQL query to check if the user exists
    $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

         if ($user) {
            // Check if the user is locked
            $now = new DateTime();
            $locked_until = $user['locked_until'] ? new DateTime($user['locked_until']) : null;

            // If the account is locked, check the time difference
            if ($locked_until && $locked_until > $now) {
                // Calculate the remaining lock time
                $lock_time = $locked_until->diff($now);
                $seconds_left = $lock_time->s + ($lock_time->i * 60) + ($lock_time->h * 3600);
                echo "<script>alert('Account locked. Please try again after " . (20 - $seconds_left) . " seconds.');</script>";
            
            } else {
                // Verify password
                if (password_verify($password, $user['password'])) {
                    $_SESSION['user'] = $user['email'];
                    // Reset attempt count
                    $conn->query("UPDATE users SET attempt_count = 0 WHERE email = '$email'");
                    echo "<script>window.location.href = 'dashboard.php';</script>";
                } else {
                    // Update attempt count and lock account after 3 failed attempts
                    $attempt_count = $user['attempt_count'] + 1;
                    if ($attempt_count >= 3) {
                        $locked_until = new DateTime();
                        $locked_until->modify("+20 seconds");
                        $conn->query("UPDATE users SET attempt_count = 0, locked_until = '" . $locked_until->format('Y-m-d H:i:s') . "' WHERE email = '$email'");
                        echo "<script>alert('Account locked due to too many failed attempts. Please try again later.');</script>";
                    } else {
                        $conn->query("UPDATE users SET attempt_count = $attempt_count WHERE email = '$email'");
                        echo "<script>alert('Incorrect password.');</script>";
                    }
                }
            }
        } else {
            echo "<script>alert('No user found with this email.');</script>";
        }
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign In</title>
    <style>
       body {
    font-family: Arial, sans-serif;
    background:linear-gradient(to right, #FF69B4, #0000FF);
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
    width: 350px;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    text-align: center;
    margin-right: 20px; /* Adds some space from the right edge */
}
        h2 {
            color: #444;
            margin-bottom: 15px;
        }
        input[type="email"], input[type="password"] {
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
        <h2>Sign In</h2>
        <form action="signin.php" method="post">
            <input type="email" name="email" placeholder="Email" required>
            <input type="password" name="password" placeholder="Password" required>
            <button type="submit" name="signin">Sign In</button>
        </form>
        <p class="switch-link">Don't have an account? <a href="signup.php">Sign Up</a></p>
    </div>
</body>
</html>