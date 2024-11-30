document.querySelector("#nav-toggle").addEventListener("click", () => {
  document.querySelector(".sidebar").classList.toggle("collapsed");
  document.querySelector(".main-content").classList.toggle("expanded");
});
