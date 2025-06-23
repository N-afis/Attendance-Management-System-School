<?php
require_once '../config/config.php';
require_once '../config/database.php';

$img_profile = $_SESSION['img_path'];

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo APP_NAME; ?></title>

    <link rel="icon" type="image/x-icon" href="<?php echo FAVICON ?>">

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta1/dist/css/bootstrap.min.css">

    <!-- Boxicons CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/boxicons@latest/css/boxicons.min.css">

    <!-- jQuery (JS) -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>

    <script src="../assets/js/sidebar.js"></script>

    <!-- Bootstrap Bundle (includes Popper) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta1/dist/js/bootstrap.bundle.min.js"></script>

    <!-- SheetJS Library (Excel) -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" integrity="sha512-Evv84Mr4kqVGRNSgIGL/F/aIDqQb7xQ2vcrdIwxfjThSH8CSR7PBEakCr51Ck+w+/U6swU2Im1vVX0SVk9ABhg==" crossorigin="anonymous" referrerpolicy="no-referrer" />

    <!-- Custom Sidebar CSS -->
    <link rel="stylesheet" href="../assets/css/sidebar.css">

    <link rel="stylesheet" href="../assets/css/students.css">

    <link rel="stylesheet" href="../assets/css/teacher.css">

    <link rel="stylesheet" href="../assets/css/profile.css">

    <link rel="stylesheet" href="../assets/css/dashboard.css">

    <link rel="stylesheet" href="../assets/css/attendance.css">

</head>


<body id="body-pd" class="bg-light">
    <header class="header" id="header">
        <div class="header_toggle"> <i class='bx bx-menu' id="header-toggle"></i> </div>
        <div class="header_img"> <a href="<?php echo BASE_URL; ?>/pages/admin-profile.php"><img src="<?php echo $img_profile ?>" alt="" id="headerImg"></a> </div>
    </header>
    <div class="l-navbar" id="nav-bar">
        <nav class="nav">
            <div>
                <!-- Logo Section -->
                <a href="<?php echo BASE_URL; ?>/pages/dashboard.php" class="nav_logo">
                    <img src="../assets/images/logoSchool.png" alt="logo">
                    <span class="nav_logo-name">CMC TTA</span>
                </a>

                <!-- Navigation Links -->
                <div class="nav_list">
                    <a
                        <?php echo (basename($_SERVER['PHP_SELF']) == 'dashboard.php' ? 'class="nav_link active"' : 'class="nav_link"'); ?>
                        href="<?php echo BASE_URL; ?>/pages/dashboard.php">
                        <i class='bx bx-home-alt nav_icon'></i>
                        <span class="nav_name">Dashboard</span>
                    </a>
                    <a
                        <?php echo (strpos($_SERVER['PHP_SELF'], '/pages/students.php') !== false ? 'class="nav_link active"' : 'class="nav_link"'); ?>
                        href="<?php echo BASE_URL; ?>/pages/students.php">
                        <i class="fa-solid fa-graduation-cap nav_icon"></i>
                        <span class="nav_name">Students</span>
                    </a>
                    <a
                        <?php echo (strpos($_SERVER['PHP_SELF'], '/pages/teachers.php') !== false ? 'class="nav_link active"' : 'class="nav_link"'); ?>
                        href="<?php echo BASE_URL; ?>/pages/teachers.php">
                        <i class="fa-solid fa-user-tie nav_icon"></i>
                        <span class="nav_name">Teachers</span>
                    </a>
                    <a
                        <?php echo (strpos($_SERVER['PHP_SELF'], '/pages/attendance.php') !== false ? 'class="nav_link active"' : 'class="nav_link"'); ?>
                        href="<?php echo BASE_URL; ?>/pages/attendance.php">
                        <i class="fa-regular fa-calendar-check nav_icon"></i>
                        <span class="nav_name">Mark Attendance</span>
                    </a>
                    <a
                        <?php echo (strpos($_SERVER['PHP_SELF'], '/pages/records.php') !== false ? 'class="nav_link active"' : 'class="nav_link"'); ?>
                        href="<?php echo BASE_URL; ?>/pages/records.php">
                        <i class="fa-regular fa-chart-bar nav_icon"></i>
                        <span class="nav_name">Absence Records</span>
                    </a>
                    <!-- <a href="#" class="nav_link">
                        <i class='bx bx-folder nav_icon'></i>
                        <span class="nav_name">Files</span>
                    </a>
                    <a href="#" class="nav_link">
                        <i class='bx bx-bar-chart-alt-2 nav_icon'></i>
                        <span class="nav_name">Stats</span>
                    </a> -->
                </div>
            </div>

            <!-- Sign Out Link -->
            <a href="<?php echo BASE_URL; ?>/logout.php" class="nav_link">
                <i class='bx bx-log-out nav_icon'></i>
                <span class="nav_name">Sign Out</span>
            </a>
        </nav>

    </div>
    <!--Container Main start-->
    <div class="height-120 bg-light ">