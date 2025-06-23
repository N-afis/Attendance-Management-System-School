<div id="teacherModal" class="modal">
    <div class="modal-content">
        <span class="close">&times;</span>
        <h1 class="fs-5 mb-4">Add New Teacher</h1>

        <form id="addTeacherForm">
            <div class="row mb-2">
                <div class="col-6">
                    <label>First Name</label>
                    <input type="text" class="form-control" name="first_name" required>
                </div>
                <div class="col-6">
                    <label>Last Name</label>
                    <input type="text" class="form-control" name="last_name" required>
                </div>
            </div>
            <div class="row mb-2">
                <div class="col-6">
                    <label>Email</label>
                    <input type="email" class="form-control" name="email" required>
                </div>
                <div class="col-6">
                    <label>Gender</label>
                    <select name="gender" class="form-select">
                        <option disabled selected>Gender</option>
                        <option value="male">Male</option>
                        <option value="female">Female</option>
                    </select>
                </div>
            </div>
            <div class="row mb-2">
                <div class="col-6">
                    <label>Date Of Birth</label>
                    <input type="date" class="form-control" name="dob" required>
                </div>
            </div>
            <div class="modal-actions">
                <button type="button" class="cancelBtn" id="cancelBtn">Cancel</button>
                <button type="submit" class="submitbtn" id="submitbtn">Add Teacher</button>
            </div>
        </form>
    </div>
</div>