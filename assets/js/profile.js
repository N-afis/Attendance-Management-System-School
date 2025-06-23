import { showToast } from "./utils/toast.js";

// =================================== Profile img list ===========================================

document.addEventListener("DOMContentLoaded", function () {
  const profileImg = document.querySelector(".imgPro");
  const imgList = document.getElementById("imgList");

  // Toggle menu on profile image click
  profileImg.addEventListener("click", function (e) {
    e.stopPropagation();

    const defaultSrc = "../assets/images/img_user.png";
    const currentSrc = profileImg.getAttribute("src");

    if (currentSrc === defaultSrc) {
      document.getElementById("changedImg").click();
    } else {
      imgList.classList.toggle("activer");
    }
  });

  // Close when clicking outside
  document.addEventListener("click", function (e) {
    if (!imgList.contains(e.target) && !profileImg.contains(e.target)) {
      imgList.classList.remove("activer");
    }
  });

  // Prevent closing when clicking inside menu
  imgList.addEventListener("click", function (e) {
    e.stopPropagation();
  });
});

// =================================== Remove Profile Img ===========================================

document.getElementById("removeImg").addEventListener("click", () => {
  const email = document.getElementById("adminEmail").textContent;

  fetch("../api/profile/delete_profile_img.php", {
    method: "POST",
    headers: {
      "Content-Type": "application/json",
    },
    body: JSON.stringify({ email }),
  })
    .then((res) => res.json())
    .then((data) => {
      if (data.success) {
        showToast(data.message, "bg-success");
        // Optionally update UI
        document.querySelector(".imgPro").src = "../assets/images/img_user.png";
        document.getElementById("headerImg").src =
          "../assets/images/img_user.png";
        document.getElementById("imgList").classList.remove("activer");
      } else {
        showToast(data.message, "bg-danger");
      }
    })
    .catch((e) => showToast("Error: " + e.message, "bg-danger"));
});

// =================================== Change Profile Img ===========================================

document.getElementById("changedImg").addEventListener("change", function () {
  const email = document.getElementById("adminEmail").textContent;
  const file = this.files[0];
  if (!file) return;

  const formData = new FormData();
  formData.append("changedImg", file);
  formData.append("email", email);

  fetch("../api/profile/change_profile_img.php", {
    method: "POST",
    body: formData,
  })
    .then((res) => res.json())
    .then((data) => {
      if (data.success) {
        showToast(data.message, "bg-success");

        // Update UI image
        document.querySelector(".imgPro").src = data.new_img_path;
        document.getElementById("headerImg").src = data.new_img_path;

        // Optionally update localStorage
        localStorage.setItem("user_img", data.new_img_path);

        document.getElementById("imgList").classList.remove("activer");
      } else {
        showToast(data.message, "bg-danger");
      }
    })
    .catch((e) => showToast("Upload failed: " + e.message, "bg-danger"));
});

// =================================== Download Profile Img ===========================================

document.getElementById("downloadImg").addEventListener("click", () => {
  const img = document.querySelector(".imgPro");
  const imageUrl = img.getAttribute("src");

  // Extract filename from URL
  const filename = imageUrl.split("/").pop();

  const a = document.createElement("a");
  a.href = imageUrl;
  a.download = filename;
  document.body.appendChild(a);
  a.click();
  document.body.removeChild(a);

  document.getElementById("imgList").classList.remove("activer");
});
