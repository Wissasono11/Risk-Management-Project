// input check-box
document.addEventListener("DOMContentLoaded", () => {
  const sidebar = document.querySelector(".sidebar");
  const mainContent = document.querySelector(".main-content");
  const toggleBtn = document.getElementById("sidebar-toggle");

  toggleBtn.addEventListener("click", () => {
    sidebar.classList.toggle("collapsed");
    mainContent.classList.toggle("collapsed");
  });
});

// close button idebar untuk mobile
document.addEventListener("DOMContentLoaded", function () {
  const closeButton = document.querySelector(".fa-close");
  const sidebar = document.querySelector(".sidebar");

  closeButton.addEventListener("click", () => {
    sidebar.classList.add("collapsed");
  });

  const openButton = document.querySelector("#sidebar-toggle");
  openButton.addEventListener("click", () => {
    sidebar.classList.add("expanded");
  });
});
