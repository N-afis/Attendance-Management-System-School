<?php

require_once '../config/config.php';
require_once '../config/database.php';
require_once '../classes/Teachers.php';

requiredLoggeIn();

$db = new Database();
$pdo = $db->getConnection();

$student = new Teacher($pdo);

include_once "../includes/header.php";

?>


<!-- Add Modal -->
<?php include_once "../includes/teacher/addModal.php"; ?>

<!-- Modify Modal -->
<?php include_once "../includes/teacher/modifyModal.php"; ?>





<div>
    <h1 class="fs-4 fw-bold">Teachers Management</h1>

    <div class="d-flex justify-content-between align-items-center">
        <div class="d-flex align-items-center gap-3">
            <div class="form-check d-flex align-items-center">
                <input class="form-check-input" type="checkbox" id="checkAll">
                <label class="form-check-label" for="checkAll">
                    Check all
                </label>
            </div>
            <button class="btn btn-danger" id="multiDeleteBtn">Delete</button>
        </div>
        <div class="d-flex gap-3">
            <label class="uploadBtn mb-4 d-flex align-items-center gap-2">
                <i class="fa-solid fa-upload"></i> Upload Teacher
                <input type="file" id="fileInput" accept=".xlsx,.xls,.csv" hidden>
            </label>

            <button type="button" id="openModalBtn" class="addBtn mb-4 d-flex align-items-center gap-2">
                <i class="bx bx-user-plus bx-flip-horizontal nav_icon"></i> Add Teacher
            </button>
        </div>
    </div>
    <div class="row mb-4" id="search">
        <div class="col-3">
            <label>Search</label>
            <input type="text" class="form-control" id="searchInput" placeholder="Search teachers...">
        </div>
    </div>
    <table class="table table-hover">

        <tr class="bg-main text-white">
            <th>Select</th>
            <th>Last Name</th>
            <th>First Name</th>
            <th>Email</th>
            <th>Gender</th>
            <th>Date Of Birth</th>
            <th>Action</th>
        </tr>
        <tbody id="teachersTable">
            <!-- <tr>
            <td class="py-3">Saad</td>
            <td class="py-3">Kanani</td>
            <td class="py-3">saad@cmc.com</td>
            <td class="py-3">Developpement Digital</td>
            <td class="py-3">DEV101</td>
            <td class="py-3">Male</td>
            <td class="py-3">2005-07-25</td>
            <td class="py-3">
                <button class="editbtn"><i class="fa-regular fa-pen-to-square"></i></button>
                <button class="deletebtn "><i class="fa-regular fa-trash-can"></i></button>
            </td>
        </tr> -->
        </tbody>
    </table>
    <?php include_once '../includes/toast.php' ?>

    <div id="pagination" class="mt-3 text-center"></div>
</div>


<script type="module" src="../assets/js/teacher.js"></script>