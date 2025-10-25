<?php
// One-off script to create an admin user. Delete this file immediately after use.
require __DIR__ . '/config/db.php';

$name = 'SUPISI';
$email = 'admin@gmail.com';
$password = 'admin123';
$role = 'admin';

// Defensive: check if user already exists
$check = $con->prepare("SELECT id FROM users WHERE email = ?");
$check->bind_param("s", $email);
$check->execute();
$res = $check->get_result();
if ($res && $res->num_rows > 0) {
    echo "User with email {$email} already exists.\n";
    exit(0);
}

$hash = password_hash($password, PASSWORD_BCRYPT);

$stmt = $con->prepare("INSERT INTO users (name, email, password_hash, role) VALUES (?, ?, ?, ?)");
$stmt->bind_param("ssss", $name, $email, $hash, $role);

if ($stmt->execute()) {
    echo "Admin user created successfully: {$email}\n";
} else {
    echo "Error creating admin: " . $stmt->error . "\n";
}

$stmt->close();
$check->close();

// Reminder for the operator
echo "IMPORTANT: Delete this file (create_admin.php) now to avoid leaving credentials on disk.\n";

?>