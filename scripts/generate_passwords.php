 
<?php
// generate_passwords.php

// test-use same password
$password = "password123";

$userList = [
    'user' => [
        'username' => ['user1', 'user2', 'user3'],
        'fullname' => ['Dominique Pickett', 'Boris Harding', 'Nasim Silva'],
        'privilege' => 0
    ],
    'journalist' => [
        'username' => ['journalist1', 'journalist2', 'journalist3'],
        'fullname' => ['Vance Murray', 'Joel Henry', 'Skyler Cotton'],
        'privilege' => 1
    ],
    'editor' => [
        'username' => ['editor1', 'editor2', 'editor3'],
        'fullname' => ['Dorian Sanders', 'Flavia Kaufman', 'Rudyard Burns'],
        'privilege' => 2
    ]
];

foreach ($userList as $idx => $_) {
    for ($i = 0; $i < 3; $i++) {
        // radom
        $salt = bin2hex(random_bytes(16));

        $hashedPassword = password_hash($password . $salt, PASSWORD_BCRYPT);

        // output
        echo "User: {$userList[$idx]['username'][$i]}\n";
        echo "Salt: $salt\n";
        echo "Hashed Password: $hashedPassword\n";
        echo "SQL: ('{$userList[$idx]['username'][$i]}', '{$userList[$idx]['fullname'][$i]}', '$hashedPassword', '$salt', '{$userList[$idx]['privilege']}') \n\n";
    }
}
?>
