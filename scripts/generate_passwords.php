 
<?php
// generate_passwords.php

// test-use same password
$password = "password123";

$users = ['alice', 'bob', 'charlie', 'editor1', 'journalist1', 'user1'];

foreach ($users as $index => $user) {
    // radom
    $salt = bin2hex(random_bytes(16));
    
    $hashedPassword = password_hash($password . $salt, PASSWORD_BCRYPT);
    
    // output
    echo "User: $user\n";
    echo "Salt: $salt\n";
    echo "Hashed Password: $hashedPassword\n";
    echo "SQL: ('$user', '$hashedPassword', '$salt', " . ($user === 'editor1' ? 2 : ($user === 'alice' || $user === 'journalist1' ? 1 : 0)) . "),\n\n";
}
?>