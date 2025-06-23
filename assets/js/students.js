import { showToast } from "./utils/toast.js";

function loadStudents(page = 1, limit = 20) {
  fetch(`../api/student/get_students.php?page=${page}&limit=${limit}`)
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

document.addEventListener("DOMContentLoaded", loadStudents);

//============================== Display students ================================

function setupPagination(pagination) {
  const paginationContainer = document.getElementById("pagination");
  paginationContainer.innerHTML = "";

  if (pagination.total_pages <= 1) return;

  for (let i = 1; i <= pagination.total_pages; i++) {
    const btn = document.createElement("button");
    btn.innerText = i;
    btn.className = "btn btn-sm btn-outline-primary mx-1";
    if (i === pagination.current_page) {
      btn.classList.add("active");
    }
    btn.addEventListener("click", () => {
      loadStudents(i);
      document.getElementById("checkAll").checked = false;
    });
    paginationContainer.appendChild(btn);
  }
}

//============================== Dropdown Logic ================================

function setupDependentDropdowns(modal) {
  const poleSelect = modal.querySelector(".pole");
  const filiereSelect = modal.querySelector(".filiere");
  const classSelect = modal.querySelector(".class_id");

  if (!poleSelect || !filiereSelect || !classSelect) return;

  // Pole -> Filiere
  poleSelect.addEventListener("change", function () {
    const poleId = this.value;

    filiereSelect.innerHTML = "<option disabled selected>Loading...</option>";
    filiereSelect.disabled = true;
    classSelect.innerHTML = "<option disabled selected>Class</option>";
    classSelect.disabled = true;

    if (!poleId) return;

    fetch(`../api/get_filieres.php?pole_id=${poleId}`)
      .then((res) =>
        res.ok ? res.json() : Promise.reject("Failed to load filieres")
      )
      .then((data) => {
        filiereSelect.innerHTML =
          "<option disabled selected>Choose Filière</option>";
        data.forEach((filiere) => {
          let opt = document.createElement("option");
          opt.value = filiere.id;
          opt.textContent = filiere.name;
          filiereSelect.appendChild(opt);
        });
        filiereSelect.disabled = false;
      })
      .catch((err) => {
        console.error(err);
        filiereSelect.innerHTML =
          "<option disabled selected>Error loading data</option>";
      });
  });

  // Filiere -> Class
  filiereSelect.addEventListener("change", function () {
    const filiereId = this.value;

    classSelect.innerHTML = "<option disabled selected>Loading...</option>";
    classSelect.disabled = true;

    if (!filiereId) return;

    fetch(`../api/get_classes.php?filiere_id=${filiereId}`)
      .then((res) =>
        res.ok ? res.json() : Promise.reject("Failed to load classes")
      )
      .then((data) => {
        classSelect.innerHTML =
          "<option disabled selected>Choose Class</option>";
        data.forEach((cls) => {
          let opt = document.createElement("option");
          opt.value = cls.id;
          opt.textContent = cls.name;
          classSelect.appendChild(opt);
        });
        classSelect.disabled = false;
      })
      .catch((err) => {
        console.error(err);
        classSelect.innerHTML =
          "<option disabled selected>Error loading data</option>";
      });
  });
}

//============================== Add Student =======================================

document.getElementById("addStudentForm").addEventListener("submit", (e) => {
  e.preventDefault(); // prevent default form reload

  const first_name = document
    .querySelector('[name="first_name"]')
    .value.trim()
    .toUpperCase();
  const last_name = document
    .querySelector('[name="last_name"]')
    .value.trim()
    .toUpperCase();
  const email = document
    .querySelector('[name="email"]')
    .value.trim()
    .toLowerCase();
  const gender = document.querySelector('[name="gender"]').value.toLowerCase();
  const dob = document.querySelector('[name="dob"]').value;
  const class_id = document.querySelector('[name="class_id"]').value;

  fetch("../api/student/add_student.php", {
    method: "POST",
    headers: { "Content-Type": "application/json" },
    body: JSON.stringify({
      first_name,
      last_name,
      email,
      gender,
      dob,
      class_id,
    }),
  })
    .then((response) => {
      if (!response.ok) {
        throw new Error("Network error: " + response.status);
      }
      return response.json();
    })
    .then((data) => {
      if (data.success) {
        document.getElementById("addStudentForm").reset();
        document.querySelector(".close").click();
        showToast(data.message, "bg-success");
        loadStudents();
      } else {
        showToast(data.message, "bg-danger");
      }
    })
    .catch((error) => {
      console.error("Error adding student:", error);
      showToast(`Error adding student: ${error}`, "bg-danger");
    });
});

//============================== Delete Student ================================

document.addEventListener("click", (e) => {
  if (
    e.target.classList.contains("deletebtn") ||
    e.target.closest(".deletebtn")
  ) {
    const button = e.target.closest(".deletebtn");
    const id = button.getAttribute("data-id");

    if (confirm("Are you sure you want to delete this student?")) {
      fetch(`../api/student/delete_student.php`, {
        method: "DELETE",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ id: id }),
      })
        .then((res) => res.json())
        .then((data) => {
          if (data.success) {
            showToast(data.message, "bg-success");
            autoSearch();
          } else {
            showToast(data.message, "bg-danger");
          }
        })
        .catch((err) => {
          showToast("Network error. Could not delete student.", "bg-danger");
          console.error("Delete Error:", err);
        });
    }
  }
});

//============================== Delete Multipule Student ================================

const multiDeleteBtn = document.getElementById("multiDeleteBtn");

multiDeleteBtn.addEventListener("click", () => {
  const studentIds = [
    ...document.querySelectorAll('input[type="checkbox"]:checked'),
  ].map((ch) => ch.value);

  if (studentIds.length === 0) {
    showToast("No students selected.", "bg-info");
    return;
  }

  if (confirm("Are you sure you want to delete selected students?")) {
    fetch("../api/student/delete_student.php", {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify({ ids: studentIds }),
    })
      .then((res) => res.json())
      .then((data) => {
        if (data.success) {
          showToast(data.message, "bg-success");
          autoSearch();
          document.getElementById("checkAll").checked = false;
        } else {
          showToast(data.message, "bg-danger");
        }
      })
      .catch((err) => {
        console.error("Error deleting students:", err);
        showToast("Network error. Could not delete students.", "bg-danger");
      });
  }
});

//============================== Modify Student ================================

document.getElementById("ModifyStudentForm").addEventListener("submit", (e) => {
  e.preventDefault();

  const modifyModal = document.getElementById("modifyModal");

  const id = modifyModal.querySelector('[name="id"]').value.trim();
  const first_name = modifyModal
    .querySelector('[name="first_name"]')
    .value.trim()
    .toUpperCase();
  const last_name = modifyModal
    .querySelector('[name="last_name"]')
    .value.trim()
    .toUpperCase();
  const email = modifyModal
    .querySelector('[name="email"]')
    .value.trim()
    .toLowerCase();
  const gender = modifyModal
    .querySelector('[name="gender"]')
    .value.toLowerCase();
  const date_of_birth = modifyModal.querySelector('[name="dob"]').value;
  const class_id = modifyModal.querySelector('[name="class_id"]').value;

  fetch(`../api/student/update_student.php`, {
    method: "PUT",
    headers: { "Content-Type": "application/json" },
    body: JSON.stringify({
      id,
      first_name,
      last_name,
      email,
      gender,
      date_of_birth,
      class_id,
    }),
  })
    .then((response) => response.json())
    .then((data) => {
      if (data.success) {
        showToast(data.message, "bg-primary");
        document.getElementById("ModifyStudentForm").reset();
        document.querySelector(".close2").click();
        autoSearch();
      } else {
        showToast(data.message, "bg-danger");
      }
    })
    .catch((error) => {
      showToast("Network error occurred.", "bg-danger");
      console.error("Update Error:", error);
    });
});

//============================== Search Student ================================

let currentPage = 1;
let totalPages = 1;

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

  const params = new URLSearchParams({
    pole_id: poleId || "",
    filiere_id: filiereId || "",
    class_id: classId || "",
    page: page,
  });

  fetch(`../api/student/search_students.php?${params.toString()}`)
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
  tbody.innerHTML = "";

  if (data.length === 0) {
    tbody.innerHTML = `<tr><td colspan="8" class='text-center'>No students found.</td></tr>`;
    return;
  }

  data.forEach((student) => {
    const row = document.createElement("tr");
    row.innerHTML = `
      <td class="py-3 text-center"><input class="form-check-input item-checkbox" value="${student.id}" name="item-checkbox" type="checkbox"></td>
      <td class="py-3">${student.first_name}</td>
      <td class="py-3">${student.last_name}</td>
      <td class="py-3">${student.email}</td>
      <td class="py-3">${student.filiere_name}</td>
      <td class="py-3">${student.class_name}</td>
      <td class="py-3">${student.gender}</td>
      <td class="py-3">${student.date_of_birth}</td>
      <td class="py-3">
        <button class="editbtn" data-id="${student.id}">
          <i class="fa-regular fa-pen-to-square"></i>
        </button>
        <button class="deletebtn" data-id="${student.id}">
          <i class="fa-regular fa-trash-can"></i>
        </button>
      </td>
    `;
    tbody.appendChild(row);
  });

  initCheckboxes();
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

// Initialize dropdowns and search
document.addEventListener("DOMContentLoaded", () => {
  setupDependentDropdowns2(document);
  autoSearch();
});

//============================== Upload Excel File =====================================

document.getElementById("fileInput").addEventListener("change", function (e) {
  const file = e.target.files[0];
  if (!file) return;

  const validExtensions = [".xlsx", ".xls", ".csv"];
  const fileExtension = file.name.split(".").pop().toLowerCase();

  if (!validExtensions.includes(`.${fileExtension}`)) {
    showToast("Only Excel files (.xlsx, .xls, .csv) are allowed.", "bg-danger");
    this.value = "";
    return;
  }

  const reader = new FileReader();
  reader.onload = function (e) {
    const data = new Uint8Array(e.target.result);
    const workbook = XLSX.read(data, { type: "array" });

    const sheetName = workbook.SheetNames[0];
    const worksheet = workbook.Sheets[sheetName];

    const jsonData = XLSX.utils.sheet_to_json(worksheet, { header: 1 });
    const rows = jsonData.slice(1); // Skip header row

    let successCount = 0;
    let skippedCount = 0;

    (async () => {
      for (let index = 0; index < rows.length; index++) {
        const row = rows[index];

        if (row.length >= 7) {
          const student = {
            first_name: row[0].toUpperCase(),
            last_name: row[1].toUpperCase(),
            email: row[2].toLowerCase(),
            class_name: row[4],
            gender: row[5].toLowerCase(),
            dob: row[6],
          };

          console.log(student);

          try {
            const res = await fetch("../api/student/upload_students.php", {
              method: "POST",
              headers: {
                "Content-Type": "application/json",
              },
              body: JSON.stringify(student),
            });

            const text = await res.text();
            let result;
            try {
              result = JSON.parse(text);
            } catch (err) {
              console.error("Invalid JSON:", text);
              skippedCount++;
              continue;
            }

            if (result.success) {
              successCount++;
            } else {
              skippedCount++;
              console.warn(`Skipped row ${index + 2}: ${result.message}`);
            }
          } catch (error) {
            console.error(`Error adding student at row ${index + 2}:`, error);
            skippedCount++;
          }
        }
      }

      // Show result after loop ends
      showToast(
        `${successCount} students added. ${skippedCount} skipped.`,
        "bg-success"
      );
      loadStudents();
    })();
  };

  reader.readAsArrayBuffer(file);
});

//============================== Check All =====================================

document.addEventListener("DOMContentLoaded", function () {
  // Wait a brief moment if data loads after DOMContentLoaded
  setTimeout(initCheckboxes, 100);
});

function initCheckboxes() {
  const selectAll = document.getElementById("checkAll");
  const checkboxes = document.querySelectorAll(".item-checkbox");

  if (!checkboxes.length) return;

  // "Select All"
  selectAll.onclick = () => {
    checkboxes.forEach((cb) => (cb.checked = selectAll.checked));
  };

  // If one unchecked → "Select All" should uncheck
  checkboxes.forEach((cb) => {
    cb.onchange = () => {
      const allChecked = [...checkboxes].every((c) => c.checked);
      selectAll.checked = allChecked;
    };
  });
}

//============================== Add modal =====================================
document.getElementById("openModalBtn").onclick = () => {
  const modal = document.getElementById("studentModal");
  modal.style.display = "block";
  setupDependentDropdowns(modal);
};

document.querySelector(".close").onclick = () =>
  document.getElementById("cancelBtn").click();

document.getElementById("cancelBtn").onclick = () => {
  // Close modal
  document.getElementById("studentModal").style.display = "none";

  // Reset the form
  const form = document.getElementById("addStudentForm");
  form.reset();

  // Clear and reset Filiere and Class dropdowns
  const filiereSelect = form.querySelector(".filiere");
  const classSelect = form.querySelector(".class_id");

  filiereSelect.innerHTML =
    '<option value="" disabled selected>Filiere</option>';
  filiereSelect.disabled = true;

  classSelect.innerHTML = '<option value="" disabled selected>Class</option>';
  classSelect.disabled = true;
};

//============================== Modify modal ================================

document.addEventListener("click", (e) => {
  if (e.target.classList.contains("editbtn") || e.target.closest(".editbtn")) {
    const modal = document.getElementById("modifyModal");
    modal.style.display = "block";
    setupDependentDropdowns(modal);

    const button = e.target.closest(".editbtn");
    const id = button.getAttribute("data-id");

    fetch(`../api/student/get_one_student.php?id=${id}`)
      .then((response) => response.json())
      .then((data) => {
        // Fill simple inputs
        modal.querySelector("[name='id']").value = data.id;
        modal.querySelector("[name='first_name']").value = data.first_name;
        modal.querySelector("[name='last_name']").value = data.last_name;
        modal.querySelector("[name='email']").value = data.email;
        modal.querySelector("[name='gender']").value = data.gender;
        modal.querySelector("[name='dob']").value = data.date_of_birth;

        // Fill dropdowns in sequence: pole -> filiere -> class
        const poleSelect = modal.querySelector(".pole");
        const filiereSelect = modal.querySelector(".filiere");
        const classSelect = modal.querySelector(".class_id");

        // 1. Set Pole
        poleSelect.value = data.pole_id;
        poleSelect.dispatchEvent(new Event("change")); // trigger fetch of filieres

        // 2. Wait for filieres to load
        setTimeout(() => {
          filiereSelect.value = data.filiere_id;
          filiereSelect.dispatchEvent(new Event("change")); // trigger fetch of classes

          // 3. Wait for classes to load
          setTimeout(() => {
            classSelect.value = data.class_id;
          }, 300);
        }, 300);
      })
      .catch((error) => console.error(error));
  }
});

document.querySelector(".close2").onclick = () =>
  (document.getElementById("modifyModal").style.display = "none");

document.getElementById("cancelBtn2").onclick = () =>
  (document.getElementById("modifyModal").style.display = "none");

// =========================================================================

document.addEventListener("DOMContentLoaded", () => {
  setupDependentDropdowns2(document.getElementById("search"));
});
