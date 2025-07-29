<?php
include('connection.php');

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $gmail = $_POST['email'];
    $password = $_POST['password'];
    $toa = isset($_POST['toa']) ? 1 : 0;
    $status = isset($_POST['status']) ? $_POST['status'] : 'user';

    if (!$toa) {
        header("Location: signup.php?msg=" . urlencode("You must agree to the Terms and Conditions."));
        exit;
    }

    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    $check = $conn->prepare("SELECT gmail FROM user WHERE gmail = ?");
    $check->bind_param("s", $gmail);
    $check->execute();
    $check->store_result();

    if ($check->num_rows > 0) {
        header("Location: ..section/Login.php?msg=" . urlencode("Email is already registered."));
        exit;
    }

    $stmt = $conn->prepare("INSERT INTO user (gmail, password, toa, status) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssis", $gmail, $hashedPassword, $toa, $status);

    if ($stmt->execute()) {
        header("Location: ..section/Login.php?msg=" . urlencode("Signup successful!"));
        exit;
    } else {
        header("Location: ..section/Login.php?msg=" . urlencode("Error during signup: " . $stmt->error));
        exit;
    }

    $check->close();
    $conn->close();
}
?>
