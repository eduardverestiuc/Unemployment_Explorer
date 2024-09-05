var userLeft = false;

window.onbeforeunload = function () {
  userLeft = true;
  document.getElementById("registerForm").reset();
};

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

document.addEventListener("DOMContentLoaded", function () {
  if (!userLeft) {
    // verifica daca utilizatorul a parasit pagina
    document
      .getElementById("registerForm")
      .addEventListener("submit", function (e) {
        e.preventDefault();
        var xhr = new XMLHttpRequest();
        xhr.open("POST", "/PROIECT_TEHNOLOGII_WEB/FisierePHP/signup.php", true);
        xhr.setRequestHeader(
          "Content-Type",
          "application/x-www-form-urlencoded"
        );
        xhr.send(new URLSearchParams(new FormData(this)).toString());

        xhr.onload = function () {
          if (this.status == 200) {
            if (this.response == "exists") {
              Swal.fire({
                title: "Notificare",
                html: "Username, email sau numar de telefon deja existent.",
                icon: "error",
                timer: 3000,
                timerProgressBar: true,
              });
            } else if (this.response == "verify") {
              Swal.fire({
                title: "Notificare",
                html: "Vei fi redirecționat către pagina de confirmare a email-ului.",
                icon: "success",
                timer: 3000,
                timerProgressBar: true,
                willClose: () => {
                  window.location.href =
                    "/PROIECT_TEHNOLOGII_WEB/FisiereHTML/verify.html";
                },
              });
            } else {
              console.log(this.response);
            }
          }
        };
      });
  }
});
