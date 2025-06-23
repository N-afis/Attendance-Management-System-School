<?php

header('Content-Type: application/json');

require_once '../../config/config.php';
require_once '../../config/database.php';
require_once '../../classes/Profile.php';

$db = new Database();
$pdo = $db->getConnection();

$profile = new Profile($pdo);

// Read JSON body
$data = json_decode(file_get_contents('php://input'), true);
$email = $data['email'] ?? null;

if (!$email) {
    echo json_encode(['success' => false, 'message' => 'Email is required']);
    exit;
}

// Fetch user

$user = $profile->getByEmail($email);

if (!$user) {
    echo json_encode(['success' => false, 'message' => 'User not found']);
    exit;
}

// Delete old image if it exists
if ($user && !empty($user['img_path'])) {
    $defaultImage = '../assets/images/img_user.png';
    $oldPath = '../' . ltrim($user['img_path'], '/');
    if (file_exists($oldPath) && $user['img_path'] !== $defaultImage) {
        unlink($oldPath);
    }
}

// Update DB
$profile->email = $email;
$profile->delete();

if (!$profile->delete()) {
    echo json_encode(['success' => false, 'message' => 'Failed to delete profile image']);
    exit;
}

// Update session if needed
if ($_SESSION['email'] === $email) {
    $_SESSION['img_path'] = '../assets/images/img_user.png';
}

echo json_encode(['success' => true, 'message' => 'Profile image removed']);
