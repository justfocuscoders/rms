document.addEventListener("DOMContentLoaded", function() {
  const sidebar = document.getElementById("sidebar");
  const toggleBtn = document.querySelector(".toggle-icon");

  toggleBtn.addEventListener("click", function() {
    sidebar.classList.toggle("collapsed");
    document.querySelector(".page-wrapper").classList.toggle("expanded");
    document.querySelector(".topbar").classList.toggle("expanded");
  });
});
