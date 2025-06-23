import { showToast } from "./utils/toast.js";

function loadTeachers(page = 1, limit = 20) {
  // Ensure page is a number (in case event object gets passed)
  const pageNumber = typeof page === "number" ? page : 1;

  fetch(`../api/teacher/get_teachers.php?page=${pageNumber}&limit=${limit}`)
    .then((response) => {
      if (!response.ok) {
        throw new Error(`HTTP error! status: ${response.status}`);
      }
      return response.json();
    })
    .then((data) => {
      if (data.success) {
        renderTeachers(data.data);
        setupPagination(data.pagination);
      } else {
        showToast(data.message, "bg-danger");
      }
    })
    .catch((error) => {
      showToast("Could not fetch teachers", "bg-danger");
      console.error("Fetch error:", error);
    });
}

document.addEventListener("DOMContentLoaded", () => {
  loadTeachers(1, 20);
  initSearch();
});
// //============================== Display teachers ================================

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
    btn.addEventListener("click", (e) => {
      e.preventDefault(); // Prevent default behavior
      loadTeachers(i); // Pass the page number directly
    });
    paginationContainer.appendChild(btn);
  }
  document.getElementById("checkAll").checked = false;
}

// //============================== Add Teacher =======================================

document.getElementById("addTeacherForm").addEventListener("submit", (e) => {
  e.preventDefault(); // prevent default form reload

  const first_name = document
    .querySelector('[name="first_name"]')
    .value.trim()
    .toUpperCase()
    .toUpperCase();
  const last_name = document
    .querySelector('[name="last_name"]')
    .value.trim()
    .toUpperCase()
    .toUpperCase();
  const email = document
    .querySelector('[name="email"]')
    .value.trim()
    .toLowerCase()
    .toLowerCase();
  const gender = document.querySelector('[name="gender"]').value.toLowerCase();
  const dob = document.querySelector('[name="dob"]').value;

  fetch("../api/teacher/add_teacher.php", {
    method: "POST",
    headers: { "Content-Type": "application/json" },
    body: JSON.stringify({
      first_name,
      last_name,
      email,
      gender,
      dob,
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
        document.getElementById("addTeacherForm").reset();
        document.querySelector(".close").click();
        showToast(data.message, "bg-success");
        loadTeachers();
      } else {
        showToast(data.message, "bg-danger");
      }
    })
    .catch((error) => {
      console.error("Error adding teacher:", error);
      showToast(`Error adding teacher: ${error}`, "bg-danger");
    });
});

// //============================== Delete Teacher ================================

document.addEventListener("click", (e) => {
  if (
    e.target.classList.contains("deletebtn") ||
    e.target.closest(".deletebtn")
  ) {
    const button = e.target.closest(".deletebtn");
    const id = button.getAttribute("data-id");

    if (confirm("Are you sure you want to delete this teacher?")) {
      fetch("../api/teacher/delete_teacher.php", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ id: id }),
      })
        .then((res) => res.json())
        .then((data) => {
          if (data.success) {
            showToast(data.message, "bg-success");
            loadTeachers();
          } else {
            showToast(data.message, "bg-danger");
          }
        })
        .catch((err) => {
          showToast("Network error. Could not delete teacher.", "bg-danger");
          console.error("Delete Error:", err);
        });
    }
  }
});

//============================== Delete Multipule Teachers ================================

const multiDeleteBtn = document.getElementById("multiDeleteBtn");

multiDeleteBtn.addEventListener("click", () => {
  const teacherIds = [
    ...document.querySelectorAll('input[type="checkbox"]:checked'),
  ].map((ch) => ch.value);

  if (teacherIds.length === 0) {
    showToast("No teachers selected.", "bg-info");
    return;
  }

  if (confirm("Are you sure you want to delete selected teachers?")) {
    fetch("../api/teacher/delete_teacher.php", {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify({ ids: teacherIds }),
    })
      .then((res) => res.json())
      .then((data) => {
        if (data.success) {
          showToast(data.message, "bg-success");
          loadTeachers();
          document.getElementById("checkAll").checked = false;
        } else {
          showToast(data.message, "bg-danger");
        }
      })
      .catch((err) => {
        console.error("Error deleting teachers:", err);
        showToast("Network error. Could not delete teachers.", "bg-danger");
      });
  }
});

// //============================== Modify Teacher ================================

document.getElementById("ModifyTeacherForm").addEventListener("submit", (e) => {
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

  fetch(`../api/teacher/update_teacher.php`, {
    method: "PUT",
    headers: { "Content-Type": "application/json" },
    body: JSON.stringify({
      id,
      first_name,
      last_name,
      email,
      gender,
      date_of_birth,
    }),
  })
    .then((response) => response.json())
    .then((data) => {
      if (data.success) {
        showToast(data.message, "bg-primary");
        document.getElementById("ModifyTeacherForm").reset();
        document.querySelector(".close2").click();
        loadTeachers();
      } else {
        showToast(data.message, "bg-danger");
      }
    })
    .catch((error) => {
      showToast("Network error occurred.", "bg-danger");
      console.error("Update Error:", error);
    });
});

// //============================== Search Teacher ================================

let currentSearchKeyword = "";
let currentSearchPage = 1;
const limit = 20;

function initSearch() {
  const searchInput = document.getElementById("searchInput");
  const teachersTable = document.getElementById("teachersTable");

  if (!searchInput || !teachersTable) {
    console.error("Missing searchInput or teachersTable in the DOM.");
    return;
  }

  // Handle real-time search input
  searchInput.addEventListener("input", () => {
    currentSearchKeyword = searchInput.value.trim();
    currentSearchPage = 1;
    searchTeachers(currentSearchKeyword, currentSearchPage, limit);
  });
};

// ======================== SEARCH TEACHERS ========================
function searchTeachers(keyword, page = 1, limit = 20) {
  const params = new URLSearchParams({
    keyword: keyword || "",
    page,
    limit,
  });

  fetch(`../api/teacher/search_teachers.php?${params}`)
    .then((res) => {
      if (!res.ok) throw new Error("Failed to fetch teachers.");
      return res.json();
    })
    .then((data) => {
      renderTeachers(data.data);
      setupTeacherPagination(data.pagination, true); // search mode
    })
    .catch((error) => {
      console.error("Search Error:", error);
    });
}

// ======================== RENDER TEACHERS ========================
function renderTeachers(data) {
  const teachersTable = document.getElementById("teachersTable");
  const pagination = document.getElementById("pagination");

  teachersTable.innerHTML = "";
  pagination.innerHTML = "";

  if (!Array.isArray(data) || data.length === 0) {
    teachersTable.innerHTML = `<tr><td colspan="7" class="text-center">No teachers found.</td></tr>`;
    return;
  }

  data.forEach((teacher) => {
    const row = document.createElement("tr");
    row.innerHTML = `
      <td class="py-3 text-center">
        <input class="form-check-input item-checkbox" value="${teacher.id}" type="checkbox">
      </td>
      <td class="py-3">${teacher.last_name}</td>
      <td class="py-3">${teacher.first_name}</td>
      <td class="py-3">${teacher.email}</td>
      <td class="py-3">${teacher.gender}</td>
      <td class="py-3">${teacher.date_of_birth}</td>
      <td class="py-3">
        <button class="editbtn" data-id="${teacher.id}">
          <i class="fa-regular fa-pen-to-square"></i>
        </button>
        <button class="deletebtn" data-id="${teacher.id}">
          <i class="fa-regular fa-trash-can"></i>
        </button>
      </td>`;
    teachersTable.appendChild(row);
  });

  initCheckboxes(); // reset select all logic
  document.getElementById("checkAll").checked = false;
}

// ======================== PAGINATION ========================
function setupTeacherPagination(pagination, isSearch = false) {
  const container = document.getElementById("pagination");
  container.innerHTML = "";

  if (!pagination || pagination.total_pages <= 1) return;

  for (let i = 1; i <= pagination.total_pages; i++) {
    const btn = document.createElement("button");
    btn.innerText = i;
    btn.className = "btn btn-sm btn-outline-primary mx-1";
    if (i === pagination.current_page) btn.classList.add("active");

    btn.addEventListener("click", (e) => {
      e.preventDefault();
      currentSearchPage = i;
      if (isSearch && currentSearchKeyword !== "") {
        searchTeachers(currentSearchKeyword, i, limit);
      } else {
        loadTeachers(i, limit); // fallback if not in search mode
      }
    });

    container.appendChild(btn);
  }

  document.getElementById("checkAll").checked = false;
}

// //============================== Upload Excel File =====================================

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

    rows.forEach(async (row, index) => {
      if (row.length >= 5) {
        const teacher = {
          first_name: row[0].toUpperCase(),
          last_name: row[1].toUpperCase(),
          email: row[2].toLowerCase(),
          gender: row[3].toLowerCase(),
          dob: row[4],
        };
        
        try {
          const res = await fetch("../api/teacher/upload_teachers.php", {
            method: "POST",
            headers: {
              "Content-Type": "application/json",
            },
            body: JSON.stringify(teacher),
          });

          const result = await res.json();

          if (result.success) {
            successCount++;
          } else {
            skippedCount++;
            console.warn(`Skipped row ${index + 2}: ${result.message}`);
          }

          // Optionally show a summary after last row
          if (index === rows.length - 1) {
            showToast(
              `${successCount} teachers added. ${skippedCount} skipped.`,
              "bg-success"
            );
            loadTeachers();
          }
        } catch (error) {
          console.error("Error adding teacher:", error);
          skippedCount++;
        }
      }
    });
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
  const modal = document.getElementById("teacherModal");
  modal.style.display = "block";
};

document.querySelector(".close").onclick = () =>
  document.getElementById("cancelBtn").click();

document.getElementById("cancelBtn").onclick = () => {
  // Close modal
  document.getElementById("teacherModal").style.display = "none";

  // Reset the form
  const form = document.getElementById("addTeacherForm");
  form.reset();
};

//============================== Modify modal ================================

document.addEventListener("click", (e) => {
  if (e.target.classList.contains("editbtn") || e.target.closest(".editbtn")) {
    const modal = document.getElementById("modifyModal");
    modal.style.display = "block";

    const button = e.target.closest(".editbtn");
    const id = button.getAttribute("data-id");

    fetch(`../api/teacher/get_one_teacher.php?id=${id}`)
      .then((response) => response.json())
      .then((data) => {
        console.log(data);

        modal.querySelector("[name='id']").value = data.id;
        modal.querySelector("[name='first_name']").value = data.first_name;
        modal.querySelector("[name='last_name']").value = data.last_name;
        modal.querySelector("[name='email']").value = data.email;
        modal.querySelector("[name='gender']").value = data.gender;
        modal.querySelector("[name='dob']").value = data.date_of_birth;
      })
      .catch((error) => console.error(error));
  }
});

document.querySelector(".close2").onclick = () =>
  (document.getElementById("modifyModal").style.display = "none");

document.getElementById("cancelBtn2").onclick = () =>
  (document.getElementById("modifyModal").style.display = "none");
