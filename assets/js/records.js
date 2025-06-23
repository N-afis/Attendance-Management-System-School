// ========================== IMPORTS =============================

import { showToast } from "./utils/toast.js";
import { setupSwitcher } from "./utils/uiSwitching.js";

// ==================================== UI SWITCHING ============================================

document.addEventListener("DOMContentLoaded", () => {
  setupSwitcher("studentsBtn", "teachersBtn", "studentCard", "teacherCard");
});

// =================================== STUDENT ATTENDANCE ===========================================

// Load student records
function loadStudents(page = 1, limit = 20) {
  fetch(
    `../api/records/get_students_attendance.php?page=${page}&limit=${limit}`
  )
    .then((response) => response.json())
    .then((data) => {
      if (data.success) {
        renderStudents(data.data);
        setupPagination(data.pagination);
      } else {
        showToast(data.message, "bg-danger");
      }
    })
    .catch((error) => {
      showToast("Could not fetch students", "bg-danger");
      console.error("Fetch error:", error);
    });
}

document.addEventListener("DOMContentLoaded", () => {
  loadStudents();
  setupDependentDropdowns(document.getElementById("studentCard"));
  autoSearch();
});

function setupPagination(pagination) {
  currentPage = pagination.current_page;
  totalPages = pagination.total_pages;
  renderPagination();
}

let currentPage = 1;
let totalPages = 1;

// Filter/Search controls for students
function setupDependentDropdowns(container) {
  const poleSelect = container.querySelector(".pole");
  const filiereSelect = container.querySelector(".filiere");
  const classSelect = container.querySelector(".class_id");
  const fromDateInput = document.getElementById("fromDateStu");
  const toDateInput = document.getElementById("toDateStu");
  const searchInput = document.getElementById("searchInputStu");

  if (
    !poleSelect ||
    !filiereSelect ||
    !classSelect ||
    !fromDateInput ||
    !toDateInput ||
    !searchInput
  )
    return;

  fromDateInput.addEventListener("change", () => autoSearch());

  toDateInput.addEventListener("change", () => autoSearch());

  searchInput.addEventListener("input", () => autoSearch());

  poleSelect.addEventListener("change", function () {
    const poleId = this.value;
    filiereSelect.innerHTML = `<option value="">All Filieres</option>`;
    classSelect.innerHTML = `<option value="">All Classes</option>`;
    filiereSelect.disabled = true;
    classSelect.disabled = true;

    if (!poleId) {
      currentPage = 1;
      autoSearch();
      return;
    }

    fetch(`../api/get_filieres.php?pole_id=${poleId}`)
      .then((res) => res.json())
      .then((data) => {
        data.forEach((filiere) => {
          let opt = document.createElement("option");
          opt.value = filiere.id;
          opt.textContent = filiere.name;
          filiereSelect.appendChild(opt);
        });
        filiereSelect.disabled = false;
        currentPage = 1;
        autoSearch();
      });
  });

  filiereSelect.addEventListener("change", function () {
    const filiereId = this.value;
    classSelect.innerHTML = `<option value="">All Classes</option>`;
    classSelect.disabled = true;

    if (!filiereId) {
      currentPage = 1;
      autoSearch();
      return;
    }

    fetch(`../api/get_classes.php?filiere_id=${filiereId}`)
      .then((res) => res.json())
      .then((data) => {
        data.forEach((cls) => {
          let opt = document.createElement("option");
          opt.value = cls.id;
          opt.textContent = cls.name;
          classSelect.appendChild(opt);
        });
        classSelect.disabled = false;
        currentPage = 1;
        autoSearch();
      });
  });

  classSelect.addEventListener("change", () => {
    currentPage = 1;
    autoSearch();
  });
}

function autoSearch(page = currentPage) {
  const poleId = document.getElementById("poleSelect").value;
  const filiereId = document.getElementById("filiereSelect").value;
  const classId = document.getElementById("classSelect").value;
  const fromDateStu = document.getElementById("fromDateStu").value;
  const toDateStu = document.getElementById("toDateStu").value;
  const searchValue = document.getElementById("searchInputStu").value;

  console.log(fromDateStu, toDateStu, searchValue);
  

  const params = new URLSearchParams({
    pole_id: poleId || "",
    filiere_id: filiereId || "",
    class_id: classId || "",
    from_date: fromDateStu || "",
    to_date: toDateStu || "",
    keyword: searchValue || "",
    page: page,
  });

  fetch(`../api/records/search_students_attendance.php?${params.toString()}`)
    .then((res) => res.json())
    .then((data) => {
      currentPage = data.current_page;
      totalPages = data.total_pages;
      renderStudents(data.students);
      renderPagination();
    });
}

function renderStudents(data) {
  const tbody = document.getElementById("studentsTable");
  const StuRecordsNumber = document.getElementById("StuRecordsNumber");
  StuRecordsNumber.innerHTML = data.length;
  tbody.innerHTML = "";

  if (data.length === 0) {
    tbody.innerHTML = `<tr><td colspan="7" class='text-center'>No students found.</td></tr>`;
    return;
  }
  let studentFileId = "student_";
  data.forEach((stu) => {
    const row = document.createElement("tr");
    row.innerHTML = `
                <td>${stu.first_name} ${stu.last_name}</td>
                <td>${stu.filiere_name}</td>
                <td>${stu.class_name}</td>
                <td>${stu.date}</td>
                <td>${stu.absence_start_time.slice(
                  0,
                  5
                )} - ${stu.absence_end_time.slice(0, 5)}</td>
                <td>${
                  stu.is_justified === 1
                    ? '<span class="badge rounded-pill bg-success">Justified</span>'
                    : '<span class="badge rounded-pill bg-danger">Not Justified</span>'
                }</td>
                <td>
                    <label for="${studentFileId + stu.student_id}" class="p-0">
                        <i class="fa-solid fa-arrow-up-from-bracket secondary p-0"></i>
                        <input type="file" class="p-0" data-student-id="${
                          stu.student_id
                        }" name="justificationDoc" id="${
      studentFileId + stu.student_id
    }" hidden>
                    </label>  
                </td>
                <td class="text-center">
                  <button class="btn p-0 viewJustificationBtn" data-justification="${
                    stu.justification_document
                  }" ${stu.is_justified === 0 ? "disabled" : ""}>
                    <i class="fa fa-eye p-0"></i>
                  </button>
                </td>
    `;
    tbody.appendChild(row);
  });
  attachFileUploadListeners();
}

function renderPagination() {
  const container = document.getElementById("pagination");
  container.innerHTML = "";

  if (totalPages <= 1) return;

  for (let i = 1; i <= totalPages; i++) {
    const btn = document.createElement("button");
    btn.className = `btn btn-sm btn-outline-primary mx-1 ${
      i === currentPage ? "active" : ""
    }`;
    btn.textContent = i;
    btn.addEventListener("click", () => {
      currentPage = i;
      autoSearch(i);
      document.getElementById("checkAll").checked = false;
    });
    container.appendChild(btn);
  }
}

// =================================== TEACHER ATTENDANCE ===========================================

function loadTeachers() {
  fetch(`../api/records/get_teachers_attendance.php`)
    .then((response) => {
      if (!response.ok) {
        throw new Error(`HTTP error! status: ${response.status}`);
      }
      return response.json();
    })
    .then((data) => {
      if (data.success) {
        renderTeachers(data.data);
      } else {
        showToast(data.message, "bg-danger");
      }
    })
    .catch((error) => {
      showToast("Could not fetch teachers", "bg-danger");
      console.error("Fetch error:", error);
    });
}

// Initialize dropdowns and search
document.addEventListener("DOMContentLoaded", () => {
  loadTeachers();
  setupFilterTeacher();
  autoSearchTea();
});


//============================== Search Teacher ===================================

function setupFilterTeacher() {
  const fromDateInput = document.getElementById("fromDateTea");
  const toDateInput = document.getElementById("toDateTea");
  const searchInput = document.getElementById("searchInputTea");

  if (!fromDateInput || !toDateInput || !searchInput) return;

  fromDateInput.addEventListener("change", () => {
    autoSearchTea();
  });

  toDateInput.addEventListener("change", () => {
    autoSearchTea();
  });

  searchInput.addEventListener("input", () => {
    autoSearchTea();
  });
}

function autoSearchTea() {
  const fromDateStu = document.getElementById("fromDateTea").value;
  const toDateStu = document.getElementById("toDateTea").value;
  const searchValue = document.getElementById("searchInputTea").value;

  const params = new URLSearchParams({
    from_date: fromDateStu || "",
    to_date: toDateStu || "",
    keyword: searchValue || "",
  });

  fetch(`../api/records/search_teachers_attendance.php?${params.toString()}`)
    .then((res) => res.json())
    .then((data) => {
      renderTeachers(data.teachers);
    });
}

function renderTeachers(data) {
  const tbody = document.getElementById("teachersTable");
  const TeaRecordsNumber = document.getElementById("TeaRecordsNumber");
  TeaRecordsNumber.innerHTML = data.length;
  tbody.innerHTML = "";

  if (data.length === 0) {
    tbody.innerHTML = `<tr><td colspan="6" class='text-center'>No students found.</td></tr>`;
    return;
  }
  let teacherFileId = "teacher_";
  data.forEach((teacher) => {
    const row = document.createElement("tr");
    row.innerHTML = `
                <td>${teacher.first_name} ${teacher.last_name}</td>
                <td>${teacher.date}</td>
                <td>${teacher.absence_start_time.slice(
                  0,
                  5
                )} - ${teacher.absence_end_time.slice(0, 5)}</td>
                <td>${
                  teacher.is_justified === 1
                    ? '<span class="badge rounded-pill bg-success">Justified</span>'
                    : '<span class="badge rounded-pill bg-danger">Not Justified</span>'
                }</td>
                <td>
                    <label for="${
                      teacherFileId + teacher.teacher_id
                    }" class="p-0">
                        <i class="fa-solid fa-arrow-up-from-bracket secondary p-0"></i>
                        <input type="file" class="p-0" data-teacher-id="${
                          teacher.teacher_id
                        }" name="justificationDoc" id="${
      teacherFileId + teacher.teacher_id
    }" hidden>
                    </label>  
                </td>
                <td>
                  <button class="btn p-0 viewJustificationBtn" data-justification="${
                    teacher.justification_document
                  }" ${teacher.is_justified === 0 ? "disabled" : ""}>
                    <i class="fa fa-eye p-0"></i>
                  </button>
                </td>`;
    tbody.appendChild(row);
  });
  attachFileUploadListeners();
}


//============================== Upload Document For Student and Teacher ===================================

function attachFileUploadListeners() {
  document
    .querySelectorAll('input[name="justificationDoc"]')
    .forEach((input) => {
      input.addEventListener("change", function (e) {
        const file = e.target.files[0];
        if (!file) return;

        const formData = new FormData();

        const isTeacher = document
          .getElementById("teachersBtn")
          ?.classList.contains("active");

        if (isTeacher) {
          const teacherId = e.target.dataset.teacherId;

          if (!teacherId) {
            showToast("Missing teacher ID", "bg-warning");
            return;
          }

          formData.append("justificationDoc", file);
          formData.append("teacher_id", teacherId);

          fetch("../api/records/upload_justification.php", {
            method: "POST",
            body: formData,
          })
            .then((res) => res.json())
            .then((data) => {
              if (data.success) {
                showToast("Justification uploaded successfully", "bg-success");
                autoSearchTea();
              } else {
                showToast(data.message || "Upload failed", "bg-danger");
              }
            })
            .catch((err) => {
              console.error("Upload error:", err);
              showToast("Network error during upload", "bg-danger");
            });
        } else {
          const studentId = e.target.dataset.studentId;

          if (!studentId) {
            showToast("Missing student ID", "bg-warning");
            return;
          }

          formData.append("justificationDoc", file);
          formData.append("student_id", studentId);

          fetch("../api/records/upload_justification.php", {
            method: "POST",
            body: formData,
          })
            .then((res) => res.json())
            .then((data) => {
              if (data.success) {
                showToast("Justification uploaded successfully", "bg-success");
                autoSearch();
              } else {
                showToast(data.message || "Upload failed", "bg-danger");
              }
            })
            .catch((err) => {
              console.error("Upload error:", err);
              showToast("Network error during upload", "bg-danger");
            });
        }
      });
    });
}

//============================== Document Preview For Student and Teacher ===================================

document.addEventListener("click", function (e) {
  const btn = e.target.closest(".viewJustificationBtn");
  if (!btn) return;

  const justification = btn.getAttribute("data-justification");

  if (!justification) {
    showToast("No justification document available.", "bg-warning");
    return;
  }

  // Determine folder based on active tab
  const isTeacher = document
    .getElementById("teachersBtn")
    ?.classList.contains("active");
  const folder = isTeacher ? "teachers" : "students";
  const fileUrl = `../uploads/justifications/${folder}/${justification}`;

  // Open in new tab
  window.open(fileUrl, "_blank");
});
