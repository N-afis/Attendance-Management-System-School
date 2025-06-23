<?php

header('Content-Type: application/json');

require_once '../../config/config.php';
require_once '../../config/database.php';
require_once '../../classes/Profile.php';

$db = new Database();
$pdo = $db->getConnection();

$profile = new Profile($pdo);

$email = $_POST['email'] ?? '';
$file = $_FILES['changedImg'] ?? null;

if (!$email || !$file) {
    echo json_encode(['success' => false, 'message' => 'Missing file or email']);
    exit;
}

$allowedTypes = ['image/jpeg', 'image/png', 'image/webp'];
if (!in_array($file['type'], $allowedTypes)) {
    echo json_encode(['success' => false, 'message' => 'Invalid image type']);
    exit;
}

// Fetch old image path

$user = $profile->getByEmail($email);

// Delete old image if it exists
if ($user && !empty($user['img_path'])) {
    $defaultImage = '../assets/images/img_user.png';
    $oldPath = '../' . ltrim($user['img_path'], '/');
    if (file_exists($oldPath) && $user['img_path'] !== $defaultImage) {
        unlink($oldPath);
    }
}

// Save new image
$ext = pathinfo($file['name'], PATHINFO_EXTENSION);
$newName = uniqid() . '.' . $ext;
$uploadPath = '../../uploads/profilePicture/' . $newName;
move_uploaded_file($file['tmp_name'], $uploadPath);

// Store relative path in DB
$relativePath = '../uploads/profilePicture/' . $newName;

$profile->email = $email;
$profile->img_path = $relativePath;
$profile->change();

// Update session
if (isset($_SESSION['email']) && $_SESSION['email'] === $email) {
    $_SESSION['img_path'] = $relativePath;
}

echo json_encode([
    'success' => true,
    'message' => 'Profile image updated successfully.',
    'new_img_path' => $relativePath
]);
