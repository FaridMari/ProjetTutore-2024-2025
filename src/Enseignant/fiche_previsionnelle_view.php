<?php
require_once __DIR__ . '/../Db/connexionFactory.php';
use src\Db\connexionFactory;
try {
  // Démarrer la session
  if (!isset($_SESSION)) {
      session_start();
  }

  $conn = connexionFactory::makeConnection();

  // Vérifier l'authentification
  if (!isset($_SESSION['id_utilisateur'])) {
      echo "<div style='color:red; padding:20px; font-family:Arial;'>Utilisateur non connecté.</div>";
      exit;
  }

  $id_utilisateur = $_SESSION['id_utilisateur'];

  $enseignantDTO    = new EnseignantDTO();
  $enseignant = $enseignantDTO->findByUtilisateurId($userId);
  $idEnseignant = $enseignant ? $enseignant->getIdEnseignant() : null;

  // PARTIE CRUCIALE : Vérifier le verrou
  // D'abord, vérifions si la requête fonctionne
  $stmtTest = $conn->prepare("SELECT 1");
  $stmtTest->execute();

  $stmtVerrouTemp = $conn->prepare("SELECT modification_en_cours FROM voeux WHERE id_enseignant = ?");
  $stmtVerrouTemp->execute([$idEnseignant]);
  $verrouTemp = $stmtVerrouTemp->fetch(PDO::FETCH_ASSOC);

  // Afficher la valeur pour débogage (sera visible dans le code source HTML)
  echo "<!-- Valeur de modification_en_cours : " .
      (($verrouTemp && isset($verrouTemp['modification_en_cours']))
          ? $verrouTemp['modification_en_cours'] : "non trouvée") . " -->";

  // Vérifier si le blocage doit être activé
  $doitBloquer = false;
  if ($verrouTemp && isset($verrouTemp['modification_en_cours']) && intval($verrouTemp['modification_en_cours']) === 1) {
      $doitBloquer = true;
  }

  // IMPORTANT: Afficher la page de blocage si nécessaire
  if ($doitBloquer) {
      // Ici, on affiche une version très simple d'abord pour s'assurer que ça fonctionne
      ?>
      <!DOCTYPE html>
      <html lang="fr">
      <head>
          <meta charset="UTF-8">
          <title>Fiche en cours de modification</title>
          <style>
              body {
                  font-family: Arial, sans-serif;
                  text-align: center;
                  padding: 50px;
              }
              .message {
                  background-color: #fff3cd;
                  color: #856404;
                  padding: 20px;
                  border-radius: 5px;
                  display: inline-block;
              }
              .btn {
                  margin-top: 20px;
                  padding: 10px 20px;
                  background-color: #FFEF65;
                  color: #000;
                  text-decoration: none;
                  border-radius: 5px;
              }
          </style>
      </head>
      <body>
      <div class="message">
          Votre fiche est en train d'être modifiée par le gestionnaire.
          <br><br>
          <a href="index.php?action=accueilEnseignant" class="btn">Retour à l'accueil</a>
      </div>
      </body>
      </html>
      <?php
      // IMPORTANT - Terminer l'exécution du script ici
      exit();
  }

  $verrouille = false;
  $stmtVerif = $conn->prepare("SELECT statut, date_validation FROM  voeux WHERE id_enseignant = ? LIMIT 1");
  $stmtVerif->execute([$idEnseignant]);
  $voeux = $stmtVerif->fetch(PDO::FETCH_ASSOC);
  if ($voeux && isset($voeux['statut']) && $voeux['statut'] === 'validée') {
      $verrouille = true;
  }

} catch (Exception $e) {
  // Afficher les erreurs
  echo "<div style='color:red; padding:20px; font-family:Arial;'>";
  echo "Erreur: " . $e->getMessage();
  echo "</div>";
  exit;
}

?>
<style>
    /* ================== STYLE DU DOCUMENT ================== */
    .container {
      /* Par exemple, ici on peut ajouter des marges ou un background */
    }
    .container h2 {
      text-transform: uppercase;
      font-weight: 600;
      color: #000;
      margin-bottom: 1em;
    }
    .alert.alert-warning {
      background-color: #FFEF65;
      color: #000;
      font-weight: 600;
      border: none;
      border-radius: 8px;
      box-shadow: 0 2px 6px rgba(0,0,0,0.1);
    }
    .table {
      border-color: #ddd;
      margin-bottom: 2em;
    }
    .table thead,
    .table thead tr.thead-light {
      background-color: #000 !important;
      color: #fff !important;
    }
    .table thead th {
      border-color: #000;
    }
    .total-row {
      background-color: #f9f9f9;
      font-weight: 600;
    }
    .table tbody tr:hover {
      background-color: #FFE74A;
      cursor: pointer;
    }
    .table input,
    .table select {
      border: none !important;
      box-shadow: none !important;
      background-color: transparent;
      width: 100%;
    }
    .table input:focus,
    .table select:focus {
      outline: none;
    }
    .table input[readonly] {
      color: #6c757d;
    }
    .btn.btn-success {
      background-color: #FFEF65;
      color: #000;
      border: none;
      font-weight: 600;
      transition: 0.3s ease;
    }
    .btn.btn-success:hover {
      background-color: #FFE74A;
      transform: scale(1.02);
    }
    .btn.btn-success:active,
    .btn.btn-success:focus {
      background-color: #FFD400;
      outline: none;
      transform: scale(0.98);
    }
    .btn.btn-primary {
      background-color: #FFEF65;
      color: #000;
      border: none;
      font-weight: 600;
      transition: 0.3s;
    }
    .btn.btn-primary:hover {
      background-color: #FFE74A;
      transform: scale(1.02);
    }
    .btn.btn-primary:focus,
    .btn.btn-primary:active {
      background-color: #FFD400;
      transform: scale(0.98);
      outline: none;
      border: none;
    }
    
    .weeks-grid {
        font-family: Arial, sans-serif;
        margin: 20px 0;
    }

    .selected-courses {
        margin-bottom: 20px;
    }

    .selected-courses h3 {
        font-size: 16px;
        margin-bottom: 10px;
    }

    .selected-courses ul {
        list-style-type: none;
        padding: 0;
        margin: 0;
    }

    .selected-courses li {
        padding: 5px 0;
    }

    .week-boxes {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(80px, 1fr));
        gap: 10px;
    }

    .week-box {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    height: 80px;
    border-radius: 6px;
    padding: 10px;
    background-color: #e8d46f;
    color: #fff;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    text-align: center;
    }

    .week-number {
        font-weight: bold;
        font-size: 16px;
        margin-bottom: 5px;
    }

    .week-hours {
        font-size: 14px;
    }
    
    #repartition-content {
      margin-left: 20px;
    }



  </style>

<div id="main-content">
  <div class="container mt-5" style="
    padding-left: 4em;">
    <!-- Structure des onglets -->
    <?php if ($verrouille): ?>
        <div class="alert alert-warning">
            Cette fiche a été validée et ne peut plus être modifiée.
        </div>
        <?php endif; ?>
    <ul class="nav nav-tabs" id="myTab" role="tablist">
      <li class="nav-item" role="presentation">
        <a class="nav-link active" id="fiche-tab" data-bs-toggle="tab" href="#fiche" role="tab" aria-controls="fiche" aria-selected="true">
          Fiche Prévisionnelle
        </a>
      </li>
      <li class="nav-item" role="presentation">
        <a class="nav-link" id="repartition-tab" data-bs-toggle="tab" href="#repartition" role="tab" aria-controls="repartition" aria-selected="false">
          Répartition des Heures
        </a>
      </li>
    </ul>
    <div class="d-flex justify-content-end mb-4">
    <button onclick="window.open('src/Enseignant/fiche_service_pdf.php', '_blank')" class="btn btn-primary">
      Exporter en PDF
    </button>
  </div>
    <div class="tab-content" id="myTabContent">
      <!-- Onglet Fiche Prévisionnelle (votre contenu existant) -->
      <div class="tab-pane fade show active" id="fiche" role="tabpanel" aria-labelledby="fiche-tab">
        <div id="main-content">
          <div class="container mt-5">
            <h2 class="text-center mb-4">Fiche Prévisionnelle de Service</h2>
            <p><strong>IUT Nancy-Charlemagne - Département Informatique</strong></p>
        
            <?php if (!empty($successMessage)): ?>
              <div class="alert alert-success">
                <?= htmlspecialchars($successMessage) ?>
              </div>
            <?php endif; ?>
        
            <?php if (!empty($errors)): ?>
              <div class="alert alert-danger">
                <?php foreach ($errors as $error): ?>
                  <p><?= htmlspecialchars($error) ?></p>
                <?php endforeach; ?>
              </div>
            <?php endif; ?>
        
            <form action="" method="post">
  <!-- Utilisation de fieldset pour désactiver l'ensemble des contrôles si $verrouille est vrai -->
  <fieldset <?= $verrouille ? 'disabled' : '' ?>>
    <input type="hidden" name="septembre_count" value="<?= $septembreCount ?>">
    <input type="hidden" name="janvier_count" value="<?= $janvierCount ?>">

    <div class="alert alert-warning font-weight-bold">Enseignements sur la période SEPTEMBRE-JANVIER</div>
    <div class="table-responsive">
      <table class="table table-bordered text-center w-100" id="table-septembre">
        <thead class="thead-light">
          <tr>
            <th>Formation BUT</th>
            <th>Ressource / SAE</th>
            <th>Semestre</th>
            <th>CM</th>
            <th>TD</th>
            <th>TP</th>
            <th>EI</th>
            <th>Remarques</th>
            <th>Action</th>
          </tr>
        </thead>
        <tbody>
          <?php generateTableRows('septembre', $coursList, $septembreCount, $postData); ?>
          <tr class="total-row">
            <td colspan="3" class="font-weight-bold">Total :</td>
            <td>0</td>
            <td>0</td>
            <td>0</td>
            <td>0</td>
            <td colspan="2"></td>
          </tr>
        </tbody>
      </table>
    </div>
    <div class="text-center mb-4">
      <button type="button" id="add_line_septembre" class="btn btn-success">Ajouter une ligne (Septembre)</button>
    </div>

    <div class="alert alert-warning font-weight-bold">Enseignements sur la période JANVIER-JUIN</div>
    <div class="table-responsive">
      <table class="table table-bordered text-center w-100" id="table-janvier">
        <thead class="thead-light">
          <tr>
            <th>Formation BUT</th>
            <th>Ressource / SAE</th>
            <th>Semestre</th>
            <th>CM</th>
            <th>TD</th>
            <th>TP</th>
            <th>EI</th>
            <th>Remarques</th>
            <th>Action</th>
          </tr>
        </thead>
        <tbody>
          <?php generateTableRows('janvier', $coursList, $janvierCount, $postData); ?>
          <tr class="total-row">
            <td colspan="3" class="font-weight-bold">Total :</td>
            <td>0</td>
            <td>0</td>
            <td>0</td>
            <td>0</td>
            <td colspan="2"></td>
          </tr>
        </tbody>
      </table>
    </div>
    <div class="text-center mb-4">
      <button type="button" id="add_line_janvier" class="btn btn-success">Ajouter une ligne (Janvier)</button>
    </div>

    <div class="alert alert-warning font-weight-bold">TOTAL :</div>
    <div class="table-responsive">
      <table class="table table-bordered text-center w-100" id="table-dept-info">
        <thead>
          <tr>
            <th>CM</th>
            <th>TD</th>
            <th>TP</th>
            <th>EI</th>
          </tr>
        </thead>
        <tbody>
          <tr>
            <td>0</td>
            <td>0</td>
            <td>0</td>
            <td>0</td>
          </tr>
        </tbody>
      </table>
    </div>

    <div class="alert alert-warning font-weight-bold">Enseignements hors Dept Info (pour information)</div>
    <div class="table-responsive">
      <table class="table table-bordered text-center w-100" id="table_hors_iut">
        <thead class="thead-light">
          <tr>
            <th>Composants</th>
            <th>Formation</th>
            <th>Module</th>
            <th>CM</th>
            <th>TD</th>
            <th>TP</th>
            <th>EI</th>
            <th>Total</th>
            <th>HETD</th>
            <th>Action</th>
          </tr>
        </thead>
        <tbody>
          <?php 
            if (isset($postData['hors_iut'])) {
                generateHorsIUTRows($postData['hors_iut']);
            }
          ?>
        </tbody>
      </table>
    </div>
    <div class="text-center mb-4">
      <button type="button" id="add_line_hors_info" class="btn btn-success">Ajouter une ligne hors IUT</button>
    </div>

    <div class="text-center mt-4">
      <button type="submit" name="envoyer" class="btn btn-primary">Envoyer</button>
    </div>
  </fieldset>
</form>
          </div>
        
          <!-- Templates pour l'ajout dynamique de lignes -->
          <template id="template-septembre">
            <tr>
              <input type="hidden" name="septembre[id][]" value="">
              <input type="hidden" name="septembre[course_id][]" value="">
              <td><input type="text" name="septembre[formation][]" readonly></td>
              <td>
                <select name="septembre[ressource][]">
                  <option value="" <?= $verrouille ? 'disabled' : '' ?>>-- Sélectionner un cours --</option>
                </select>
              </td>
              <td><input type="text" name="septembre[semestre][]" readonly></td>
              <td><input type="number" name="septembre[cm][]"></td>
              <td><input type="number" name="septembre[td][]"></td>
              <td><input type="number" name="septembre[tp][]"></td>
              <td><input type="number" name="septembre[ei][]"></td>
              <td><input type="text" name="septembre[remarques][]"></td>
              <td><button type="button" class="btn btn-danger btn-sm remove-line" 
              <?= $verrouille ? 'disabled' : '' ?>>&times;</button></td>
            </tr>
          </template>
        
          <template id="template-janvier">
            <tr>
              <input type="hidden" name="janvier[id][]" value="">
              <input type="hidden" name="janvier[course_id][]" value="">
              <td><input type="text" name="janvier[formation][]" readonly></td>
              <td>
                <select name="janvier[ressource][]">
                  <option value="">-- Sélectionner un cours --</option>
                </select>
              </td>
              <td><input type="text" name="janvier[semestre][]" readonly></td>
              <td><input type="number" name="janvier[cm][]"></td>
              <td><input type="number" name="janvier[td][]"></td>
              <td><input type="number" name="janvier[tp][]"></td>
              <td><input type="number" name="janvier[ei][]"></td>
              <td><input type="text" name="janvier[remarques][]"></td>
              <td><button type="button" class="btn btn-danger btn-sm remove-line"
              <?= $verrouille ? 'disabled' : '' ?>>&times;</button></td>
            </tr>
          </template>
        </div>
      </div>
      <!-- Onglet Répartition des Heures -->
      <div class="tab-pane fade" id="repartition" role="tabpanel" aria-labelledby="repartition-tab">
        <div id="repartition-content" class="mt-3">
          <p>Sélectionnez un ou plusieurs cours dans la fiche prévisionnelle pour voir leur répartition hebdomadaire des heures.</p>
        </div>
      </div>
    </div>
  </div>
</div>
  
  <!-- Inclusion du bundle Bootstrap avec Popper -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/js/bootstrap.bundle.min.js"></script>
  
  <!-- Injection des données des cours depuis PHP -->
  <script>
    window.coursData = <?= json_encode(array_map(function($c) {
      return [
        'nomCours'  => $c->getNomCours(),
        'formation' => $c->getFormation(),
        'semestre'  => $c->getSemestre(),
        'cm'        => $c->getNbHeuresCM(),
        'td'        => $c->getNbHeuresTD(),
        'tp'        => $c->getNbHeuresTP(),
        'ei'        => $c->getNbHeuresEI(),
        'total'     => $c->getNbHeuresTotal(),
        'codeCours' => $c->getCodeCours(),
        'idCours'   => $c->getIdCours()
      ];
    }, $coursList)); ?>;
  </script>
  
  <!-- Votre JavaScript existant + la fonction pour récupérer la répartition -->
  <script>
    document.addEventListener('DOMContentLoaded', function () {
  
      // Mise à jour d'une ligne lors du changement du select
      function updateLine(selectElem) {
  var tr = selectElem.closest('tr');
  var nomCours = selectElem.value;
  var coursInfo = window.coursData.find(function(c) {
      return c.nomCours === nomCours;
  });
  var formationInput = tr.querySelector('td:nth-of-type(1) input');
  var semestreInput  = tr.querySelector('td:nth-of-type(3) input');
  var numberInputs   = tr.querySelectorAll('input[type="number"]');
  // Récupération du champ caché course_id
  var courseIdInput = tr.querySelector('input[name$="[course_id][]"]');
  
  if (coursInfo) {
      formationInput.value = coursInfo.formation;
      semestreInput.value  = coursInfo.semestre;
      if (!numberInputs[0].value) numberInputs[0].value = coursInfo.cm;
      if (!numberInputs[1].value) numberInputs[1].value = coursInfo.td;
      if (!numberInputs[2].value) numberInputs[2].value = coursInfo.tp;
      if (!numberInputs[3].value) numberInputs[3].value = coursInfo.ei;
      if (courseIdInput) {
          courseIdInput.value = coursInfo.idCours;
      }
  } else {
      formationInput.value = '';
      semestreInput.value  = '';
      numberInputs.forEach(function(input) { input.value = ''; });
      if (courseIdInput) {
          courseIdInput.value = '';
      }
  }
  
  var table = selectElem.closest('table');
  if (table && table.id) {
    updateTotals(table.id);
  }
  updateDeptInfoTotals();
}


      
      // Peupler les options du select en fonction du type
      function populateCoursOptions(selectElem, type) {
        var allowedSemesters = type === 'septembre' ? ['1','3','5'] : ['2','4','6'];
        selectElem.innerHTML = '<option value="">-- Sélectionner un cours --</option>';
        window.coursData.forEach(function(cour) {
          if (allowedSemesters.includes(cour.semestre)) {
            var option = document.createElement('option');
            option.value = cour.nomCours;
            option.text = cour.codeCours + ' - ' + cour.nomCours;
            // Ajout de l'attribut data-id (ici, nous utilisons codeCours pour l'exemple, à adapter si besoin)
            option.setAttribute('data-id', cour.idCours);
            selectElem.appendChild(option);
          }
        });
      }
      
      // Ajout d'une nouvelle ligne dans le tableau d'un type donné
      function addLine(type) {
        var template = document.getElementById('template-' + type);
        if (!template) return;
        var newRow = template.content.cloneNode(true);
        var selectElem = newRow.querySelector('select');
        populateCoursOptions(selectElem, type);
        selectElem.addEventListener('change', function() {
            updateLine(this);
            fetchRepartition();
        });
        newRow.querySelector('.remove-line').addEventListener('click', function() {
            var row = this.closest('tr');
            var table = row ? row.closest('table') : null;
            if (row) row.remove();
            if (table && table.id) {
              updateTotals(table.id);
            }
            updateDeptInfoTotals();
        });
        var tbody = document.getElementById('table-' + type).querySelector('tbody');
        var totalRow = tbody.querySelector('tr.total-row');
        tbody.insertBefore(newRow, totalRow);
      }
      
      // Calcul des totaux pour les voeux (septembre/janvier)
      function updateTotals(tableId) {
        var table = document.getElementById(tableId);
        if (!table) return;
        var rows = table.querySelectorAll('tbody tr');
        var totalRow = table.querySelector('tr.total-row');
        if (!totalRow) return;
        
        var cmSum = 0, tdSum = 0, tpSum = 0, eiSum = 0;
        
        rows.forEach(function(row) {
          if (row.classList.contains('total-row')) return;
          var cells = row.querySelectorAll('td');
          var cm = parseFloat((cells[3].querySelector('input') || {}).value) || 0;
          var td = parseFloat((cells[4].querySelector('input') || {}).value) || 0;
          var tp = parseFloat((cells[5].querySelector('input') || {}).value) || 0;
          var ei = parseFloat((cells[6].querySelector('input') || {}).value) || 0;
          
          cmSum += cm;
          tdSum += td;
          tpSum += tp;
          eiSum += ei;
        });
        
        var totalCells = totalRow.querySelectorAll('td');
        totalCells[1].textContent = cmSum;
        totalCells[2].textContent = tdSum;
        totalCells[3].textContent = tpSum;
        totalCells[4].textContent = eiSum;
      }
      
      // Calcul des totaux globaux (Dept Info) incluant les voeux hors IUT
      function updateDeptInfoTotals() {
        var septTable = document.getElementById('table-septembre');
        var janTable  = document.getElementById('table-janvier');
        var horsIutTable = document.getElementById('table_hors_iut');
        var deptTable = document.getElementById('table-dept-info');
        if (!septTable || !janTable || !horsIutTable || !deptTable) return;
        
        var septTotalRow = septTable.querySelector('tr.total-row');
        var septCM = septTotalRow ? (parseFloat(septTotalRow.querySelectorAll('td')[1].textContent) || 0) : 0;
        var septTD = septTotalRow ? (parseFloat(septTotalRow.querySelectorAll('td')[2].textContent) || 0) : 0;
        var septTP = septTotalRow ? (parseFloat(septTotalRow.querySelectorAll('td')[3].textContent) || 0) : 0;
        var septEI = septTotalRow ? (parseFloat(septTotalRow.querySelectorAll('td')[4].textContent) || 0) : 0;
        
        var janTotalRow = janTable.querySelector('tr.total-row');
        var janCM = janTotalRow ? (parseFloat(janTotalRow.querySelectorAll('td')[1].textContent) || 0) : 0;
        var janTD = janTotalRow ? (parseFloat(janTotalRow.querySelectorAll('td')[2].textContent) || 0) : 0;
        var janTP = janTotalRow ? (parseFloat(janTotalRow.querySelectorAll('td')[3].textContent) || 0) : 0;
        var janEI = janTotalRow ? (parseFloat(janTotalRow.querySelectorAll('td')[4].textContent) || 0) : 0;
        
        var horsIutRows = horsIutTable.querySelectorAll('tbody tr');
        var horsCM = 0, horsTD = 0, horsTP = 0, horsEI = 0;
        horsIutRows.forEach(function(row) {
          var cells = row.querySelectorAll('td');
          horsCM += parseFloat((cells[3].querySelector('input') || {}).value) || 0;
          horsTD += parseFloat((cells[4].querySelector('input') || {}).value) || 0;
          horsTP += parseFloat((cells[5].querySelector('input') || {}).value) || 0;
          horsEI += parseFloat((cells[6].querySelector('input') || {}).value) || 0;
        });
        
        var deptCM = septCM + janCM + horsCM;
        var deptTD = septTD + janTD + horsTD;
        var deptTP = septTP + janTP + horsTP;
        var deptEI = septEI + janEI + horsEI;
        
        var deptRow = deptTable.querySelector('tbody tr');
        var deptCells = deptRow.querySelectorAll('td');
        deptCells[0].textContent = deptCM;
        deptCells[1].textContent = deptTD;
        deptCells[2].textContent = deptTP;
        deptCells[3].textContent = deptEI;
      }
      
      document.getElementById('add_line_septembre').addEventListener('click', function() {
        addLine('septembre');
      });
      document.getElementById('add_line_janvier').addEventListener('click', function() {
        addLine('janvier');
      });
      
      document.getElementById('add_line_hors_info').addEventListener('click', function() {
        var tableBody = document.getElementById('table_hors_iut').querySelector('tbody');
        var newRow = tableBody.insertRow();
        
        var cellNames = ['composant', 'formation', 'module', 'cm', 'td', 'tp', 'ei', 'total', 'hetd'];
        cellNames.forEach(function(name) {
          var cell = newRow.insertCell();
          var input = document.createElement('input');
          if (['cm', 'td', 'tp', 'ei'].includes(name)) {
            input.type = 'number';
            input.min  = '0';
            input.step = '0.1';
            input.required = true;
            input.addEventListener('input', calculateHorsIUTTotal);
          } else if (['total', 'hetd'].includes(name)) {
            input.type = 'number';
            input.readOnly = true;
          } else {
            input.type = 'text';
            input.required = true;
          }
          input.name = 'hors_iut[' + name + '][]';
          cell.appendChild(input);
        });
        var actionCell = newRow.insertCell();
        var deleteBtn = document.createElement('button');
        deleteBtn.type = 'button';
        deleteBtn.className = 'btn btn-danger btn-sm remove-line';
        deleteBtn.innerHTML = '&times;';
        deleteBtn.addEventListener('click', function() {
          var row = this.closest('tr');
          var table = row ? row.closest('table') : null;
          if (row) row.remove();
          if (table && table.id) {
            updateTotals(table.id);
          }
          updateDeptInfoTotals();
        });
        actionCell.appendChild(deleteBtn);
      });
      
      function calculateHorsIUTTotal() {
        var row = this.closest('tr');
        var cm   = parseFloat(row.querySelector('input[name="hors_iut[cm][]"]').value) || 0;
        var td   = parseFloat(row.querySelector('input[name="hors_iut[td][]"]').value) || 0;
        var tp   = parseFloat(row.querySelector('input[name="hors_iut[tp][]"]').value) || 0;
        var ei   = parseFloat(row.querySelector('input[name="hors_iut[ei][]"]').value) || 0;
        var total = cm + td + tp + ei;
        var hetd  = total * 1.5;
        row.querySelector('input[name="hors_iut[total][]"]').value = total.toFixed(1);
        row.querySelector('input[name="hors_iut[hetd][]"]').value = hetd.toFixed(1);
        updateTotals('table_hors_iut');
        updateDeptInfoTotals();
      }
      
      // Pour les selects déjà présents au chargement du DOM
      document.querySelectorAll('table select').forEach(function(selectElem) {
        selectElem.addEventListener('change', function() {
            updateLine(this);
            fetchRepartition();
        });
        if (selectElem.value !== "") {
            updateLine(selectElem);
        }
      });

      // Fonction pour récupérer la répartition des heures via AJAX
      function fetchRepartition() {
        var selectedCourseIds = [];
        // Parcourir les selects de la fiche prévisionnelle (septembre et janvier)
        document.querySelectorAll('select[name="septembre[ressource][]"], select[name="janvier[ressource][]"]').forEach(function(selectElem) {
            if (selectElem.value !== "") {
                // On récupère l'attribut data-id
                var courseId = selectElem.options[selectElem.selectedIndex].getAttribute('data-id');
                if (courseId && !selectedCourseIds.includes(courseId)) {
                    selectedCourseIds.push(courseId);
                }
            }
        });
        
        if(selectedCourseIds.length === 0) {
            document.getElementById('repartition-content').innerHTML = "<p>Aucun cours sélectionné.</p>";
            return;
        }
        
        fetch('src/Enseignant/get_repartitions_service.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ course_ids: selectedCourseIds })
        })
        .then(response => response.json())
        .then(data => {
            // Création d'un tableau pour stocker les heures par semaine
            const weeklyHours = {};
            const allCourses = {};
            
            // Trouver les semaines min et max pour créer toutes les cases
            let minWeek = 52;
            let maxWeek = 1;
            
            // Parcourir les données pour calculer les heures par semaine
            data.forEach(item => {
                // Stocker les informations de cours
                if (!allCourses[item.nomCours]) {
                    allCourses[item.nomCours] = {
                        nom: item.nomCours,
                        semestre: item.semestre,
                        heuresSemaine: item.nbHeuresSemaine
                    };
                }
                
                // Calculer les heures pour chaque semaine du cours
                const startWeek = parseInt(item.semaineDebut);
                const endWeek = parseInt(item.semaineFin);
                
                // Mettre à jour les semaines min et max
                if (startWeek < minWeek) minWeek = startWeek;
                if (endWeek > maxWeek) maxWeek = endWeek;
                
                // Attribuer les heures à chaque semaine
                for (let week = startWeek; week <= endWeek; week++) {
                    if (!weeklyHours[week]) {
                        weeklyHours[week] = 0;
                    }
                    weeklyHours[week] += parseFloat(item.nbHeuresSemaine);
                }
            });
            
            let html = "";
            
            if (Object.keys(weeklyHours).length > 0) {
                html += "<div class='weeks-grid'>";
                
                // Liste des cours sélectionnés en haut
                html += "<div class='selected-courses'>";
                html += "<h3>Cours sélectionnés</h3>";
                html += "<ul>";
                Object.values(allCourses).forEach(course => {
                    html += `<li>${course.nom} - Semestre ${course.semestre}</li>`;
                });
                html += "</ul></div>";
                
                // Grille des semaines
                html += "<div class='week-boxes'>";
                
                // Créer une case pour chaque semaine
                for (let week = minWeek; week <= maxWeek; week++) {
                    const hours = weeklyHours[week] || 0;
                    
                    // Utiliser une couleur fixe pour chaque case
                    html += `<div class='week-box'>
                        <div class='week-number'>S${week}</div>
                        <div class='week-hours'>${hours.toFixed(1)}h</div>
                    </div>`;
                }
                
                html += "</div></div>";
            } else {
                html = "<p>Aucune répartition trouvée pour les cours sélectionnés.</p>";
            }
            
            document.getElementById('repartition-content').innerHTML = html;
        })
        .catch(error => {
            console.error('Erreur :', error);
            document.getElementById('repartition-content').innerHTML = "<p>Une erreur s'est produite lors de la récupération des répartitions.</p>";
        });
      }

      // Observer pour surveiller la suppression de lignes du tableau
      function setupTableRowObserver() {
        // Trouver tous les tableaux qui contiennent nos selects
        const tables = Array.from(document.querySelectorAll('table')).filter(table => {
            return table.querySelectorAll('select[name="septembre[ressource][]"], select[name="janvier[ressource][]"]').length > 0;
        });
        
        if (tables.length === 0) return;
        
        // Pour chaque tableau, surveiller les modifications
        tables.forEach(table => {
            const observer = new MutationObserver(mutations => {
                let shouldUpdate = false;
                
                mutations.forEach(mutation => {
                    // Vérifier si des nœuds ont été supprimés (lignes de tableau)
                    if (mutation.type === 'childList' && mutation.removedNodes.length > 0) {
                        shouldUpdate = true;
                    }
                });
                
                if (shouldUpdate) {
                    fetchRepartition();
                }
            });
            
            // Observer le corps du tableau pour les suppressions de lignes
            const tbody = table.querySelector('tbody') || table;
            observer.observe(tbody, { childList: true, subtree: true });
        });
      }

      // Fonction pour ajouter les écouteurs aux boutons de suppression de ligne
      function attachRowDeleteButtons() {
        // Trouver tous les boutons qui pourraient supprimer une ligne
        document.querySelectorAll('button.delete-row, button.remove-row, button.btn-danger, .delete-btn, [data-action="delete"], [data-action="remove"]').forEach(button => {
            button.removeEventListener('click', onRowDeleted);
            button.addEventListener('click', onRowDeleted);
        });
      }

      // Fonction appelée lorsqu'une ligne est supprimée
      function onRowDeleted() {
        setTimeout(fetchRepartition, 50);  // Petit délai pour laisser le DOM se mettre à jour
      }

      // Attacher aux événements de changement de select
      function attachSelectEvents() {
        document.querySelectorAll('select[name="septembre[ressource][]"], select[name="janvier[ressource][]"]').forEach(selectElem => {
            selectElem.removeEventListener('change', fetchRepartition);
            selectElem.addEventListener('change', fetchRepartition);
        });
      }

      // Fonction d'initialisation générale
      function initRepartition() {
        attachSelectEvents();
        attachRowDeleteButtons();
        setupTableRowObserver();
        
        // Capture les événements spécifiques aux frameworks JS courants
        document.addEventListener('rowDeleted', fetchRepartition);
        document.addEventListener('tableUpdated', fetchRepartition);
        document.addEventListener('courseRemoved', fetchRepartition);
        
        // Exécuter fetchRepartition une première fois
        fetchRepartition();
      }

      // Exécuter à la fin du chargement de la page
      initRepartition();

      // Ajout du déclenchement des 'change' sur les selects pré-remplis ---
      document.querySelectorAll('select[name="septembre[ressource][]"], select[name="janvier[ressource][]"]').forEach(function(selectElem) {
        if (selectElem.value !== "") {
          var coursInfo = window.coursData.find(function(c) {
            return c.nomCours === selectElem.value;
          });
          if (coursInfo) {
            selectElem.options[selectElem.selectedIndex].setAttribute('data-id', coursInfo.idCours);
          }
          selectElem.dispatchEvent(new Event('change'));
        }
      });

      // Ajout d'un écouteur d'événement global pour supprimer les lignes ---
      document.addEventListener('click', function(e) {
        if (e.target.classList.contains('remove-line')) {
          var row = e.target.closest('tr');
          var table = row ? row.closest('table') : null;
          if (row) row.remove();
          if (table && table.id) {
            updateTotals(table.id);
          }
          updateDeptInfoTotals();
        }
      });
  

      // Réexécuter après chaque modification majeure du DOM
      const bodyObserver = new MutationObserver(mutations => {
        let significantChange = false;
        
        mutations.forEach(mutation => {
            if (mutation.type === 'childList' && 
                (mutation.addedNodes.length > 0 || mutation.removedNodes.length > 0)) {
                // Vérifier s'il y a eu des changements importants (comme l'ajout/suppression de selects ou boutons)
                const addedSelects = Array.from(mutation.addedNodes).some(node => 
                    node.nodeType === 1 && (
                        node.tagName === 'SELECT' || 
                        node.querySelectorAll('select').length > 0
                    )
                );
                
                const addedButtons = Array.from(mutation.addedNodes).some(node => 
                    node.nodeType === 1 && (
                        node.tagName === 'BUTTON' || 
                        node.querySelectorAll('button').length > 0
                    )
                );
                
                if (addedSelects || addedButtons) {
                    significantChange = true;
                }
            }
        });
        
        if (significantChange) {
            attachSelectEvents();
            attachRowDeleteButtons();
        }
      });

      // Observer le corps entier du document pour les changements majeurs
      bodyObserver.observe(document.body, { childList: true, subtree: true });
  
    });
  </script>
</body>
</html>
