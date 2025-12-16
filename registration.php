<?php

$error = '';
$success = '';

if (isset($_POST['register'])) {

    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    if (empty($name) || empty($email) || empty($password) || empty($confirm_password)) {
        $error = "All fields are required!";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Invalid email format!";
    } elseif (strlen($password) < 8 || !preg_match("/[\W]/", $password)) {
        $error = "Password must be at least 8 characters and contain a special character!";
    } elseif ($password !== $confirm_password) {
        $error = "Passwords do not match!";
    } else {

        $file = 'users.json';
        $users = [];

        if (file_exists($file)) {
            $json_data = file_get_contents($file);
            $users = json_decode($json_data, true);
            if (!is_array($users)) {
                $users = [];
            }
        }

        // Check if email already exists
        foreach ($users as $user) {
            if ($user['email'] === $email) {
                $error = "Email is already registered!";
                break;
            }
        }

        if (!$error) {
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);

            $users[] = [
                'name' => $name,
                'email' => $email,
                'password' => $hashed_password
            ];

            if (file_put_contents($file, json_encode($users, JSON_PRETTY_PRINT))) {
                $success = "Registration successful!";
                $name = $email = ""; // clear form
            } else {
                $error = "Failed to save user data!";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>User Registration</title>
</head>
<body>

<h2>User Registration Form</h2>

<?php
if ($error) {
    echo "<p style='color:red;'>$error</p>";
} elseif ($success) {
    echo "<p style='color:green;'>$success</p>";
}
?>

<form method="POST">
    <label>Name:</label><br>
    <input type="text" name="name" value="<?= htmlspecialchars($name ?? '') ?>"><br><br>

    <label>Email:</label><br>
    <input type="email" name="email" value="<?= htmlspecialchars($email ?? '') ?>"><br><br>

    <label>Password:</label><br>
    <input type="password" name="password"><br><br>

    <label>Confirm Password:</label><br>
    <input type="password" name="confirm_password"><br><br>

    <button type="submit" name="register">Register</button>
</form>

</body>
</html>
