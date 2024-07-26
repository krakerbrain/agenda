document.addEventListener("DOMContentLoaded", function () {
  document.getElementById("admin").classList.add("active");
  fetch("../user_admin/admin.php")
    .then((response) => response.text())
    .then((data) => {
      document.getElementById("main-content").innerHTML = data;
    })
    .catch((error) => console.error("Error al cargar admin:", error));
});

document.getElementById("navbar-toggle").addEventListener("click", function () {
  document.querySelector(".navbar-nav").classList.toggle("active");
});

document.querySelectorAll(".nav-link").forEach((link) => {
  link.addEventListener("click", function (event) {
    event.preventDefault();
    document.querySelectorAll(".nav-link").forEach((nav) => nav.classList.remove("active"));
    this.classList.add("active");
    fetch(`../user_admin/${this.id}.php`)
      .then((response) => response.text())
      .then((data) => {
        document.getElementById("main-content").innerHTML = data;
      })
      .catch((error) => console.error(`Error al cargar ${this.id}:`, error));
  });
});
