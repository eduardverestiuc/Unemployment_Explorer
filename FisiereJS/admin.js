window.onload = function () {
  var jwt = localStorage.getItem("jwt");
  var overlay = document.getElementById("overlay");
  var body = document.body;
  overlay.style.display = "block";
  if (!jwt) {
    window.location.href = "/PROIECT_TEHNOLOGII_WEB/FisiereHTML/login.html";
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
        } else {
          overlay.style.display = "none";
          body.style.display = "block";
        }
      });
  }
};

var tables = [];
var users = [];
var feedbacks = [];
var feedbacksId = [];
const jwtToken = localStorage.getItem("jwt");
console.log(jwtToken);
fetch("/PROIECT_TEHNOLOGII_WEB/FisierePHP/admin_obtine_datele.php", {
  headers: {
    Authorization: `Bearer ${jwtToken}`,
  },
})
  .then((response) => response.json())
  .then((data) => {
    var tableList = document.getElementById("table-list");
    var userList = document.getElementById("user-list");
    var feedbackList = document.getElementById("feedback-list");
    var feedbackIdList = document.getElementById("feedback-id-list");

    data.tables.forEach((table) => {
      var option = document.createElement("option");
      option.value = table;
      tableList.appendChild(option);
      tables.push(table);
    });

    data.usernames.forEach((user) => {
      var option = document.createElement("option");
      option.value = user;
      userList.appendChild(option);
      users.push(user);
    });

    data.feedbacks.forEach((feedback) => {
      var li = document.createElement("li");
      var option = document.createElement("option");
      option.value = feedback.id;
      feedbackIdList.appendChild(option);
      feedbacksId.push(feedback.id);
      li.textContent = `ID: ${feedback.id}, Email: ${feedback.email}, Message: ${feedback.message}`;
      feedbackList.appendChild(li);
      feedbacks.push(feedback);
    });

    //console.log(feedbacksId);
  });

function logout() {
  localStorage.removeItem("jwt");
  window.location.href = "/PROIECT_TEHNOLOGII_WEB/FisiereHTML/login.html";
}

function deleteTable() {
  var selectedTable = document.getElementById("tables").value;

  if (!tables.includes(selectedTable)) {
    alert("Tabela introdusă nu există.");
    return;
  }
  var confirmDelete = confirm(
    "Sunteți sigur că doriți să ștergeți tabela " + selectedTable + "?"
  );

  if (confirmDelete) {
    fetch("/PROIECT_TEHNOLOGII_WEB/FisierePHP/admin_sterge_tabela.php", {
      method: "POST",
      headers: {
        "Content-Type": "application/json",
        Authorization: `Bearer ${jwtToken}`,
      },
      body: JSON.stringify({ tableName: selectedTable }),
    })
      .then((response) => response.json())
      .then((data) => {
        if (data.success) {
          alert("Tabela " + selectedTable + " a fost ștearsă cu succes.");
          window.location.reload();
        } else {
          alert("A apărut o eroare la ștergerea tabelei: " + data.error);
        }
      });
  }
}

function deleteUser() {
  var selectedUser = document.getElementById("users").value;

  if (!users.includes(selectedUser)) {
    alert("Utilizatorul introdus nu există.");
    return;
  }

  var confirmDelete = confirm(
    "Sunteți sigur că doriți să ștergeți utilizatorul " + selectedUser + "?"
  );

  if (confirmDelete) {
    fetch("/PROIECT_TEHNOLOGII_WEB/FisierePHP/admin_sterge_user.php", {
      method: "POST",
      headers: {
        "Content-Type": "application/json",
        Authorization: `Bearer ${jwtToken}`,
      },
      body: JSON.stringify({ username: selectedUser }),
    })
      .then((response) => response.json())
      .then((data) => {
        if (data.success) {
          alert("Utilizatorul " + selectedUser + " a fost șters cu succes.");
          window.location.reload();
        } else {
          alert("A apărut o eroare la ștergerea utilizatorului.");
        }
      });
  }
}

function createTable() {
  var tableName = document.getElementById("new-table-name").value;
  var csvFile = document.getElementById("csv-file").files[0];

  if (!tableName || !csvFile) {
    alert("Vă rugăm să completați toate câmpurile.");
    return;
  }

  var confirmCreate = confirm(
    "Sunteți sigur că doriți să creați tabela " + tableName + "?"
  );
  if (!confirmCreate) {
    return;
  }

  var formData = new FormData();
  formData.append("table-name", tableName);
  formData.append("csv-file", csvFile);

  fetch("/PROIECT_TEHNOLOGII_WEB/FisierePHP/admin_creaza_tabela.php", {
    method: "POST",
    headers: {
      Authorization: `Bearer ${jwtToken}`,
    },
    body: formData,
  })
    .then((response) => response.json())
    .then((data) => {
      if (data.success) {
        alert("Tabela a fost creată cu succes.");
        window.location.reload();
      } else {
        alert("A apărut o eroare la crearea tabelei: " + data.error);
      }
    });
}
function showForm() {
  var form = document.getElementById("table-form");
  if (form.style.display === "block") {
    form.style.display = "none";
  } else {
    form.style.display = "block";
  }
}

function showFeedbackuri() {
  var feedbackForm = document.getElementById("feedback-form");
  var feedbackList = document.getElementById("feedback-list");

  if (
    feedbackForm.style.display === "none" ||
    feedbackForm.style.display === ""
  ) {
    feedbackForm.style.display = "block";
    feedbacks.forEach(function (feedback) {
      if (feedback.nume !== undefined && feedback.mesaj !== undefined) {
        var listItem = document.createElement("li");
        listItem.textContent = `${feedback.nume}: ${feedback.mesaj}`;
        feedbackList.appendChild(listItem);
      }
    });
  } else {
    feedbackForm.style.display = "none";
  }
}

function deleteFeedback() {
  var feedbackId = document.getElementById("feedbacks").value;
  var feedbackExists = feedbacks.some((feedback) => feedback.id === feedbackId);

  if (!feedbackExists) {
    alert("Feedback-ul cu id-ul introdus nu exista.");
    return;
  }

  var confirmDelete = confirm(
    "Sunteți sigur că doriți să ștergeți feedback-ul selectat?"
  );

  if (confirmDelete) {
    fetch("/PROIECT_TEHNOLOGII_WEB/FisierePHP/admin_sterge_feedback.php", {
      method: "POST",
      headers: {
        "Content-Type": "application/json",
        Authorization: `Bearer ${jwtToken}`,
      },
      body: JSON.stringify({ id: feedbackId }),
    })
      .then((response) => response.json())
      .then((data) => {
        if (data.success) {
          alert("Feedback-ul a fost șters cu succes.");
          window.location.reload();
        } else {
          alert("A apărut o eroare la ștergerea feedback-ului: " + data.error);
        }
      })
      .catch((error) => {
        console.error("Error:", error);
        alert("A apărut o eroare la ștergerea feedback-ului.");
      });
  }
}
