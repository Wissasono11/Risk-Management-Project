// profile.js
document.addEventListener("DOMContentLoaded", function () {
  const editProfileBtn = document.querySelector(".edit-button");
  const modal = document.getElementById("editProfileModal");
  const form = document.getElementById("profileForm");
  const fileInput = form.querySelector('input[type="file"]');
  const preview = form.querySelector(".file-preview");
  const profilePicInput = document.getElementById("profile_picture");
  const profileForm = document.getElementById("profileForm");

  // Show modal
  editProfileBtn?.addEventListener("click", () => {
    modal.classList.add("show");
  });

  // Close modal
  document.querySelectorAll(".modal-close").forEach((btn) => {
    btn.addEventListener("click", () => {
      modal.classList.remove("show");
    });
  });

  // File preview
  fileInput?.addEventListener("change", (e) => {
    const file = e.target.files[0];
    if (file) {
      const reader = new FileReader();
      reader.onload = (e) => {
        preview.innerHTML = `
                    <img src="${e.target.result}" alt="Preview" 
                         style="max-width: 200px; border-radius: 8px;">
                `;
      };
      reader.readAsDataURL(file);
    }
  });

  // Form submit
  form?.addEventListener("submit", async (e) => {
    e.preventDefault();

    try {
      const formData = new FormData(form);
      const response = await fetch(`${baseUrl}/profile/update`, {
        method: "POST",
        body: formData,
      });

      const data = await response.json();

      if (data.success) {
        showToast("Profile updated successfully", "success");
        setTimeout(() => window.location.reload(), 1000);
      } else {
        throw new Error(data.message || "Failed to update profile");
      }
    } catch (error) {
      showToast(error.message, "error");
    }
  });

  profilePicInput?.addEventListener("change", function (e) {
    handleProfilePictureChange(e.target);
  });
});

function handleProfilePictureChange(input) {
  if (input.files && input.files[0]) {
    const formData = new FormData();
    formData.append("profile_picture", input.files[0]);

    fetch(`${baseUrl}/profile/update`, {
      method: "POST",
      body: formData,
    })
      .then((response) => response.json())
      .then((data) => {
        if (data.success) {
          const allProfilePictures =
            document.querySelectorAll(".profile-picture");
          allProfilePictures.forEach((img) => {
            img.src = data.profile_picture;
          });
          window.location.href = `${baseUrl}/profile`;
        } else {
          throw new Error(data.message);
        }
      })
      .catch((error) => {
        console.error("Error:", error);
        alert("Terjadi kesalahan saat mengupload foto");
      });
  }
}
