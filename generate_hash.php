<?php
// --- Password Hashing Tool ---

// 1. Set the password you want to hash below.
$passwordToHash = 'securepass';

// 2. This line will generate a secure, random hash for that password.
$hashedPassword = password_hash($passwordToHash, PASSWORD_DEFAULT);

// 3. This will display the generated hash on the screen.
echo "Password to hash: " . htmlspecialchars($passwordToHash) . "<br><br>";
echo "Generated Hash: <br>";
// Use a textarea to make it easy to copy the full hash.
echo '<textarea rows="2" cols="80" readonly>' . htmlspecialchars($hashedPassword) . '</textarea>';

?>
