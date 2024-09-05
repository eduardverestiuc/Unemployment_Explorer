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
