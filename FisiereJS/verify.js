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

document.getElementById("verify-form").addEventListener("submit", function (e) {
  e.preventDefault();
  var xhr = new XMLHttpRequest();
  xhr.open("POST", "/PROIECT_TEHNOLOGII_WEB/FisierePHP/verify_code.php", true);
  xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
  xhr.send("code=" + document.getElementById("code").value);
  xhr.onload = function () {
    if (this.status == 200) {
      if (this.response == "success") {
        Swal.fire({
          title: "Notificare",
          html: "Inregistrare cu succes... Veti fi redirectionat catre pagina de login in cateva secunde.",
          icon: "success",
          timer: 3000,
          timerProgressBar: true,
        }).then(() => {
          window.location.href =
            "/PROIECT_TEHNOLOGII_WEB/FisiereHTML/login.html";
        });
      } else if (this.response == "incorrect") {
        Swal.fire({
          title: "Notificare",
          html: "Codul introdus este incorect. Mai incercati!",
          icon: "error",
          timer: 3000,
          timerProgressBar: true,
        });
      } else {
        console.log(this.response);
      }
    }
  };
});
