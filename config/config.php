<?php 

// Application configuration settings
define('BASE_URL', 'http://localhost/Attendance-Management-System');
define('APP_NAME', 'Attendance Management System');
define('FAVICON', '../assets/images/favicon.png');
define('APP_VERSION', '1.0.0');

// ini_set('session.gc_maxlifetime', 10); // 1 hour in seconds
// session_set_cookie_params(10); 
session_start();

date_default_timezone_set("Africa/Casablanca");

function isLoggeIn(){
    return isset($_SESSION['user_id']);
}

function requiredLoggeIn(){
    if (!isLoggeIn()) {
        header("Location: " . BASE_URL . "/login.php");
        exit;
    }
}

function redirect($url){
    header("Location: " . BASE_URL . $url);
    exit;
}

?>