<?php
session_start();
if (!isset($_SESSION['user'])) {
    header("Location: SignIn.php");
    exit;
}

echo "<h2>Welcome to the Dashboard!</h2>";
echo "<p>Hello, " . $_SESSION['user'] . "</p>";
echo "<a href='Logout.php'>Logout</a>";
?>