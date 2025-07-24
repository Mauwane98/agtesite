<?php
// Start a session to keep the user logged in after they sign in.
session_start();

// --- IMPORTANT SECURITY NOTE ---
// In a real-world application, you would connect to a secure database like MySQL 
// to store and retrieve user information. For this example, we are using a simple
// array to demonstrate the logic.

// To generate a secure password hash for this array, you can use the following code:
// echo password_hash('YourPassword123', PASSWORD_DEFAULT);
// Run this in a separate PHP file to get the hash, then copy it here.

$users = [
    // The username is the key, and the hashed password is the value.
    'admin' => '$2y$10$uU3xo9w1pS2bAyzQzuF22uSvWWx8qKDOTFreLCqHsnxVcristU9gGs', // Example hash for "password123"
    'susan' => '$2y$10$4hgtvRVA/iML2sG.JiKwAefdCEp0/nvXdaJlbRdcvDC/fekApp2xC'  // Example hash for "securepass"
];

// Retrieve the username and password submitted from the login form.
$username = $_POST['username'];
$password = $_POST['password'];

// Check if the submitted username exists in our array and if the submitted password
// matches the stored hash for that user.
if (isset($users[$username]) && password_verify($password, $users[$username])) {
    
    // If the password is correct, we set session variables to mark the user as logged in.
    $_SESSION['loggedin'] = true;
    $_SESSION['username'] = $username;
    
    // Redirect the user to the admin dashboard.
    // Note: For this to work, you must rename admin.html to admin.php
    header('Location: admin.php');
    exit;
    
} else {
    
    // If the username or password is incorrect, redirect the user back to the login page.
    // We can also add an error flag to the URL to display a message.
    header('Location: admin-login.html?error=1');
    exit;
}
?>
