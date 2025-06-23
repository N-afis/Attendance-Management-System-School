// ========================== IMPORTS =============================

import { setupSwitcher } from "./utils/uiSwitching.js";

// ==================================== UI SWITCHING ============================================

document.addEventListener("DOMContentLoaded", () => {
  setupSwitcher("studentsBtn", "teachersBtn", "studentCard", "teacherCard");
});

// ==================================== Most Absent Students Card ============================================

fetch("../api/dashboard/topAbsent.php")
  .then((res) => res.json())
  .then((data) => {
    console.log(data);

    fillStudentList(data);
  })
  .catch((e) => console.error(`Most Absent Error: ${e}`));

// Fill student list
function fillStudentList(data) {
  const list = document.getElementById("topAbsentStudents");
  data.forEach((s) => {
    const li = document.createElement("li");
    li.className = "mb-2 d-flex justify-content-between";
    li.innerHTML = `<span>👤 ${s.first_name} ${s.last_name} | ${s.class_name}</span><span>${s.absentTimes}x</span>`;
    list.appendChild(li);
  });
}

// ==================================== Chart Absences by Filière  ============================================

fetch("../api/dashboard/absencesByFiliere.php")
  .then((res) => res.json())
  .then((data) => {
    console.log(data);
    const chartData = {
      labels: data.map((item) => item.filiere_name),
      data: data.map((item) => item.absentTimes),
    };
    fillFiliereBarChart(chartData);
  })
  .catch((e) => console.error(`Most Absent Error: ${e}`));

// Filière bar chart
function fillFiliereBarChart(chartData) {
  const ctx = document.getElementById("filiereAbsenceChart").getContext("2d");
  new Chart(ctx, {
    type: "bar",
    data: {
      labels: chartData.labels,
      datasets: [
        {
          label: "Absences",
          data: chartData.data,
          backgroundColor: "#2B9CB8",
          borderRadius: 8,
        },
      ],
    },
    options: {
      scales: {
        y: {
          beginAtZero: true,
        },
      },
    },
  });
}
