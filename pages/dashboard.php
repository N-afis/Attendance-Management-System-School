<?php

require_once '../config/config.php';
require_once '../config/database.php';
require_once '../classes/Teachers.php';
require_once '../classes/Students.php';
require_once '../classes/StudentAttendance.php';
require_once '../classes/TeacherAttendance.php';

requiredLoggeIn();

$adminName = $_SESSION["full_name"];


$db = new Database();
$pdo = $db->getConnection();

$student = new Student($pdo);
$teacher = new Teacher($pdo);
$studentAttendance = new StudentAttendance($pdo);
$teacherAttendance = new TeacherAttendance($pdo);





include_once "../includes/header.php";

?>


<div>
    <h1 class="fs-4">Welcome back, <span class="fw-bold"><?php echo $adminName ?></span> </h1>
    <p class="fs-5 text-muted">Here's an overview of today's attendance statistics</p>
</div>

<div class="row">
    <div class="col-12 col-md-8 mb-3">
        <div class="row g-4 mb-5">
            <div class="col-12 col-md-6 col-lg-3">
                <div class="cards d-flex flex-column gap-2 shadow">
                    <span class="fw-bold fs-5 d-flex align-items-center gap-3">Total Students <i class="fa-solid fa-graduation-cap nav_icon text-primary"></i></span>
                    <div class="d-flex flex-column">
                        <span class="fs-2 fw-bold"><?php echo $student->countAll() ?></span>
                        <span class="text-muted">+12 from last month</span>
                    </div>
                </div>
            </div>
            <div class="col-12 col-md-6 col-lg-3">
                <div class="cards d-flex flex-column gap-2 shadow">
                    <span class="fw-bold  fs-5 d-flex align-items-center gap-3">Total Teachers <i class="fa-solid fa-user-tie nav_icon text-secondary"></i></span>
                    <div class="d-flex flex-column">
                        <span class="fs-2 fw-bold"><?php echo $teacher->countAll() ?></span>
                        <span class="text-muted">+2 from last month</span>
                    </div>
                </div>
            </div>
            <div class="col-12 col-md-6 col-lg-3">
                <div class="cards d-flex flex-column gap-2 shadow">
                    <span class="fw-bold  fs-5 d-flex align-items-center gap-3">Present Today <i class="fa-solid fa-circle-check text-success"></i></span>
                    <div class="d-flex flex-column">
                        <span class="fs-2 fw-bold"><?php echo $studentAttendance->countPresenceToday() ?></span>
                        <span class="text-muted">89% attendance rate</span>
                    </div>
                </div>
            </div>
            <div class="col-12 col-md-6 col-lg-3">
                <div class="cards d-flex flex-column gap-2 shadow">
                    <span class="fw-bold  fs-5 d-flex align-items-center gap-3">Absent Today <i class="fa-solid fa-circle-xmark text-danger"></i></span>
                    <div class="d-flex flex-column">
                        <span class="fs-2 fw-bold"><?php echo $studentAttendance->countAbsenceToday() ?></span>
                        <span class="text-muted">11% absence rate</span>
                    </div>
                </div>
            </div>
        </div>
        <div>
            <div class="btns d-flex gap-2 mb-3">
                <button class="active" id="studentsBtn">Students Attendance</button>
                <button id="teachersBtn">Teachers Attendance</button>
            </div>
            <div id="studentCard" class="">
                <div class="cards d-flex flex-column gap-2 shadow">
                    <div class="d-flex flex-column">
                        <span class="fs-2 fw-bold">Today's Student Attendance Summary</span>
                        <span class="text-muted fs-5">Overview of student attendance for 5/28/2025</span>
                    </div>
                    <div class="row g-4">
                        <div class="col-6">
                            <div class="cards2 d-flex align-items-center flex-column gap-2">
                                <i class="fa-solid fa-circle-check text-success fs-3"></i>
                                <span class="fs-2 fw-bold"><?php echo $studentAttendance->countPresenceToday() ?></span>
                                <span class="text-muted">Present</span>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="cards2 d-flex align-items-center flex-column gap-2">
                                <i class="fa-solid fa-circle-xmark text-danger fs-3"></i>
                                <span class="fs-2 fw-bold"><?php echo $studentAttendance->countAbsenceToday() ?></span>
                                <span class="text-muted">Absent</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div id="teacherCard" class="d-none">
                <div class="cards d-flex flex-column gap-2 shadow">
                    <div class="d-flex flex-column">
                        <span class="fs-2 fw-bold">Today's Teacher Attendance Summary</span>
                        <span class="text-muted fs-5">Overview of student attendance for 5/28/2025</span>
                    </div>
                    <div class="row g-4">
                        <div class="col-6">
                            <div class="cards2 d-flex align-items-center flex-column gap-2">
                                <i class="fa-solid fa-circle-check text-success fs-3"></i>
                                <span class="fs-2 fw-bold"><?php echo $teacherAttendance->countPresenceToday() ?></span>
                                <span class="text-muted">Present</span>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="cards2 d-flex align-items-center flex-column gap-2">
                                <i class="fa-solid fa-circle-xmark text-danger fs-3"></i>
                                <span class="fs-2 fw-bold"><?php echo $teacherAttendance->countAbsenceToday() ?></span>
                                <span class="text-muted">Absent</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-12 col-md-4">
        <div class="right-dashboard d-flex flex-column gap-3">
            <!-- Most Absent Students -->
            <div class="cards shadow">
                <h5 class="fs-4 fw-bold mb-3">Most Absent Students (This Month)</h5>
                <ul id="topAbsentStudents" class="list-unstyled mb-0"></ul>
            </div>

            <!-- Absences by Filière Chart -->
            <div class="cards shadow">
                <h5 class="fs-4 fw-bold mb-3">Absences by Filière (This Month)</h5>
                <canvas id="filiereAbsenceChart" height="200"></canvas>
            </div>
        </div>
    </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script type="module" src="../assets/js/dashboard.js"></script>