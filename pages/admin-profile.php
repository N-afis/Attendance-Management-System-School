<?php

require_once '../config/config.php';
require_once '../config/database.php';

requiredLoggeIn();

$img_profile = $_SESSION['img_path'];
$fullName = $_SESSION['full_name'];
$email = $_SESSION['email'];
$userId = $_SESSION['user_id'];

$error = '';

$db = new Database();
$pdo = $db->getConnection();

include_once '../includes/header.php';

?>

<h1 class="fs-4 fw-bold">Profile</h1>

<form action="admin-profile.php" method="POST" enctype="multipart/form-data">
    <div class="mb-5 p-2 d-flex flex-column">
        <div class="d-flex gap-2 mb-2 mb-md-4 w-100 h-50" >
            <div class="profile-image-container">
                <img class="imgPro" src="<?php echo $img_profile; ?>" alt="img">
            </div>

            <div class="img-list" id="imgList">
                <div class="d-flex flex-column gap-1">
                    <span id="removeImg">Remove image</span>
                    <hr class="w-100">
                    <span id="downloadImg">Download image</span>
                    <label id="changeImg">Change Image
                        <input type="file" name="changedImg" id="changedImg" accept="image/*" hidden>
                    </label>
                </div>
            </div>
        </div>
        <h1 class="fw-bold fs-2 mb-2"><?php echo $fullName ?></h1>
        <hr>
        <div class="my-3">
            <p class="text-muted text-decoration-underline mb-3">Admin Info:</p>
            <div class="d-flex gap-5">
                <p>Email :</p>
                <p id="adminEmail"><?php echo $email; ?></p>
            </div>
        </div>
    </div>
</form>

<?php include_once '../includes/toast.php' ?>

<script type="module" src="../assets/js/profile.js"></script>