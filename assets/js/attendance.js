// ========================== IMPORTS =============================

import { showToast } from "./utils/toast.js";
import { setupSwitcher } from "./utils/uiSwitching.js";

// ==================================== Set the date of Today ============================================

document.addEventListener("DOMContentLoaded", function () {
  const today = new Date();
  const dateInput = document.getElementById("AttendanceDate");

  // Format date as YYYY-MM-DD (required for date inputs)
  const formattedDate = today.toISOString().split("T")[0];
  dateInput.value = formattedDate;
  document.querySelector(".dateAttenInfo").textContent = `on ${formatDate(
    dateInput.value
  )}`;
});

// ==================================== UI SWITCHING ============================================

document.addEventListener("DOMContentLoaded", () => {
  setupSwitcher("studentsBtn", "teachersBtn", "studentCard", "teacherCard");
});

//============================== Search Student ===================================

function setupDependentDropdowns2(container) {
  const poleSelect = container.querySelector(".pole");
  const filiereSelect = container.querySelector(".filiere");
  const classSelect = container.querySelector(".class_id");

  if (!poleSelect || !filiereSelect || !classSelect) return;

  poleSelect.addEventListener("change", function () {
    const poleId = this.value;
    filiereSelect.innerHTML = `<option value="">All Filieres</option>`;
    classSelect.innerHTML = `<option value="">All Classes</option>`;
    filiereSelect.disabled = true;
    classSelect.disabled = true;

    if (!poleId) {
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
        autoSearch();
      });
  });

  filiereSelect.addEventListener("change", function () {
    const filiereId = this.value;
    classSelect.innerHTML = `<option value="">All Classes</option>`;
    classSelect.disabled = true;

    if (!filiereId) {
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
        autoSearch();
      });
  });

  classSelect.addEventListener("change", () => {
    autoSearch();
  });
}

function autoSearch() {
  const poleId = document.getElementById("poleSelect").value;
  const filiereId = document.getElementById("filiereSelect").value;
  const classId = document.getElementById("classSelect").value;

  const params = new URLSearchParams({
    pole_id: poleId || "",
    filiere_id: filiereId || "",
    class_id: classId || "",
  });

  fetch(`../api/student/search_students.php?${params.toString()}`)
    .then((res) => res.json())
    .then((data) => {
      renderStudents(data.students);
    });
}

function renderStudents(data) {
  const tbody = document.getElementById("studentsTable");
  tbody.innerHTML = "";

  if (data.length === 0) {
    tbody.innerHTML = `<tr><td colspan="5" class='text-center'>No students found.</td></tr>`;
    return;
  }

  data.forEach((student) => {
    const row = document.createElement("tr");
    row.setAttribute("data-id", student.id);

    row.innerHTML = `
    <td>${student.first_name} ${student.last_name}</td>
    <td>${student.filiere_name}</td>
    <td>${student.class_name}</td>
    <td>
      <select name="status" class="form-select" data-status>
        <option value="present">Present</option>
        <option value="absent">Absent</option>
      </select>
    </td>
  `;
    tbody.appendChild(row);
  });
}

// Initialize dropdowns and search
document.addEventListener("DOMContentLoaded", () => {
  setupDependentDropdowns2(document);
  autoSearch();
});

//============================== Search Teacher ===================================

function loadTeachers() {
  fetch(`../api/teacher/get_teachers.php`)
    .then((res) => {
      if (!res.ok) throw new Error("HTTP error! status: " + res.status);
      return res.json();
    })
    .then((data) => {
      if (data.success) {
        renderTeachersTable(data.data);
      } else {
        showToast(data.message, "bg-danger");
      }
    })
    .catch((error) => {
      console.error("Fetch error:", error);
      showToast("Could not fetch teachers", "bg-danger");
    });
}

document.addEventListener("DOMContentLoaded", () => {
  loadTeachers(); // Explicitly pass initial values
});
// //============================== Display teachers ================================

document.addEventListener("DOMContentLoaded", function () {
  const searchInput = document.getElementById("searchInput");

  searchInput.addEventListener("input", function () {
    const keyword = this.value.trim();

    if (keyword === "") {
      loadTeachers(); // fallback to show all
      return;
    }

    fetch(
      `../api/teacher/search_teachers.php?keyword=${encodeURIComponent(
        keyword
      )}`
    )
      .then((res) => {
        if (!res.ok) throw new Error("Network response was not ok");
        return res.json();
      })
      .then((data) => {
        renderTeachersTable(data.data);
      })
      .catch((error) => {
        console.error("Error loading teachers:", error);
        showToast("Search failed", "bg-danger");
      });
  });

  loadTeachers();
});

function renderTeachersTable(data) {
  const teachersTable = document.getElementById("teachersTable");
  teachersTable.innerHTML = "";

  if (!Array.isArray(data) || data.length === 0) {
    teachersTable.innerHTML = `<tr><td colspan="2" class='text-center'>No teachers found.</td></tr>`;
    return;
  }

  data.forEach((teacher) => {
    const row = document.createElement("tr");
    row.setAttribute("data-id", teacher.id);

    row.innerHTML = `
      <td>${teacher.first_name} ${teacher.last_name}</td>
      <td>
        <select name="" class="form-select">
          <option value="present">Present</option>
          <option value="absent">Absent</option>
        </select>
      </td>
    `;
    teachersTable.appendChild(row);
  });
}

// ============================== Shared Date + Time Handling ==============================
document.addEventListener("DOMContentLoaded", function () {
  // Set today's date by default
  const today = new Date();
  const dateInput = document.getElementById("AttendanceDate");
  const formattedDate = today.toISOString().split("T")[0];
  dateInput.value = formattedDate;

  // Initialize both cards with default values
  updateAllDateDisplays(formattedDate);
  updateAllTimeDisplays("All Day");

  // Date Picker Change Handler
  dateInput.addEventListener("change", function () {
    updateAllDateDisplays(this.value);
  });

  // Time Range Change Handler
  document
    .getElementById("SelectTimeRange")
    .addEventListener("change", function () {
      handleTimeRangeChange(this.value);
    });

  // Custom Time Inputs Change Handler
  document
    .getElementById("startTime")
    ?.addEventListener("change", updateCustomTimeDisplay);
  document
    .getElementById("endTime")
    ?.addEventListener("change", updateCustomTimeDisplay);
});

// Update date displays in both cards
function updateAllDateDisplays(date) {
  document.querySelectorAll(".dateAttenInfo").forEach((el) => {
    el.textContent = `on ${formatDate(date)}`;
  });
}

// Update time displays in both cards
function updateAllTimeDisplays(timeText) {
  document.querySelectorAll(".timeAttenInfo").forEach((el) => {
    el.textContent = timeText.includes("during")
      ? timeText
      : `during ${timeText}`;
  });
}

// Handle time range selection changes
function handleTimeRangeChange(selectedValue) {
  const customTimeDiv = document.getElementById("customTime");

  if (selectedValue === "CusTime") {
    customTimeDiv?.classList.remove("d-none");
    updateCustomTimeDisplay();
  } else {
    customTimeDiv?.classList.add("d-none");
    updateAllTimeDisplays(selectedValue);
  }
}

// Update display when custom times change
function updateCustomTimeDisplay() {
  const startTime = document.getElementById("startTime")?.value || "8:00";
  const endTime = document.getElementById("endTime")?.value || "10:50";
  updateAllTimeDisplays(`${startTime} - ${endTime} (Custom)`);
}

// Format date nicely
function formatDate(dateString) {
  const options = { year: "numeric", month: "long", day: "numeric" };
  return new Date(dateString).toLocaleDateString(undefined, options);
}

// ============================== Submit Student ==============================

document.getElementById("submitStudent").addEventListener("click", () => {
  const date = document.getElementById("AttendanceDate").value;

  const timeRange = document.getElementById("SelectTimeRange").value;

  const { start, end } = validateDataTime(date, timeRange);

  const studentCon = document.getElementById("studentCard");
  const selectElements = studentCon.querySelectorAll("select");

  const absentStudent = [];

  selectElements.forEach((select) => {
    if (select.value === "absent") {
      const tr = select.closest("tr");
      if (tr) {
        const id = tr.getAttribute("data-id");
        if (id) {
          absentStudent.push({
            studentId: id,
            date: date,
            status: "absent",
            absence_start_time: start,
            absence_end_time: end,
          });
        }
      }
    }
  });

  console.log(absentStudent);

  fetch("../api/attendance/mark_students_attendance.php", {
    method: "POST",
    headers: {
      "Content-Type": "application/json",
    },
    body: JSON.stringify({
      data: absentStudent.length > 0 ? absentStudent : null,
    }),
  })
    .then((res) => res.json())
    .then((data) => {
      if (data.success) {
        showToast(data.message, "bg-primary");
      } else {
        showToast(data.message, "bg-danger");
      }
    })
    .catch((e) => {
      console.error("Error:", e);
      showToast("Something went wrong", "bg-danger");
    });
});

// ============================== Validation function ==============================

function validateDataTime(date, timeRange) {
  let start, end;

  const inputDate = new Date(date);
  const today = new Date();

  // Remove time from both dates
  inputDate.setHours(0, 0, 0, 0);
  today.setHours(0, 0, 0, 0);

  if (inputDate > today) {
    showToast("Please enter a valid date (not in the future)", "bg-warning");
    return;
  }

  if (timeRange === "CusTime") {
    start = document.getElementById("startTime").value;
    end = document.getElementById("endTime").value;
  } else if (timeRange === "All Day") {
    start = "08:30";
    end = "18:30";
  } else {
    [start, end] = timeRange.split("-").map((x) => x.trim());
  }

  return { start, end };
}

// ============================== Submit Teacher ==============================

document.getElementById("submitTeacher").addEventListener("click", () => {
  const date = document.getElementById("AttendanceDate").value;

  const timeRange = document.getElementById("SelectTimeRange").value;

  const { start, end } = validateDataTime(date, timeRange);

  const teacherCard = document.getElementById("teacherCard");
  const selectElements = teacherCard.querySelectorAll("select");

  const absentTeachers = [];

  selectElements.forEach((select) => {
    if (select.value === "absent") {
      const tr = select.closest("tr");
      if (tr) {
        const id = tr.getAttribute("data-id");
        if (id) {
          absentTeachers.push({
            teacher_id: id,
            date: date,
            status: "absent",
            absence_start_time: start,
            absence_end_time: end,
          });
        }
      }
    }
  });

  console.log(absentTeachers);

  fetch("../api/attendance/mark_teachers_attendance.php", {
    method: "POST",
    headers: {
      "Content-Type": "application/json",
    },
    body: JSON.stringify({
      data: absentTeachers.length > 0 ? absentTeachers : null,
    }),
  })
    .then((res) => res.json())
    .then((data) => {
      if (data.success) {
        showToast(data.message, "bg-primary");
      } else {
        showToast(data.message, "bg-danger");
      }
    })
    .catch((e) => {
      console.error("Error:", e);
      showToast("Something went wrong", "bg-danger");
    });
});