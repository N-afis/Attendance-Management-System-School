<?php

require_once '../config/config.php';
require_once '../config/database.php';

requiredLoggeIn();

$db = new Database();
$pdo = $db->getConnection();

include_once "../includes/header.php";

?>

<div>
    <h1 class="fs-4 fw-bold">Absence Records</span> </h1>
    <p class="fs-5 text-muted">Absence Records for students and teachers</p>
</div>


<div>
    <div class="btns w-100 d-flex gap-2 mb-3">
        <button class="active w-50" id="studentsBtn">Students Attendance</button>
        <button class="w-50" id="teachersBtn">Teachers Attendance</button>
    </div>

    <div id="studentCard">
        <div class="cards d-flex flex-column gap-2 shadow">
            <div class="cards3">
                <div>
                    <h1 class="fs-4 fw-bold">Filter Attendance Records</h1>
                    <p class="fs-5 text-muted textAttenInfo">
                        Use the filters below to find specific attendance records
                    </p>
                </div>
                <div class="row g-2">
                    <div class="col-3">
                        <label for="">From</label>
                        <input type="date" class="form-control" id="fromDateStu">
                    </div>
                    <div class="col-3">
                        <label for="">To</label>
                        <input type="date" class="form-control" id="toDateStu">
                    </div>
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
                </div>
                <div class="row">
                    <div class="col-3">
                        <label>Class</label>
                        <select class="form-select class_id" id="classSelect" disabled>
                            <option value="">All Classes</option>
                        </select>
                    </div>
                    <div class="col-3">
                        <label>Search</label>
                        <input type="text" placeholder="Search..." class="form-control" id="searchInputStu">
                    </div>
                </div>

            </div>

            <div class="cards3">
                <div>
                    <h1 class="fs-4 fw-bold">Attendance Records</h1>
                    <p class="fs-5 text-muted textAttenInfo">
                        Showing <span id="StuRecordsNumber">0</span> records
                    </p>
                </div>
                <table class="table table-hover text-center">
                    <tr class="bg-main text-white">
                        <th>Full Name</th>
                        <th>Filiere</th>
                        <th>Class</th>
                        <th>Date</th>
                        <th>Time Slot</th>
                        <th>Justification</th>
                        <th>Justification Document</th>
                        <th>Justification Preview</th>
                    </tr>
                    <tbody id="studentsTable"></tbody>
                </table>
                <div id="pagination" class="mt-3 text-center"></div>
            </div>
        </div>
    </div>
    <div id="teacherCard" class="d-none">
        <div class="cards d-flex flex-column gap-2 shadow">
            <div class="cards3">
                <div>
                    <h1 class="fs-4 fw-bold">Filter Attendance Records</h1>
                    <p class="fs-5 text-muted textAttenInfo">
                        Use the filters below to find specific attendance records
                    </p>
                </div>
                <div class="row g-2">
                    <div class="col-3">
                        <label for="">From</label>
                        <input type="date" class="form-control" id="fromDateTea">
                    </div>
                    <div class="col-3">
                        <label for="">To</label>
                        <input type="date" class="form-control" id="toDateTea">
                    </div>
                    <div class="col-3">
                        <label>Search</label>
                        <input type="text" placeholder="Search..." class="form-control" id="searchInputTea">
                    </div>
                </div>

            </div>

            <div class="cards3">
                <div>
                    <h1 class="fs-4 fw-bold">Attendance Records</h1>
                    <p class="fs-5 text-muted textAttenInfo">
                        Showing <span id="TeaRecordsNumber">0</span> records
                    </p>
                </div>
                <table class="table table-hover text-center">
                    <tr class="bg-main text-white">
                        <th>Full Name</th>
                        <th>Date</th>
                        <th>Time Slot</th>
                        <th>Justification</th>
                        <th>Justification Document</th>
                        <th>Justification Preview</th>
                    </tr>
                    <tbody id="teachersTable">

                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <?php include_once '../includes/toast.php' ?>
</div>

<script type="module" src="../assets/js/records.js"></script>