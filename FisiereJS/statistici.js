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
function logout() {
  localStorage.removeItem("jwt");
  window.location.href = "/PROIECT_TEHNOLOGII_WEB/FisiereHTML/login.html";
}
const jwtToken = localStorage.getItem("jwt");
document.getElementById("saveButton").addEventListener("click", function () {
  console.log("Button clicked");
  var selectValue = document.getElementById("selectOptiuni").value;
  var box = document.querySelector(".box");
  var textElement = document.querySelector(".atentionareDownload");

  box.innerHTML = ""; // Ștergem conținutul din box când apăsăm din nou butonul

  var outputBox = document.getElementById("outputBox");
  outputBox.innerHTML = "";
  var exportButton = document.querySelector(".myButton2");
  var textElement = document.querySelector(".atentionareDownload");
  var mapDiv = document.getElementById("map");

  if (textElement) {
    textElement.remove();
  }

  // Dacă butonul de export există deja, ștergeți-l
  if (exportButton) {
    exportButton.remove();
  }

  // Dacă există, îl eliminăm
  if (mapDiv) {
    mapDiv.remove();
  }

  if (selectValue === "optiune1") {
    // Creăm tabel 1
    var table = document.createElement("table");

    // Titlul
    var titleRow = document.createElement("tr");
    var titleCell = document.createElement("th");
    titleCell.className = "titleCell";
    titleCell.colSpan = "10";
    titleCell.innerText = "Analiza datelor:";
    titleRow.appendChild(titleCell);
    table.appendChild(titleRow);
    table.className = "myTable";

    // Primul rând - Tabel 1
    var row1 = document.createElement("tr");
    var cell1_1 = document.createElement("td");
    cell1_1.innerText = "Județ:";
    cell1_1.className = "myCell";
    row1.appendChild(cell1_1);

    var cell1_2 = document.createElement("td");
    cell1_2.className = "myCell";
    var select_1 = document.createElement("select");
    select_1.className = "judete_select";
    var defaultOption = document.createElement("option");
    defaultOption.className = "judete";
    defaultOption.value = "";
    defaultOption.text = "Selectează Județ";
    select_1.appendChild(defaultOption);
    var judete = [
      "ALBA",
      "ARAD",
      "ARGES",
      "BACAU",
      "BIHOR",
      "BISTRITA NASAUD",
      "BOTOSANI",
      "BRAILA",
      "BRASOV",
      "BUZAU",
      "CALARASI",
      "CARAS-SEVERIN",
      "CLUJ",
      "CONSTANTA",
      "COVASNA",
      "DAMBOVITA",
      "DOLJ",
      "GALATI",
      "GIURGIU",
      "GORJ",
      "HARGHITA",
      "HUNEDOARA",
      "IALOMITA",
      "IASI",
      "ILFOV",
      "MARAMURES",
      "MEHEDINTI",
      "MUN. BUCURESTI",
      "MURES",
      "NEAMT",
      "OLT",
      "PRAHOVA",
      "SALAJ",
      "SATU MARE",
      "SIBIU",
      "SUCEAVA",
      "TELEORMAN",
      "TIMIS",
      "TULCEA",
      "VALCEA",
      "VASLUI",
      "VRANCEA",
      "ROMANIA",
    ];
    for (var i = 0; i < judete.length; i++) {
      var option = document.createElement("option");
      option.className = "judete";
      option.value = judete[i];
      option.text = judete[i];
      select_1.appendChild(option);
    }
    cell1_2.appendChild(select_1);
    row1.appendChild(cell1_2);

    var cell1_3 = document.createElement("td");
    cell1_3.className = "myCell";
    var addButtonJudet = document.createElement("button");
    addButtonJudet.innerText = "+";
    addButtonJudet.className = "myButton";
    addButtonJudet.addEventListener("click", function () {
      if (row1.getElementsByTagName("td").length <= 5) {
        var newCell = document.createElement("td");
        newCell.className = "myCell";
        var select = document.createElement("select");
        select.className = "judete_select";
        var defaultOption = document.createElement("option");
        defaultOption.className = "judete";
        defaultOption.value = "";
        defaultOption.text = "Selectează Județ";
        select.appendChild(defaultOption);
        for (var i = 0; i < judete.length; i++) {
          var option = document.createElement("option");
          option.className = "judete";
          option.value = judete[i];
          option.text = judete[i];
          select.appendChild(option);
        }
        newCell.appendChild(select);
        row1.insertBefore(newCell, cell1_3);
      } else {
        Swal.fire({
          title: "Notificare",
          html: "Nu se pot adăuga mai mult de 4 judete.",
          icon: "error",
          timer: 3000,
          timerProgressBar: true,
        });
      }
    });
    cell1_3.appendChild(addButtonJudet);
    row1.appendChild(cell1_3);
    row1.className = "myRow";
    table.appendChild(row1);

    // Al doilea rând - Tabel 1
    var row2 = document.createElement("tr");
    var cell2_1 = document.createElement("td");
    cell2_1.innerText = "Criterii:";
    cell2_1.className = "myCell";
    row2.appendChild(cell2_1);

    var cell2_2 = document.createElement("td");
    cell2_2.className = "myCell";
    var select_2 = document.createElement("select");
    select_2.className = "criterii_select";
    var defaultOption = document.createElement("option");
    defaultOption.className = "criterii";
    defaultOption.value = "";
    defaultOption.text = "Selectează Criteriu";
    select_2.appendChild(defaultOption);
    var criterii = [
      "Total someri",
      "Total someri femei",
      "Total someri barbati",
      "Rata somajului",
      "Rata somajului feminina",
      "Rata somajului masculina",
      "Someri mediul urban",
      "Someri mediul rural",
      "Fara studii",
      "Invatamant primar",
      "Invatamant gimnazial",
      "Invatamant liceal",
      "Invatamant posticeal",
      "Invatamant profesional",
      "Invatamant universitar",
      "Sub 25 ani",
      "25-29 ani",
      "30-39 ani",
      "40-49 ani",
      "50-55 ani",
      "Peste 55 ani",
    ];
    for (var i = 0; i < criterii.length; i++) {
      var option = document.createElement("option");
      option.className = "criterii";
      option.value = criterii[i];
      option.text = criterii[i];
      select_2.appendChild(option);
    }
    cell2_2.appendChild(select_2);
    row2.appendChild(cell2_2);

    var cell2_3 = document.createElement("td");
    cell2_3.className = "myCell";
    var addButton = document.createElement("button");
    addButton.innerText = "+";
    addButton.className = "myButton";
    addButton.addEventListener("click", function () {
      if (row2.getElementsByTagName("td").length <= 5) {
        var newCell = document.createElement("td");
        newCell.className = "myCell";
        var select = document.createElement("select");
        select.className = "criterii_select";
        var defaultOption = document.createElement("option");
        defaultOption.className = "criterii";
        defaultOption.value = "";
        defaultOption.text = "Selectează Criteriu";
        select.appendChild(defaultOption);
        for (var i = 0; i < criterii.length; i++) {
          var option = document.createElement("option");
          option.className = "criterii";
          option.value = criterii[i];
          option.text = criterii[i];
          select.appendChild(option);
        }
        newCell.appendChild(select);
        row2.insertBefore(newCell, cell2_3);
      } else {
        Swal.fire({
          title: "Notificare",
          html: "Nu se pot adăuga mai mult de 4 criterii.",
          icon: "error",
          timer: 3000,
          timerProgressBar: true,
        });
      }
    });
    cell2_3.appendChild(addButton);
    row2.appendChild(cell2_3);
    row2.className = "myRow";
    table.appendChild(row2);

    // Al treilea rând - Tabel 1
    var row3 = document.createElement("tr");
    var cell3_1 = document.createElement("td");
    cell3_1.className = "row1Cell";
    cell3_1.innerText = "Luna:";
    row3.appendChild(cell3_1);

    var cell3_2 = document.createElement("td");
    var select3 = document.createElement("select");
    select3.className = "luna_select";
    var defaultOption = document.createElement("option");
    defaultOption.className = "luna";
    defaultOption.value = "";
    defaultOption.text = "Selectează Lună";
    select3.appendChild(defaultOption);
    var luni = [
      "aprilie_2023",
      "mai_2023",
      "iunie_2023",
      "iulie_2023",
      "august_2023",
      "septembrie_2023",
      "octombrie_2023",
      "noiembrie_2023",
      "decembrie_2023",
      "ianuarie_2024",
      "februarie_2024",
      "martie_2024",
      "aprilie_2024",
    ];
    for (var i = 0; i < luni.length; i++) {
      var option = document.createElement("option");
      option.className = "luna";
      option.value = luni[i];
      option.text = luni[i];
      select3.appendChild(option);
    }
    cell3_2.appendChild(select3);
    row3.appendChild(cell3_2);

    var cellButtonLuni = document.createElement("td");
    cellButtonLuni.className = "myCell";
    var addButtonLuni = document.createElement("button");
    addButtonLuni.innerText = "+";
    addButtonLuni.className = "myButton";
    addButtonLuni.addEventListener("click", function () {
      if (row3.getElementsByTagName("td").length <= 5) {
        var newCell2 = document.createElement("td");
        newCell2.className = "myCell";
        var select2 = document.createElement("select");
        select2.className = "luna_select";
        var defaultOption = document.createElement("option");
        defaultOption.className = "luna";
        defaultOption.value = "";
        defaultOption.text = "Selectează Lună";
        select2.appendChild(defaultOption);
        for (var i = 0; i < luni.length; i++) {
          var option = document.createElement("option");
          option.className = "luna";
          option.value = luni[i];
          option.text = luni[i];
          select2.appendChild(option);
        }
        newCell2.appendChild(select2);
        row3.insertBefore(newCell2, cellButtonLuni);
      } else {
        Swal.fire({
          title: "Notificare",
          html: "Nu se pot adăuga mai mult de 4 luni.",
          icon: "error",
          timer: 3000,
          timerProgressBar: true,
        });
      }
    });
    cellButtonLuni.appendChild(addButtonLuni);
    row3.appendChild(cellButtonLuni);
    table.appendChild(row3);

    // Al patrulea rând - Tabel 1
    var row4 = document.createElement("tr");
    var cell4_1 = document.createElement("td");

    // Submit button
    var submitButton = document.createElement("input");
    submitButton.className = "myButton";
    submitButton.type = "submit";
    submitButton.value = "Submit";
    cell4_1.appendChild(submitButton);
    row4.appendChild(cell4_1);
    table.appendChild(row4);

    // endRow - Tabel 1
    var rowEnd = document.createElement("tr");
    // Celule goale pentru formatare
    for (var i = 0; i < 7; i++) {
      var cell = document.createElement("td");
      cell.className = "row1Cell";
      rowEnd.appendChild(cell);
    }
    table.appendChild(rowEnd);

    box.appendChild(table);

    submitButton.onclick = function () {
      // Verificările pentru elementele 'judete_select'
      var judetSelects = document.querySelectorAll(".judete_select");
      var selectedJudete = [];
      var unselectedJudete = false;
      var sameJudet = false;
      var judeteTracker = {};
      judetSelects.forEach(function (select) {
        if (judeteTracker[select.value]) {
          sameJudet = true;
        }
        judeteTracker[select.value] = true;
        selectedJudete.push(select.value);
        if (select.value === "") {
          unselectedJudete = true;
        }
      });
      if (unselectedJudete) {
        Swal.fire({
          title: "Notificare",
          html: "Vă rugăm să selectați un județ.",
          icon: "warning",
          timer: 3000,
          timerProgressBar: true,
        });
        return;
      }
      if (sameJudet) {
        Swal.fire({
          title: "Notificare",
          html: "Nu puteți selecta același județ de mai multe ori.",
          icon: "error",
          timer: 3000,
          timerProgressBar: true,
        });
        return;
      }

      // Verificările pentru elementele 'criterii_select'
      var criteriiSelects = document.querySelectorAll(".criterii_select");
      var selectedCriterii = [];
      var unselectedCriterii = false;
      var sameCriteriu = false;
      var criteriiTracker = {};
      criteriiSelects.forEach(function (select) {
        if (criteriiTracker[select.value]) {
          sameCriteriu = true;
        }
        criteriiTracker[select.value] = true;
        selectedCriterii.push(select.value);
        if (select.value === "") {
          unselectedCriterii = true;
        }
      });
      if (unselectedCriterii) {
        Swal.fire({
          title: "Notificare",
          html: "Vă rugăm să selectați un criteriu.",
          icon: "warning",
          timer: 3000,
          timerProgressBar: true,
        });
        return;
      }
      if (sameCriteriu) {
        Swal.fire({
          title: "Notificare",
          html: "Nu puteți selecta același criteriu de mai multe ori.",
          icon: "error",
          timer: 3000,
          timerProgressBar: true,
        });
        return;
      }

      // Verificările pentru elementele 'luna_select'
      var lunaSelects = document.querySelectorAll(".luna_select");
      var selectedLuni = [];
      var unselectedLuni = false;
      var sameLuna = false;
      var lunaTracker = {};
      lunaSelects.forEach(function (select) {
        if (lunaTracker[select.value]) {
          sameLuna = true;
        }
        lunaTracker[select.value] = true;
        selectedLuni.push(select.value);
        if (select.value === "") {
          unselectedLuni = true;
        }
      });
      if (unselectedLuni) {
        Swal.fire({
          title: "Notificare",
          html: "Vă rugăm să selectați o lună.",
          icon: "warning",
          timer: 3000,
          timerProgressBar: true,
        });
        return;
      }
      if (sameLuna) {
        Swal.fire({
          title: "Notificare",
          html: "Nu puteți selecta aceeași lună de mai multe ori.",
          icon: "error",
          timer: 3000,
          timerProgressBar: true,
        });
        return;
      }

      Swal.fire({
        title: "Notificare",
        html: "Se generează statisticile! Vă rugăm să așteptați...",
        icon: "success",
        timer: 3000,
        timerProgressBar: true,
      });
      var outputBox = document.getElementById("outputBox");
      outputBox.innerHTML = "";
      var allTables = "";

      for (var i = 0; i < selectedLuni.length; i++) {
        var data = {
          judete: selectedJudete,
          luni: selectedLuni,
          criterii: selectedCriterii,
          index_luna: i,
        };

        var jsonData = JSON.stringify(data);

        fetch("/PROIECT_TEHNOLOGII_WEB/FisierePHP/statistici_csv.php", {
          method: "POST",
          headers: {
            "Content-Type": "application/json",
            Authorization: `Bearer ${jwtToken}`,
          },
          body: jsonData,
        })
          .then((response) => {
            if (!response.ok) {
              throw new Error("Network response was not ok");
            }
            return response.text();
          })
          .then((data) => {
            var outputBox = document.getElementById("outputBox");
            outputBox.innerHTML += data;
            data += "<tr><td></td></tr>"; // Adaugăm un rând gol la sfârșitul datelor pentru formatarea în excel
            allTables += data;
          })
          .catch((error) => {
            console.error("Error:", error);
          });
      }

      // Verificăm dacă butonul de export există deja
      var exportButton = document.querySelector(".myButton2");
      if (exportButton) {
        exportButton.remove();
      }
      exportButton = document.createElement("button");
      exportButton.textContent = "Export CSV";
      exportButton.className = "myButton2";
      exportButton.addEventListener("click", function () {
        fetch("/PROIECT_TEHNOLOGII_WEB/FisierePHP/export_csv.php", {
          method: "POST",
          headers: {
            "Content-Type": "application/json",
            Authorization: `Bearer ${jwtToken}`,
          },
          body: JSON.stringify({ tables: allTables }),
        })
          .then((response) => {
            if (!response.ok) {
              throw new Error("Network response was not ok");
            }
            return response.blob();
          })
          .then((blob) => {
            var url = window.URL.createObjectURL(blob);
            var a = document.createElement("a");
            a.href = url;
            a.download = "export_statistics.csv";
            document.body.appendChild(a);
            a.click();
            a.remove();
          })
          .catch((error) => {
            console.error("Error:", error);
          });
      });
      document.body.appendChild(exportButton);
    };
  } else if (selectValue === "optiune2") {
    // Creăm tabel 2
    var table = document.createElement("table");

    // Titlul
    var titleRow = document.createElement("tr");
    var titleCell = document.createElement("th");
    titleCell.className = "titleCell";
    titleCell.colSpan = "10";
    titleCell.innerText = "Analiza datelor:";
    titleRow.appendChild(titleCell);
    table.appendChild(titleRow);
    table.className = "myTable";

    // Primul rând - Tabel 2
    var row1 = document.createElement("tr");
    var cell1_1 = document.createElement("td");
    cell1_1.innerText = "Județ:";
    cell1_1.className = "myCell";
    row1.appendChild(cell1_1);

    var cell1_2 = document.createElement("td");
    cell1_2.className = "myCell";
    var select_1 = document.createElement("select");
    select_1.className = "judete_select";
    var defaultOption = document.createElement("option");
    defaultOption.className = "judete";
    defaultOption.value = "";
    defaultOption.text = "Selectează Județ";
    select_1.appendChild(defaultOption);
    var judete = [
      "ALBA",
      "ARAD",
      "ARGES",
      "BACAU",
      "BIHOR",
      "BISTRITA NASAUD",
      "BOTOSANI",
      "BRAILA",
      "BRASOV",
      "BUZAU",
      "CALARASI",
      "CARAS-SEVERIN",
      "CLUJ",
      "CONSTANTA",
      "COVASNA",
      "DAMBOVITA",
      "DOLJ",
      "GALATI",
      "GIURGIU",
      "GORJ",
      "HARGHITA",
      "HUNEDOARA",
      "IALOMITA",
      "IASI",
      "ILFOV",
      "MARAMURES",
      "MEHEDINTI",
      "MUN. BUCURESTI",
      "MURES",
      "NEAMT",
      "OLT",
      "PRAHOVA",
      "SALAJ",
      "SATU MARE",
      "SIBIU",
      "SUCEAVA",
      "TELEORMAN",
      "TIMIS",
      "TULCEA",
      "VALCEA",
      "VASLUI",
      "VRANCEA",
      "ROMANIA",
    ];
    for (var i = 0; i < judete.length; i++) {
      var option = document.createElement("option");
      option.className = "judete";
      option.value = judete[i];
      option.text = judete[i];
      select_1.appendChild(option);
    }
    cell1_2.appendChild(select_1);
    row1.appendChild(cell1_2);

    var cell1_3 = document.createElement("td");
    cell1_3.className = "myCell";
    var addButtonJudet = document.createElement("button");
    addButtonJudet.innerText = "+";
    addButtonJudet.className = "myButton";
    addButtonJudet.addEventListener("click", function () {
      if (row1.getElementsByTagName("td").length <= 5) {
        var newCell = document.createElement("td");
        newCell.className = "myCell";
        var select = document.createElement("select");
        select.className = "judete_select";
        var defaultOption = document.createElement("option");
        defaultOption.className = "judete";
        defaultOption.value = "";
        defaultOption.text = "Selectează Județ";
        select.appendChild(defaultOption);
        for (var i = 0; i < judete.length; i++) {
          var option = document.createElement("option");
          option.className = "judete";
          option.value = judete[i];
          option.text = judete[i];
          select.appendChild(option);
        }
        newCell.appendChild(select);
        row1.insertBefore(newCell, cell1_3);
      } else {
        Swal.fire({
          title: "Notificare",
          html: "Nu se pot adăuga mai mult de 4 judete.",
          icon: "error",
          timer: 3000,
          timerProgressBar: true,
        });
      }
    });
    cell1_3.appendChild(addButtonJudet);
    row1.appendChild(cell1_3);
    row1.className = "myRow";
    table.appendChild(row1);

    // Al doilea rând - Tabel 2
    var row2 = document.createElement("tr");
    var cell2_1 = document.createElement("td");
    cell2_1.innerText = "Criterii:";
    cell2_1.className = "myCell";
    row2.appendChild(cell2_1);

    var cell2_2 = document.createElement("td");
    cell2_2.className = "myCell";
    var select_2 = document.createElement("select");
    select_2.className = "criterii_select";
    var defaultOption = document.createElement("option");
    defaultOption.className = "criterii";
    defaultOption.value = "";
    defaultOption.text = "Selectează Criteriu";
    select_2.appendChild(defaultOption);
    var criterii = [
      "Total someri",
      "Total someri femei",
      "Total someri barbati",
      "Rata somajului",
      "Rata somajului feminina",
      "Rata somajului masculina",
      "Someri mediul urban",
      "Someri mediul rural",
      "Fara studii",
      "Invatamant primar",
      "Invatamant gimnazial",
      "Invatamant liceal",
      "Invatamant posticeal",
      "Invatamant profesional",
      "Invatamant universitar",
      "Sub 25 ani",
      "25-29 ani",
      "30-39 ani",
      "40-49 ani",
      "50-55 ani",
      "Peste 55 ani",
    ];
    for (var i = 0; i < criterii.length; i++) {
      var option = document.createElement("option");
      option.className = "criterii";
      option.value = criterii[i];
      option.text = criterii[i];
      select_2.appendChild(option);
    }
    cell2_2.appendChild(select_2);
    row2.appendChild(cell2_2);

    var cell2_3 = document.createElement("td");
    cell2_3.className = "myCell";
    var addButtonCriteriu = document.createElement("button");
    addButtonCriteriu.innerText = "+";
    addButtonCriteriu.className = "myButton";
    addButtonCriteriu.addEventListener("click", function () {
      if (row2.getElementsByTagName("td").length <= 4) {
        var newCell = document.createElement("td");
        newCell.className = "myCell";
        var select = document.createElement("select");
        select.className = "criterii_select";
        var defaultOption = document.createElement("option");
        defaultOption.className = "criterii";
        defaultOption.value = "";
        defaultOption.text = "Selectează Criteriu";
        select.appendChild(defaultOption);
        for (var i = 0; i < criterii.length; i++) {
          var option = document.createElement("option");
          option.className = "criterii";
          option.value = criterii[i];
          option.text = criterii[i];
          select.appendChild(option);
        }
        newCell.appendChild(select);
        row2.insertBefore(newCell, cell2_3);
      } else {
        Swal.fire({
          title: "Notificare",
          html: "Nu se pot adăuga mai mult de 3 criterii.",
          icon: "error",
          timer: 3000,
          timerProgressBar: true,
        });
      }
    });
    cell2_3.appendChild(addButtonCriteriu);
    row2.appendChild(cell2_3);
    row2.className = "myRow";
    table.appendChild(row2);

    // Al treilea rând - Tabel 2
    var row3 = document.createElement("tr");
    var cell3_1 = document.createElement("td");
    cell3_1.className = "row1Cell";
    cell3_1.innerText = "Luna:";
    row3.appendChild(cell3_1);
    var cell3_2 = document.createElement("td");
    var select3 = document.createElement("select");
    select3.className = "luna_select";
    var defaultOption = document.createElement("option");
    defaultOption.className = "luna";
    defaultOption.value = "";
    defaultOption.text = "Selectează Lună";
    select3.appendChild(defaultOption);
    var luni = [
      "aprilie_2023",
      "mai_2023",
      "iunie_2023",
      "iulie_2023",
      "august_2023",
      "septembrie_2023",
      "octombrie_2023",
      "noiembrie_2023",
      "decembrie_2023",
      "ianuarie_2024",
      "februarie_2024",
      "martie_2024",
      "aprilie_2024",
    ];
    for (var i = 0; i < luni.length; i++) {
      var option = document.createElement("option");
      option.className = "luna";
      option.value = luni[i];
      option.text = luni[i];
      select3.appendChild(option);
    }
    cell3_2.appendChild(select3);
    row3.appendChild(cell3_2);

    var cell3_4 = document.createElement("td");
    cell3_4.className = "myCell";
    var addButtonLuni = document.createElement("button");
    addButtonLuni.innerText = "+";
    addButtonLuni.className = "myButton";
    addButtonLuni.addEventListener("click", function () {
      if (row3.getElementsByTagName("td").length <= 5) {
        var newCell = document.createElement("td");
        newCell.className = "myCell";
        var select = document.createElement("select");
        select.className = "luna_select";
        var defaultOption = document.createElement("option");
        defaultOption.className = "luna";
        defaultOption.value = "";
        defaultOption.text = "Selectează Lună";
        select.appendChild(defaultOption);
        for (var i = 0; i < luni.length; i++) {
          var option = document.createElement("option");
          option.className = "luna";
          option.value = luni[i];
          option.text = luni[i];
          select.appendChild(option);
        }
        newCell.appendChild(select);
        row3.insertBefore(newCell, cell3_4);
      } else {
        Swal.fire({
          title: "Notificare",
          html: "Nu se pot adăuga mai mult de 4 luni.",
          icon: "error",
          timer: 3000,
          timerProgressBar: true,
        });
      }
    });
    cell3_4.appendChild(addButtonLuni);
    row3.appendChild(cell3_4);
    table.appendChild(row3);

    // Al patrulea rând - Tabel 2
    var row4 = document.createElement("tr");
    var cell4_1 = document.createElement("td");

    // Submit button
    var submitButton = document.createElement("input");
    submitButton.className = "myButton";
    submitButton.type = "submit";
    submitButton.value = "Submit";
    cell4_1.appendChild(submitButton);
    row4.appendChild(cell4_1);
    table.appendChild(row4);

    // endRow - Tabel 2
    var rowEnd = document.createElement("tr");
    // Celule goale pentru formatare
    for (var i = 0; i < 7; i++) {
      var cell = document.createElement("td");
      cell.className = "row1Cell";
      rowEnd.appendChild(cell);
    }
    table.appendChild(rowEnd);
    box.appendChild(table);

    submitButton.onclick = function () {
      // Verificările pentru elementele 'judete_select'
      var judetSelects = document.querySelectorAll(".judete_select");
      var selectedJudete = [];
      var unselectedJudete = false;
      var sameJudet = false;
      var judeteTracker = {};
      judetSelects.forEach(function (select) {
        if (judeteTracker[select.value]) {
          sameJudet = true;
        }
        judeteTracker[select.value] = true;
        selectedJudete.push(select.value);
        if (select.value === "") {
          unselectedJudete = true;
        }
      });
      if (unselectedJudete) {
        Swal.fire({
          title: "Notificare",
          html: "Vă rugăm să selectați un județ.",
          icon: "warning",
          timer: 3000,
          timerProgressBar: true,
        });
        return;
      }
      if (sameJudet) {
        Swal.fire({
          title: "Notificare",
          html: "Nu puteți selecta același județ de mai multe ori.",
          icon: "error",
          timer: 3000,
          timerProgressBar: true,
        });
        return;
      }

      // Verificările pentru elementele 'criterii_select'
      var criteriiSelects = document.querySelectorAll(".criterii_select");
      var selectedCriterii = [];
      var unselectedCriterii = false;
      var sameCriteriu = false;
      var criteriiTracker = {};
      criteriiSelects.forEach(function (select) {
        if (criteriiTracker[select.value]) {
          sameCriteriu = true;
        }
        criteriiTracker[select.value] = true;
        selectedCriterii.push(select.value);
        if (select.value === "") {
          unselectedCriterii = true;
        }
      });
      if (unselectedCriterii) {
        Swal.fire({
          title: "Notificare",
          html: "Vă rugăm să selectați un criteriu.",
          icon: "warning",
          timer: 3000,
          timerProgressBar: true,
        });
        return;
      }
      if (sameCriteriu) {
        Swal.fire({
          title: "Notificare",
          html: "Nu puteți selecta același criteriu de mai multe ori.",
          icon: "error",
          timer: 3000,
          timerProgressBar: true,
        });
        return;
      }

      // Verificările pentru elementele 'luna_select'
      var lunaSelects = document.querySelectorAll(".luna_select");
      var selectedLuni = [];
      var unselectedLuni = false;
      var sameLuna = false;
      var lunaTracker = {};
      lunaSelects.forEach(function (select) {
        if (lunaTracker[select.value]) {
          sameLuna = true;
        }
        lunaTracker[select.value] = true;
        selectedLuni.push(select.value);
        if (select.value === "") {
          unselectedLuni = true;
        }
      });
      if (unselectedLuni) {
        Swal.fire({
          title: "Notificare",
          html: "Vă rugăm să selectați o lună.",
          icon: "warning",
          timer: 3000,
          timerProgressBar: true,
        });
        return;
      }
      if (sameLuna) {
        Swal.fire({
          title: "Notificare",
          html: "Nu puteți selecta aceeași lună de mai multe ori.",
          icon: "error",
          timer: 3000,
          timerProgressBar: true,
        });
        return;
      }

      Swal.fire({
        title: "Notificare",
        html: "Se generează statisticile! Vă rugăm să așteptați...",
        icon: "success",
        timer: 3000,
        timerProgressBar: true,
      });
      var outputBox = document.getElementById("outputBox");
      outputBox.innerHTML = "";
      const blobArray = [];

      for (var i = 0; i < selectedLuni.length; i++) {
        var data = {
          judete: selectedJudete,
          luni: selectedLuni,
          criterii: selectedCriterii,
          index_luna: i,
        };
        var jsonData = JSON.stringify(data);
        fetch("/PROIECT_TEHNOLOGII_WEB/FisierePHP/statistici_pdf.php", {
          method: "POST",
          headers: {
            "Content-Type": "application/json",
            Authorization: `Bearer ${jwtToken}`,
          },
          body: jsonData,
        })
          .then((response) => {
            if (!response.ok) {
              throw new Error("Network response was not ok");
            }
            return response.blob();
          })
          .then((data) => {
            var outputBox = document.getElementById("outputBox");
            var embed = document.createElement("embed");
            embed.className = "pdfEmbed";
            embed.src = URL.createObjectURL(data);
            embed.type = "application/pdf";
            outputBox.appendChild(embed);
            blobArray.push(data);
          })
          .catch((error) => {
            console.error("Error:", error);
          });
      }

      var exportButton = document.querySelector(".myButton2");
      var textElement = document.querySelector(".atentionareDownload");
      if (exportButton) {
        exportButton.remove();
      }
      if (textElement) {
        textElement.remove();
      }

      exportButton = document.createElement("button");
      exportButton.textContent = "Export PDF";
      exportButton.className = "myButton2";

      exportButton.addEventListener("click", function () {
        blobArray.forEach((blob, index) => {
          const forExport = document.createElement("a");
          const url = window.URL.createObjectURL(blob);
          forExport.href = url;
          forExport.download = `export_statistics_${index}.pdf`;
          document.body.appendChild(forExport);
          forExport.click();
          document.body.removeChild(forExport);
          URL.revokeObjectURL(url);
        });
      });
      var textElement = document.createElement("span");
      textElement.className = "atentionareDownload";
      textElement.textContent =
        "*Atenție! Prin această opțiune se vor descărca mai multe fișiere.";
      exportButton.after(textElement);
      document.body.appendChild(exportButton);
      document.body.appendChild(textElement);
    };
  } else if (selectValue === "optiune3") {
    // Creăm tabel 3
    var table = document.createElement("table");

    // Titlul
    var titleRow = document.createElement("tr");
    var titleCell = document.createElement("th");
    titleCell.className = "titleCell";
    titleCell.colSpan = "10";
    titleCell.innerText = "Analiza datelor:";
    titleRow.appendChild(titleCell);
    table.appendChild(titleRow);
    table.className = "myTable";

    // Primul rând - Tabel 3
    var row1 = document.createElement("tr");
    var cell1_1 = document.createElement("td");
    cell1_1.innerText = "Județ:";
    cell1_1.className = "myCell";
    row1.appendChild(cell1_1);

    var cell1_2 = document.createElement("td");
    cell1_2.className = "myCell";
    var select_1 = document.createElement("select");
    select_1.className = "judete_select";
    var defaultOption = document.createElement("option");
    defaultOption.className = "judete";
    defaultOption.value = "";
    defaultOption.text = "Selectează Județ";
    select_1.appendChild(defaultOption);
    var judete = [
      "Toate judetele",
      "ALBA",
      "ARAD",
      "ARGES",
      "BACAU",
      "BIHOR",
      "BISTRITA NASAUD",
      "BOTOSANI",
      "BRAILA",
      "BRASOV",
      "BUZAU",
      "CALARASI",
      "CARAS-SEVERIN",
      "CLUJ",
      "CONSTANTA",
      "COVASNA",
      "DAMBOVITA",
      "DOLJ",
      "GALATI",
      "GIURGIU",
      "GORJ",
      "HARGHITA",
      "HUNEDOARA",
      "IALOMITA",
      "IASI",
      "ILFOV",
      "MARAMURES",
      "MEHEDINTI",
      "MUN. BUCURESTI",
      "MURES",
      "NEAMT",
      "OLT",
      "PRAHOVA",
      "SALAJ",
      "SATU MARE",
      "SIBIU",
      "SUCEAVA",
      "TELEORMAN",
      "TIMIS",
      "TULCEA",
      "VALCEA",
      "VASLUI",
      "VRANCEA",
    ];
    for (var i = 0; i < judete.length; i++) {
      var option = document.createElement("option");
      option.className = "judete";
      option.value = judete[i];
      option.text = judete[i];
      select_1.appendChild(option);
    }
    cell1_2.appendChild(select_1);
    row1.appendChild(cell1_2);

    var cell1_3 = document.createElement("td");
    cell1_3.className = "myCell";
    var addButtonJudet = document.createElement("button");
    addButtonJudet.innerText = "+";
    addButtonJudet.className = "myButton";
    addButtonJudet.addEventListener("click", function () {
      if (row1.getElementsByTagName("td").length <= 5) {
        var newCell = document.createElement("td");
        newCell.className = "myCell";
        var select = document.createElement("select");
        select.className = "judete_select";
        var defaultOption = document.createElement("option");
        defaultOption.className = "judete";
        defaultOption.value = "";
        defaultOption.text = "Selectează Județ";
        select.appendChild(defaultOption);
        for (var i = 0; i < judete.length; i++) {
          var option = document.createElement("option");
          option.className = "judete";
          option.value = judete[i];
          option.text = judete[i];
          select.appendChild(option);
        }
        newCell.appendChild(select);
        row1.insertBefore(newCell, cell1_3);
      } else {
        Swal.fire({
          title: "Notificare",
          html: "Nu se pot adăuga mai mult de 4 judete.",
          icon: "error",
          timer: 3000,
          timerProgressBar: true,
        });
      }
    });
    cell1_3.appendChild(addButtonJudet);
    row1.appendChild(cell1_3);
    row1.className = "myRow";
    table.appendChild(row1);

    // Al doilea rând - Tabel 3
    var row2 = document.createElement("tr");
    var cell2_1 = document.createElement("td");
    cell2_1.innerText = "Criterii:";
    cell2_1.className = "myCell";
    row2.appendChild(cell2_1);

    var cell2_2 = document.createElement("td");
    cell2_2.className = "myCell";
    var select_2 = document.createElement("select");
    select_2.className = "criterii_select";
    var defaultOption = document.createElement("option");
    defaultOption.className = "criterii";
    defaultOption.value = "";
    defaultOption.text = "Selectează Criteriu";
    select_2.appendChild(defaultOption);
    var criterii = [
      "Total someri",
      "Total someri femei",
      "Total someri barbati",
      "Rata somajului",
      "Rata somajului feminina",
      "Rata somajului masculina",
      "Someri mediul urban",
      "Someri mediul rural",
      "Fara studii",
      "Invatamant primar",
      "Invatamant gimnazial",
      "Invatamant liceal",
      "Invatamant posticeal",
      "Invatamant profesional",
      "Invatamant universitar",
      "Sub 25 ani",
      "25-29 ani",
      "30-39 ani",
      "40-49 ani",
      "50-55 ani",
      "Peste 55 ani",
    ];
    for (var i = 0; i < criterii.length; i++) {
      var option = document.createElement("option");
      option.className = "criterii";
      option.value = criterii[i];
      option.text = criterii[i];
      select_2.appendChild(option);
    }
    cell2_2.appendChild(select_2);
    row2.appendChild(cell2_2);
    row2.className = "myRow";
    table.appendChild(row2);

    // Al treilea rând - Tabel 3
    var row3 = document.createElement("tr");
    var cell3_1 = document.createElement("td");
    cell3_1.className = "row1Cell";
    cell3_1.innerText = "Luna:";
    row3.appendChild(cell3_1);

    var cell3_2 = document.createElement("td");
    var select3 = document.createElement("select");
    select3.className = "luna_select";
    var defaultOption = document.createElement("option");
    defaultOption.className = "luna";
    defaultOption.value = "";
    defaultOption.text = "Selectează Lună";
    select3.appendChild(defaultOption);
    var luni = [
      "aprilie_2023",
      "mai_2023",
      "iunie_2023",
      "iulie_2023",
      "august_2023",
      "septembrie_2023",
      "octombrie_2023",
      "noiembrie_2023",
      "decembrie_2023",
      "ianuarie_2024",
      "februarie_2024",
      "martie_2024",
      "aprilie_2024",
    ];
    for (var i = 0; i < luni.length; i++) {
      var option = document.createElement("option");
      option.className = "luna";
      option.value = luni[i];
      option.text = luni[i];
      select3.appendChild(option);
    }
    cell3_2.appendChild(select3);
    row3.appendChild(cell3_2);
    table.appendChild(row3);

    // Al patrulea rând - Tabel 3
    var row4 = document.createElement("tr");
    var cell4_1 = document.createElement("td");

    // Submit button
    var submitButton = document.createElement("input");
    submitButton.className = "myButton";
    submitButton.type = "submit";
    submitButton.value = "Submit";
    cell4_1.appendChild(submitButton);
    row4.appendChild(cell4_1);
    table.appendChild(row4);

    // endRow - Tabel 3
    var rowEnd = document.createElement("tr");
    // Celule goale pentru formatare
    for (var i = 0; i < 7; i++) {
      var cell = document.createElement("td");
      cell.className = "row1Cell";
      rowEnd.appendChild(cell);
    }
    table.appendChild(rowEnd);
    box.appendChild(table);

    submitButton.onclick = function () {
      // Verificăm elementele 'judete_select'
      var judetSelects = document.querySelectorAll(".judete_select");
      var selectedJudete = [];
      var unselectedJudete = false;
      var sameJudet = false;
      var judeteTracker = {};

      for (let select of judetSelects) {
        if (judeteTracker[select.value]) {
          sameJudet = true;
        }
        judeteTracker[select.value] = true;
        if (select.value === "") {
          unselectedJudete = true;
        }
        if (select.value === "Toate judetele") {
          selectedJudete = [
            "ALBA",
            "ARAD",
            "ARGES",
            "BACAU",
            "BIHOR",
            "BISTRITA NASAUD",
            "BOTOSANI",
            "BRAILA",
            "BRASOV",
            "BUZAU",
            "CALARASI",
            "CARAS-SEVERIN",
            "CLUJ",
            "CONSTANTA",
            "COVASNA",
            "DAMBOVITA",
            "DOLJ",
            "GALATI",
            "GIURGIU",
            "GORJ",
            "HARGHITA",
            "HUNEDOARA",
            "IALOMITA",
            "IASI",
            "ILFOV",
            "MARAMURES",
            "MEHEDINTI",
            "MUN. BUCURESTI",
            "MURES",
            "NEAMT",
            "OLT",
            "PRAHOVA",
            "SALAJ",
            "SATU MARE",
            "SIBIU",
            "SUCEAVA",
            "TELEORMAN",
            "TIMIS",
            "TULCEA",
            "VALCEA",
            "VASLUI",
            "VRANCEA",
          ];
          break;
        } else {
          selectedJudete.push(select.value);
        }
      }

      if (unselectedJudete) {
        Swal.fire({
          title: "Notificare",
          html: "Vă rugăm să selectați un județ.",
          icon: "warning",
          timer: 3000,
          timerProgressBar: true,
        });
        return;
      }
      if (sameJudet) {
        Swal.fire({
          title: "Notificare",
          html: "Nu puteți selecta același județ de mai multe ori.",
          icon: "error",
          timer: 3000,
          timerProgressBar: true,
        });
        return;
      }

      // Verificăm elementele 'criterii_select'
      var criteriiSelects = document.querySelectorAll(".criterii_select");
      var selectedCriterii = [];
      var unselectedCriterii = false;
      var sameCriteriu = false;
      var criteriiTracker = {};
      criteriiSelects.forEach(function (select) {
        if (criteriiTracker[select.value]) {
          sameCriteriu = true;
        }
        criteriiTracker[select.value] = true;
        selectedCriterii.push(select.value);
        if (select.value === "") {
          unselectedCriterii = true;
        }
      });
      if (unselectedCriterii) {
        Swal.fire({
          title: "Notificare",
          html: "Vă rugăm să selectați un criteriu.",
          icon: "warning",
          timer: 3000,
          timerProgressBar: true,
        });
        return;
      }
      if (sameCriteriu) {
        Swal.fire({
          title: "Notificare",
          html: "Nu puteți selecta același criteriu de mai multe ori.",
          icon: "error",
          timer: 3000,
          timerProgressBar: true,
        });
        return;
      }

      // Verificăm elementele 'luna_select'
      var lunaSelects = document.querySelectorAll(".luna_select");
      var selectedLuni = [];
      var unselectedLuni = false;
      var sameLuna = false;
      var lunaTracker = {};
      lunaSelects.forEach(function (select) {
        if (lunaTracker[select.value]) {
          sameLuna = true;
        }
        lunaTracker[select.value] = true;
        selectedLuni.push(select.value);
        if (select.value === "") {
          unselectedLuni = true;
        }
      });
      if (unselectedLuni) {
        Swal.fire({
          title: "Notificare",
          html: "Vă rugăm să selectați o lună.",
          icon: "warning",
          timer: 3000,
          timerProgressBar: true,
        });
        return;
      }
      if (sameLuna) {
        Swal.fire({
          title: "Notificare",
          html: "Nu puteți selecta aceeași lună de mai multe ori.",
          icon: "error",
          timer: 3000,
          timerProgressBar: true,
        });
        return;
      }
      Swal.fire({
        title: "Notificare",
        html: "Se generează statisticile! Vă rugăm să așteptați...",
        icon: "success",
        timer: 3000,
        timerProgressBar: true,
      });
      var outputBox = document.getElementById("outputBox");
      outputBox.innerHTML = "";
      var dataSVG = "";

      var data = {
        judete: selectedJudete,
        luni: selectedLuni,
        criterii: selectedCriterii,
        index_luna: 0,
      };
      var jsonData = JSON.stringify(data);

      fetch("/PROIECT_TEHNOLOGII_WEB/FisierePHP/statistici_svg.php", {
        method: "POST",
        headers: {
          "Content-Type": "application/json",
          Authorization: `Bearer ${jwtToken}`,
        },
        body: jsonData,
      })
        .then((response) => {
          if (!response.ok) {
            throw new Error("Network response was not ok");
          }
          return response.text();
        })
        .then((html) => {
          outputBox.innerHTML += html;
          const parser = new DOMParser();
          const doc = parser.parseFromString(html, "text/html");
          dataSVG = doc.querySelector("svg").outerHTML;
        })
        .catch((error) => {
          console.error("Error:", error);
        });

      var exportButton = document.querySelector(".myButton2");

      if (exportButton) {
        exportButton.remove();
      }

      var mapDiv = document.getElementById("map");

      // Dacă există, îl eliminăm
      if (mapDiv) {
        mapDiv.remove();
      }

      exportButton = document.createElement("button");
      exportButton.textContent = "Export SVG";
      exportButton.className = "myButton2";

      exportButton.addEventListener("click", function () {
        var svg = new Blob([dataSVG], { type: "image/svg+xml" });
        var url = URL.createObjectURL(svg);
        var a = document.createElement("a");
        a.href = url;
        a.download = "export_statistics.svg";
        document.body.appendChild(a);
        a.click();
        a.remove();
      });
      var mapDiv = document.createElement("div");
      mapDiv.id = "map";
      document.body.appendChild(mapDiv);
      var map = L.map("map", { preferCanvas: true }).setView(
        [45.9432, 24.9668],
        7
      );
      L.tileLayer("https://tile.openstreetmap.org/{z}/{x}/{y}.png", {
        maxZoom: 19,
        attribution:
          '&copy; <a href="http://www.openstreetmap.org/copyright">OpenStreetMap</a>',
      }).addTo(map);

      fetch("/PROIECT_TEHNOLOGII_WEB/FisierePHP/cartografie.php", {
        method: "POST",
        headers: {
          "Content-Type": "application/json",
          Authorization: `Bearer ${jwtToken}`,
        },
        body: jsonData,
      })
        .then((response) => {
          if (!response.ok) {
            throw new Error("Network response was not ok");
          }
          return response.json();
        })
        .then((data) => {
          console.log("Date primite:", data);
          data.forEach((judet) => {
            const circle = L.circle([judet.lat, judet.lng], {
              color: "red",
              fillColor: "#f03",
              fillOpacity: 0.5,
              radius: judet.raza,
            }).addTo(map);

            circle.bindPopup(
              `Județ: ${judet.judet}<br>Criteriu: ${judet.criteriu}<br>Valoare: ${judet.valoare}<br>Lună: ${judet.luna}`
            );
          });
        })
        .catch((error) => {
          console.error("Error:", error);
        });
      exportButton.after(mapDiv);
      document.body.appendChild(exportButton);
    };
  }
});
