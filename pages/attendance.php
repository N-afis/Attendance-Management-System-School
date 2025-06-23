<?php

require_once '../config/config.php';
require_once '../config/database.php';

requiredLoggeIn();

$db = new Database();
$pdo = $db->getConnection();

include_once "../includes/header.php";

?>

<div>
    <h1 class="fs-4 fw-bold">Mark Attendance</h1>
    <p class="fs-5 text-muted">Mark attendance for students and teachers</p>
</div>


<div class="w-50 d-flex gap-2 mb-4">
    <input type="date" class="form-control w-50 h-25" id="AttendanceDate">
    <div class="w-50">
        <select name="time_range" id="SelectTimeRange" class="form-select">
            <option value="All Day" selected>All Day</option>
            <option value="8:00-10:50">8:00 - 10:50</option>
            <option value="11:10-13:30">11:10 - 13:30</option>
            <option value="13:30-15:50">13:30 - 15:50</option>
            <option value="16:10-18:30">16:10 - 18:30</option>
            <option value="CusTime">Custom Time Range</option>
        </select>

        <div class="d-flex gap-1 d-none" id="customTime">
            <div class="form-group">
                <span style="font-size: 14px;">Start</span>
                <input type="time" class="form-control" id="startTime" value="08:00">
            </div>
            <div class="form-group">
                <span style="font-size: 14px;">End</span>
                <input type="time" class="form-control" id="endTime" value="10:50">
            </div>
        </div>
    </div>
</div>

<div>
    <div class="btns w-100 d-flex gap-2 mb-3">
        <button class="active w-50" id="studentsBtn">Students Attendance</button>
        <button class="w-50" id="teachersBtn">Teachers Attendance</button>
    </div>

    <div id="studentCard">
        <div class="cards d-flex flex-column gap-2 shadow">
            <div>
                <div>
                    <h1 class="fs-4 fw-bold">Student Attendance</h1>
                    <p class="fs-5 text-muted textAttenInfo">
                        Mark attendance for students
                        <span class="dateAttenInfo"></span>
                        <span class="timeAttenInfo">during All Day</span>
                    </p>
                </div>
            </div>

            <div class="row mb-4" id="search">
                <div class="col-3">
                    <label>Pole</label>
                    <select class="form-select pole" id="poleSelect">
                        <option value="">All Poles</option>
                        <?php
                        $pole_stmt = $pdo->query("SELECT id, name FROM poles");
                        while ($row = $pole_stmt->fetch()) {
                            echo "<option value='{$row['id']}'>{$row['name']}</option>";
                        }
                        ?>
                    </select>
                </div>
                <div class="col-3">
                    <label>Filiere</label>
                    <select class="form-select filiere" id="filiereSelect" disabled>
                        <option value="">All Filieres</option>
                    </select>
                </div>
                <div class="col-3">
                    <label>Class</label>
                    <select class="form-select class_id" id="classSelect" disabled>
                        <option value="">All Classes</option>
                    </select>
                </div>
            </div>

            <table class="table table-hover text-center">
                <tr class="bg-main text-white">
                    <th>Full Name</th>
                    <th>Filiere</th>
                    <th>Class</th>
                    <th>Status</th>
                </tr>
                <tbody id="studentsTable">

                </tbody>
            </table>
            <div class="d-flex justify-content-end">
                <button class="Submitbtn" id="submitStudent"><i class="fa-solid fa-check"></i> Submit Attendance</button>
            </div>
        </div>
    </div>
    <div id="teacherCard" class="d-none">
        <div class="cards d-flex flex-column gap-2 shadow">
            <div>
                <div>
                    <h1 class="fs-4 fw-bold">Teacher Attendance</h1>
                    <p class="fs-5 text-muted textAttenInfo">
                        Mark attendance for teachers
                        <span class="dateAttenInfo"></span>
                        <span class="timeAttenInfo">during All Day</span>
                    </p>
                </div>
            </div>

            <div class="row mb-4" id="search">
                <div class="col-3">
                    <label>Search</label>
                    <input type="text" class="form-control" id="searchInput" placeholder="Search teachers...">
                </div>
            </div>

            <table class="table table-hover text-center">
                <tr class="bg-main text-white">
                    <th>Full Name</th>
                    <th>Status</th>
                </tr>
                <tbody id="teachersTable">

                </tbody>
            </table>
            <div class="d-flex justify-content-end">
                <button class="Submitbtn" id="submitTeacher"><i class="fa-solid fa-check"></i> <span>Submit Attendance</span></button>
            </div>
        </div>
    </div>
    <?php include_once '../includes/toast.php' ?>
</div>

<script type="module" src="../assets/js/attendance.js"></script>