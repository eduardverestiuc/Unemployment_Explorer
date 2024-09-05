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

function rezolva(event) {
  event.preventDefault();

  var username = document.getElementById("name").value;
  var password = document.getElementById("parola").value;

  var data = { username: username, password: password };

  fetch("/PROIECT_TEHNOLOGII_WEB/FisierePHP/login.php", {
    method: "POST",
    headers: {
      "Content-Type": "application/json",
    },
    body: JSON.stringify(data),
  })
    .then((response) => response.json())
    .then((data) => {
      if (data.success) {
        localStorage.setItem("jwt", data.token);
        if (data.admin) {
          Swal.fire({
            title: "Notificare",
            html: "Bine ai venit, Adminule!",
            icon: "success",
            timer: 3000,
            timerProgressBar: true,
            willClose: () => {
              window.location.href =
                "/PROIECT_TEHNOLOGII_WEB/FisiereHTML/admin.html";
            },
          });
        } else {
          Swal.fire({
            title: "Notificare",
            html: "Autentificare reușită... Veti fi redirectionat catre pagina de statistici in cateva secunde.",
            icon: "success",
            timer: 3000,
            timerProgressBar: true,
            willClose: () => {
              window.location.href =
                "/PROIECT_TEHNOLOGII_WEB/FisiereHTML/statistici.html";
            },
          });
        }
      } else {
        Swal.fire({
          title: "Notificare",
          html: "Autentificare eșuată. Verificați datele introduse!",
          icon: "error",
          timer: 3000,
          timerProgressBar: true,
        });
      }
    });
}
