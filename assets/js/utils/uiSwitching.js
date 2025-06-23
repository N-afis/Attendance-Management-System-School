export function setupSwitcher(studentsBtnId, teachersBtnId, studentCardId, teacherCardId) {
  const studentsBtn = document.getElementById(studentsBtnId);
  const teachersBtn = document.getElementById(teachersBtnId);
  const studentCard = document.getElementById(studentCardId);
  const teacherCard = document.getElementById(teacherCardId);

  if (!studentsBtn || !teachersBtn || !studentCard || !teacherCard) return;

  studentsBtn.addEventListener("click", () => {
    studentsBtn.classList.add("active");
    teachersBtn.classList.remove("active");

    studentCard.classList.remove("d-none");
    studentCard.classList.add("d-block");

    teacherCard.classList.remove("d-block");
    teacherCard.classList.add("d-none");
  });

  teachersBtn.addEventListener("click", () => {
    teachersBtn.classList.add("active");
    studentsBtn.classList.remove("active");

    teacherCard.classList.remove("d-none");
    teacherCard.classList.add("d-block");

    studentCard.classList.remove("d-block");
    studentCard.classList.add("d-none");
  });
}
