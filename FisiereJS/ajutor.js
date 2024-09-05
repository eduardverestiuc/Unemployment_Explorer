window.onload = function () {
  var jwt = localStorage.getItem("jwt");
  var overlay = document.getElementById("overlay");
  var body = document.body;
  overlay.style.display = "block";
  if (!jwt) {
    body.style.display = "block";
  } else {
    fetch("/PROIECT_TEHNOLOGII_WEB/FisierePHP/verifyJWT.php", {
      method: "POST",
      headers: {
        "Content-Type": "application/json",
      },
      body: JSON.stringify({ token: jwt }),
    })
      .then((response) => response.json())
      .then((data) => {
        if (!data.success || (data.role !== "admin" && data.role === "user")) {
          window.location.href =
            "/PROIECT_TEHNOLOGII_WEB/FisiereHTML/statistici.html";
        } else if (data.role === "admin") {
          window.location.href =
            "/PROIECT_TEHNOLOGII_WEB/FisiereHTML/admin.html";
        } else {
          overlay.style.display = "none";
          body.style.display = "block";
        }
      });
  }
};

document.getElementById("mesaj").addEventListener("input", function () {
  var charCount = this.value.length;
  var maxChars = this.getAttribute("maxlength");
  document.getElementById(
    "charCount"
  ).textContent = `${charCount}/${maxChars} caractere`;
});

document.addEventListener("DOMContentLoaded", function () {
  const form = document.querySelector("form");
  form.addEventListener("submit", function (event) {
    event.preventDefault();

    const email = document.getElementById("email").value;
    const mesaj = document.getElementById("mesaj").value;
    const data = { email: email, mesaj: mesaj };

    fetch("/PROIECT_TEHNOLOGII_WEB/FisierePHP/feedback.php", {
      method: "POST",
      headers: {
        "Content-Type": "application/json",
      },
      body: JSON.stringify(data),
    })
      .then((response) => response.json())
      .then((data) => {
        if (data.message === "nuexistaemail") {
          Swal.fire({
            title: "Notificare",
            html: "Nu exista niciun cont asociat cu acest email.",
            icon: "error",
            timer: 3000,
            timerProgressBar: true,
          });
        } else if (data.message === "succes") {
          Swal.fire({
            title: "Succes",
            text: "Mesaj trimis cu succes!",
            icon: "success",
            timer: 3000,
            timerProgressBar: true,
          });
        } else if (data.message === "limitaAtinsa") {
          Swal.fire({
            title: "Limită atinsă",
            text: "Ai atins limita de mesaje pentru astăzi, un singur mesaj la 24h.",
            icon: "warning",
            timer: 3000,
            timerProgressBar: true,
          });
        } else {
          console.log("Success:", data);
        }
      })
      .catch((error) => {
        console.error("Error:", error);
      });
  });
});
