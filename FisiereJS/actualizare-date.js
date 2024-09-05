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

var globalCode = "";
document.getElementById("verify-form").addEventListener("submit", function (e) {
  e.preventDefault();
  var xhr = new XMLHttpRequest();
  xhr.open(
    "POST",
    "/PROIECT_TEHNOLOGII_WEB/FisierePHP/verifica-email-actualizare-date.php",
    true
  );
  xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
  xhr.send("code=" + document.getElementById("code").value);
  xhr.onload = function () {
    if (this.status == 200) {
      var response = JSON.parse(this.response);
      if (response.message == "success") {
        Swal.fire({
          title: "Notificare",
          html: "Email verificat cu succes.",
          icon: "success",
          timer: 3000,
          timerProgressBar: true,
        }).then(() => {
          document.getElementById("update-form").style.display = "block";
          var user = response.user;
          globalCode = response.code;
          console.log(globalCode);
          document.getElementById("email").placeholder = user.email;
          document.getElementById("phoneNumber").placeholder = user.phoneNumber;
          document.getElementById("username").placeholder = user.username;
          document.getElementById("password").placeholder = "********";
          //console.log(response);
        });
      } else if (response.message == "incorrect") {
        Swal.fire({
          title: "Notificare",
          html: "Email-ul introdus nu este asociat cu niciun cont. Mai incercati!",
          icon: "error",
          timer: 3000,
          timerProgressBar: true,
        });
        document.getElementById("update-form").style.display = "none";
      } else {
        console.log(response);
      }
    }
  };
});

document.getElementById("update-form").addEventListener("submit", function (e) {
  e.preventDefault();

  var inputCode = document.getElementById("codVerificare").value;
  globalCode = globalCode.toString();
  //console.log(typeof inputCode, inputCode);
  //console.log(typeof globalCode, globalCode);

  if (inputCode !== globalCode) {
    Swal.fire({
      title: "Eroare!",
      text: "Codul introdus este gresit.",
      icon: "error",
      timer: 3000,
      timerProgressBar: true,
    });
    return;
  }
  var emailUpdate = document.getElementById("email").placeholder;
  var email =
    document.getElementById("email").value ||
    document.getElementById("email").placeholder;
  var phoneNumber =
    document.getElementById("phoneNumber").value ||
    document.getElementById("phoneNumber").placeholder;
  var username =
    document.getElementById("username").value ||
    document.getElementById("username").placeholder;
  var password = document.getElementById("password").value || "********";

  // Creează obiectul JSON cu datele de trimis
  var dataToSend = {
    emailUpdate: emailUpdate,
    email: email,
    phoneNumber: phoneNumber,
    username: username,
    password: password,
  };

  fetch("/PROIECT_TEHNOLOGII_WEB/FisierePHP/update-date.php", {
    method: "PUT",
    headers: {
      "Content-Type": "application/json",
    },
    body: JSON.stringify(dataToSend),
  })
    .then((response) => response.json())
    .then((data) => {
      console.log("Success:", data);
      if (data.message === "success") {
        Swal.fire({
          title: "Notificare",
          html: "Datele au fost actualizate cu succes. Veti fi redirectionat catre pagina de login in cateva secunde.",
          icon: "success",
          timer: 3000,
          timerProgressBar: true,
          willClose: () => {
            window.location.href =
              "/PROIECT_TEHNOLOGII_WEB/FisiereHTML/login.html";
          },
        });
      } else if (data.message === "incorrect") {
        Swal.fire({
          title: "Eroare!",
          text: "Actualizarea datelor a esuat.",
          icon: "error",
          timer: 3000,
          timerProgressBar: true,
        });
      } else if (data.message === "exists") {
        Swal.fire({
          title: "Eroare!",
          text: "Datele introduse sunt deja asociate unui cont.",
          icon: "error",
          timer: 3000,
          timerProgressBar: true,
        });
      }
    })
    .catch((error) => {
      console.error("Error:", error);
    });
});
