export function showToast(message, colorClass = "bg-primary") {
  const toastEl = document.getElementById("customToast");

  if (!toastEl) return;

  // Update message
  const toastMessage = document.getElementById("toastMessage");
  if (toastMessage) toastMessage.textContent = message;

  // Update color
  const toastClasses = toastEl.classList;
  toastClasses.remove("bg-primary", "bg-success", "bg-danger", "bg-warning", "bg-info");
  toastClasses.add(colorClass);

  // Show the toast
  const toast = new bootstrap.Toast(toastEl, {
    delay: 5000,
    autohide: true,
  });

  toast.show();
}