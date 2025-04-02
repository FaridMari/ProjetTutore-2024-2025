<?php
include 'src/Gestionnaire/Navbar_Top.html';
$fichesParEnseignant = $GLOBALS['fichesParEnseignant'];
$enseignantsSansFiche = $GLOBALS['enseignantsSansFiche'] ?? [];
?>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<br>
<?php if (!empty($_SESSION['toast_message'])): ?>
    <div id="toast" class="toast-message">
        <?= htmlspecialchars($_SESSION['toast_message']) ?>
    </div>
    <script>
        setTimeout(() => {
            const toast = document.getElementById("toast");
            if (toast) toast.style.display = 'none';
        }, 4000);
    </script>
    <style>
        .toast-message {
            position: fixed;
            bottom: 30px;
            right: 30px;
            background-color: #333;
            color: white;
            padding: 15px 20px;
            border-radius: 5px;
            box-shadow: 0 2px 6px rgba(0,0,0,0.3);
            z-index: 1000;
            font-size: 14px;
        }
    </style>
    <?php unset($_SESSION['toast_message']); ?>
<?php endif; ?>

<div id="main-content">
    <br><h2>Gestion des Fiches Enseignants</h2>

    <ul class="nav nav-tabs" role="tablist">
        <li class="nav-item">
            <a class="nav-link active" id="tab-remplies" data-bs-toggle="tab" href="#fiches-remplies" role="tab">Fiches Remplies</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" id="tab-vides" data-bs-toggle="tab" href="#fiches-vides" role="tab">Fiches Non Remplies</a>
        </li>
    </ul>

    <div class="tab-content mt-3">
        <!-- Onglet Fiches Remplies -->
        <div class="tab-pane fade show active" id="fiches-remplies" role="tabpanel" aria-labelledby="tab-remplies">
            <div class="mb-3 filter-container">
                <div class="filter-group">
                    <label for="filtre">Filtrer par type de fiche :</label>
                    <select id="filtre" class="form-control custom-select" onchange="filterByType(this)">
                        <option value="all">Toutes</option>
                        <option value="Fiche Contrainte">Fiche Contrainte</option>
                        <option value="Fiche Ressource">Fiche Ressource</option>
                        <option value="Fiche Prévisionnelle">Fiche Prévisionnelle</option>
                    </select>
                </div>
                <div class="search-group">
                    <input type="text" id="searchInput" class="form-control search-input" onkeyup="filterByName()" placeholder="Rechercher un nom ou prénom...">
                    <i class="fa fa-search search-icon"></i>
                </div>
            </div>

            <?php if (empty($fichesParEnseignant)): ?>
                <p style="color: orange; font-weight: bold;">Aucune fiche à afficher.</p>
            <?php else: ?>
                <table style="width:100%; border-collapse: collapse; margin-top: 20px;">
                    <thead>
                    <tr style="background-color: #f0f0f0;">
                        <th style="padding: 10px; border: 1px solid #ddd;">Nom</th>
                        <th style="padding: 10px; border: 1px solid #ddd;">Prénom</th>
                        <th style="padding: 10px; border: 1px solid #ddd;">Type de fiche</th>
                        <th style="padding: 10px; border: 1px solid #ddd;">Statut</th>
                        <th style="padding: 10px; border: 1px solid #ddd;">Action</th>
                    </tr>
                    </thead>
                    <tbody id="fichesTable">
                    <?php foreach ($fichesParEnseignant as $fiche): ?>

                        <tr data-type="<?= htmlspecialchars($fiche['fiche_type']) ?>">
                            <td style="padding: 10px; border: 1px solid #ddd;"><?= htmlspecialchars($fiche['nom']) ?></td>
                            <td style="padding: 10px; border: 1px solid #ddd;"><?= htmlspecialchars($fiche['prenom']) ?></td>
                            <td style="padding: 10px; border: 1px solid #ddd;">
                                <button class="btn-fiche" onclick='loadFicheDetails(<?= json_encode($fiche, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT) ?>)'>📄 <?= htmlspecialchars($fiche['fiche_type']) ?></button>
                            </td>
                            <td style="padding: 10px; border: 1px solid #ddd;">
                                <?php if ($fiche['statut'] === 'validée'): ?>
                                    <span class="status-valid">Validée</span>
                                <?php else: ?>
                                    <span class="status-invalid">Non validée</span>
                                <?php endif; ?>
                            </td>
                            <td style="padding: 10px; border: 1px solid #ddd;">
                                <form method="post" action="index.php?action=ficheEnseignant">
                                    <input type="hidden" name="fiche_id" value="<?= $fiche['grouped_fiches'][0]['id_contrainte'] ?? $fiche['grouped_fiches'][0]['id_ressource'] ?? $fiche['grouped_fiches'][0]['id_voeu'] ?? '' ?>">
                                    <input type="hidden" name="table" value="<?= $fiche['table'] ?>">
                                    <?php if ($fiche['statut'] === 'validée'): ?>
                                        <button type="submit" name="action_type" value="devalider" class="btn-devalider">Dévalider</button>
                                    <?php else: ?>
                                        <button type="submit" name="action_type" value="valider" class="btn-valider">Valider</button>
                                    <?php endif; ?>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>
        <!-- Modal -->
        <div id="ficheModal" class="modal" style="display:none; position:fixed; top:10%; left:50%; transform:translateX(-50%); width:60%; background:#fff; border:1px solid #ccc; padding:20px; z-index:1000; box-shadow: 0 0 10px rgba(0,0,0,0.3);">
            <div class="modal-header">
                <h5 class="modal-title">Détails de la fiche</h5>
                <button type="button" class="close-button" onclick="fermerPopup()">&times;</button>
            </div>
            <div class="modal-body" id="ficheDetailsContainer"></div>
            <div class="modal-footer">
                <button onclick="fermerPopup()" class="btn-secondary">Fermer</button>
                <button id="modifierBtn" class="btn-modifier">Modifier</button>
            </div>
        </div>
        <div id="overlay" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.5); z-index:999;" onclick="fermerPopup()"></div>


        <!-- Onglet Fiches Non Remplies -->
        <div class="tab-pane fade" id="fiches-vides" role="tabpanel" aria-labelledby="tab-vides">
            <div class="alert alert-warning mt-3">
                Voici la liste des enseignants n’ayant pas encore rempli leurs fiches.
            </div>

            <!-- Filtres -->
            <div class="mb-3 filter-container">
                <div class="filter-group">
                    <label for="filtreVide">Filtrer par type de fiche :</label>
                    <select id="filtreVide" class="form-control custom-select" onchange="filtrerFichesVidesParType(this)">
                        <option value="all">Toutes</option>
                        <option value="Fiche Contrainte">Fiche Contrainte</option>
                        <option value="Fiche Prévisionnelle">Fiche Prévisionnelle</option>
                    </select>
                </div>
                <div class="search-group mt-2">
                    <input type="text" id="searchFichesVides" class="form-control search-input" onkeyup="filtrerNomFichesVides()" placeholder="Rechercher un nom ou prénom...">
                </div>
            </div>



            <?php if (empty($enseignantsSansFiche)): ?>
                <p class="text-muted">Tous les enseignants ont rempli leurs fiches.</p>
            <?php else: ?>
                <table class="table table-bordered mt-3">
                    <thead>
                    <tr>
                        <th>Nom</th>
                        <th>Prénom</th>
                        <th>Type de fiche</th>
                        <th>Action</th>
                    </tr>
                    </thead>
                    <tbody id="tableFichesVides">
                    <?php foreach ($enseignantsSansFiche as $enseignant): ?>
                        <?php if (($enseignant['type_fiche'] ?? '') === 'Fiche Ressource') continue; ?>
                        <tr data-type="<?= htmlspecialchars($enseignant['type_fiche'] ?? '') ?>">
                            <td><?= htmlspecialchars($enseignant['nom'] ?? 'Inconnu') ?></td>
                            <td><?= htmlspecialchars($enseignant['prenom'] ?? 'Inconnu') ?></td>
                            <td><?= htmlspecialchars($enseignant['type_fiche'] ?? 'Non précisé') ?></td>
                            <td>
                                <form method="get" action="src/Gestionnaire/Page_RemplirFiche.php">
                                    <input type="hidden" name="id" value="<?= htmlspecialchars($enseignant['id_utilisateur'] ?? '') ?>">
                                    <input type="hidden" name="type" value="<?= htmlspecialchars($enseignant['type_fiche'] ?? '') ?>">
                                    <button type="submit" class="btn btn-primary">Remplir</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>
    </div>
</div>


    <script>
        function fermerPopup() {
            document.getElementById("ficheModal").style.display = "none";
            document.getElementById("overlay").style.display = "none";
        }

        function filterByType(select) {
            let type = select.value;
            document.querySelectorAll('tr[data-type]').forEach(row => {
                row.style.display = (type === 'all' || row.dataset.type === type) ? '' : 'none';
            });
        }

        function filterByName() {
            let input = document.getElementById('searchInput');
            let filter = input.value.toLowerCase();
            let rows = document.querySelectorAll('#fichesTable tr');
            rows.forEach(row => {
                let nom = row.cells[0].textContent.toLowerCase();
                let prenom = row.cells[1].textContent.toLowerCase();
                row.style.display = (nom.includes(filter) || prenom.includes(filter)) ? '' : 'none';
            });
        }

        function loadFicheDetails(fiche) {
            const container = document.getElementById("ficheDetailsContainer");
            container.innerHTML = "";
            const title = document.createElement("h5");
            title.className = "detail-title";
            title.textContent = `${fiche.fiche_type} de ${fiche.prenom} ${fiche.nom}`;
            container.appendChild(title);
            let content = "<div class='fiche-content'>";
            const grouped = fiche.grouped_fiches;

            if (fiche.fiche_type === 'Fiche Prévisionnelle') {
                grouped.forEach((v, i) => {
                    content += `<div class='detail-section'><h6>Cours ${i + 1} - ${v.nom_cours || 'Non précisé'}</h6>`;
                    content += `<div class='detail-item'><strong>Formation :</strong> ${v.formation || 'Non précisé'} - <strong>Semestre :</strong> ${v.semestre || 'Non précisé'}</div>`;
                    content += `<div class='detail-item'><strong>Code :</strong> ${v.code_cours || 'Non précisé'}</div>`;
                    content += `<div class='detail-item'><strong>Volume horaire demandé :</strong><br>`;
                    content += `<div class="hours-grid">`;
                    content += `<div>CM : ${v.nb_heures_cm || '0'}h</div>`;
                    content += `<div>TD : ${v.nb_heures_td || '0'}h</div>`;
                    content += `<div>TP : ${v.nb_heures_tp || '0'}h</div>`;
                    content += `<div>EI : ${v.nb_heures_ei || '0'}h</div>`;
                    content += `</div>`;
                    if (v.remarques) content += `<div class='detail-item'><strong>Remarques :</strong> ${v.remarques}</div>`;
                    content += `</div>`;
                    if (i < grouped.length - 1) content += "<hr class='detail-divider'>";
                });
            } else if (fiche.fiche_type === 'Fiche Ressource') {
                grouped.forEach((r, i) => {
                    content += `<div class='detail-section'><h6>Ressource ${i + 1} - ${r.nom_cours || 'Non précisé'}</h6>`;
                    content += `<div class='detail-item'><strong>Code du cours :</strong> ${r.code_cours || 'Non précisé'}</div>`;
                    content += `<div class='detail-item'><strong>Semestre :</strong> ${r.semestre || 'Non précisé'}</div>`;
                    content += `<div class='detail-item'><strong>Type de salle :</strong> ${r.type_salle || 'Non précisé'}</div>`;
                    content += `<div class='detail-item'><strong>Équipements spécifiques :</strong> ${r.equipements_specifiques || 'Non précisé'}</div>`;
                    content += `<div class='detail-item'><strong>Système souhaité :</strong> ${r.systeme || 'Non précisé'}</div>`;
                    content += `<div class='detail-item'><strong>Réservations DS :</strong> ${r.ds || 'Non précisé'}</div>`;
                    content += `<div class='detail-item'><strong>Commentaire :</strong> ${r.commentaire || 'Aucun'}</div>`;
                    content += `</div>`;
                    if (i < grouped.length - 1) content += "<hr class='detail-divider'>";
                });
            } else if (fiche.fiche_type === 'Fiche Contrainte') {
                content += `<div class='detail-section'><strong>Créneaux à éviter :</strong><ul class="detail-list">`;
                grouped.forEach(c => {
                    if (c.jour && c.heure_debut && c.heure_fin) {
                        content += `<li>${c.jour} de ${c.heure_debut}h à ${c.heure_fin}h</li>`;
                    }
                });
                content += `</ul></div>`;
                const pref = grouped[0]?.creneau_preference || 'Non précisé';
                const samedi = grouped[0]?.cours_samedi === 'oui' ? 'L’enseignant accepte les cours le samedi.' : 'L’enseignant ne souhaite pas de cours le samedi.';
                const commentaire = grouped[0]?.commentaire || 'Aucun commentaire';
                content += `<div class='detail-item'><strong>Je préfère, si possible, éviter le créneau :</strong> ${pref}</div>`;
                content += `<div class='detail-item'><strong>Cours le samedi :</strong> ${samedi}</div>`;
                content += `<div class='detail-item'><strong>Commentaire :</strong> ${commentaire}</div>`;
            }

            content += "</div>";
            container.innerHTML += content;
            document.getElementById("ficheModal").style.display = "block";
            document.getElementById("overlay").style.display = "block";
            document.getElementById("modifierBtn").onclick = function () {
                const table = fiche.table;
                let id_utilisateur = fiche.grouped_fiches[0]['id_utilisateur'];
                window.location.href = `src/Gestionnaire/Page_ModifierFiche.php?table=${table}&id=${id_utilisateur}&type=${fiche.fiche_type}`;
            };
        }

        function filtrerFichesVidesParType(select) {
            const type = select.value.toLowerCase();
            const lignes = document.querySelectorAll('#tableFichesVides tr');

            lignes.forEach(row => {
                const typeFiche = row.getAttribute('data-type')?.toLowerCase() || '';
                const visibleParType = (type === 'all' || typeFiche === type);
                row.dataset.visibleType = visibleParType ? "true" : "false";
            });

            filtrerNomFichesVides();
        }

        function filtrerNomFichesVides() {
            const filtre = document.getElementById('searchFichesVides').value.toLowerCase();
            const lignes = document.querySelectorAll('#tableFichesVides tr');

            lignes.forEach(row => {
                const nom = row.cells[0].textContent.toLowerCase();
                const prenom = row.cells[1].textContent.toLowerCase();
                const visibleParNom = nom.includes(filtre) || prenom.includes(filtre);
                const visibleParType = row.dataset.visibleType !== "false";

                row.style.display = (visibleParNom && visibleParType) ? '' : 'none';
            });
        }


    </script>

    <style>
    /* Style général de la page */
    #main-content {
        max-width: 1200px;
        margin: 0 auto;
        padding: 20px;
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }

    h2 {
        color: #2c3e50;
        margin-bottom: 25px;
        border-bottom: 2px solid #eee;
        padding-bottom: 10px;
    }

    /* Filtres et recherche */
    .filter-container {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 20px;
        background-color: #f8f9fa;
        padding: 15px;
        border-radius: 8px;
        box-shadow: 0 2px 5px rgba(0,0,0,0.05);
    }

    .filter-group, .search-group {
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .search-group {
        position: relative;
        flex: 0 0 300px;
    }

    .search-input {
        width: 100%;
        padding: 10px 15px 10px 35px;
        border: 1px solid #ddd;
        border-radius: 6px;
        transition: border-color 0.3s, box-shadow 0.3s;
    }

    .search-input:focus {
        border-color: #4a89dc;
        box-shadow: 0 0 0 2px rgba(74, 137, 220, 0.2);
        outline: none;
    }

    .search-icon {
        position: absolute;
        left: 12px;
        top: 50%;
        transform: translateY(-50%);
        color: #777;
    }

    .custom-select {
        padding: 10px 15px;
        border: 1px solid #ddd;
        border-radius: 6px;
        background-color: white;
        cursor: pointer;
        min-width: 200px;
    }

    /* Tableau */
    table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 20px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        border-radius: 8px;
        overflow: hidden;
    }

    thead tr {
        background-color: #FFC300;
        color: white;
        text-align: left;
    }

    th, td {
        padding: 14px 15px;
        border: none;
        border-bottom: 1px solid #eee;
    }

    tbody tr:hover {
        background-color: #f5f7fa;
    }

    tbody tr:last-child td {
        border-bottom: none;
    }

    /* Statuts */
    .status-valid {
        background-color: #d4edda;
        color: #155724;
        padding: 6px 12px;
        border-radius: 50px;
        font-size: 0.9em;
        font-weight: 600;
        display: inline-block;
    }

    .status-invalid {
        background-color: #f8d7da;
        color: #721c24;
        padding: 6px 12px;
        border-radius: 50px;
        font-size: 0.9em;
        font-weight: 600;
        display: inline-block;
    }

    /* Boutons */
    .btn-fiche {
        background-color: transparent;
        color: black;
        border: 1px solid black;
        padding: 6px 12px;
        border-radius: 6px;
        cursor: pointer;
        transition: all 0.3s;
        font-size: 0.9em;
    }

    .btn-fiche:hover {
        background-color: #FFC300;
        color: white;
    }

    .btn-valider, .btn-primary {
        background-color: #28a745;
        color: white;
        border: none;
        padding: 8px 16px;
        border-radius: 6px;
        cursor: pointer;
        transition: background-color 0.3s;
        font-weight: 600;
    }

    .btn-valider:hover, .btn-primary:hover {
        background-color: #218838;
    }

    .btn-devalider, .btn-secondary {
        background-color: #6c757d;
        color: white;
        border: none;
        padding: 8px 16px;
        border-radius: 6px;
        cursor: pointer;
        transition: background-color 0.3s;
        font-weight: 600;
    }

    .btn-devalider:hover, .btn-secondary:hover {
        background-color: #5a6268;
    }

    .btn-modifier {
        background-color: #ffe65f;
        border: none;
        padding: .6em 1.2em;
        cursor: pointer;
        font-weight: bold;
        border-radius: 5px;
        text-decoration: none;
        color: black;
        display: inline-block;
    }


    /* Modal / Popup */
    #ficheModal {
        display: none;
        position: fixed;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        width: 70%;
        max-width: 800px;
        max-height: 85vh;
        background: #fff;
        border-radius: 10px;
        padding: 0;
        z-index: 1000;
        box-shadow: 0 5px 25px rgba(0,0,0,0.2);
        overflow: hidden;
        animation: fadeIn 0.3s ease;
    }

    @keyframes fadeIn {
        from { opacity: 0; transform: translate(-50%, -48%); }
        to { opacity: 1; transform: translate(-50%, -50%); }
    }

    .modal-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 15px 20px;
        background-color: #f8f9fa;
        border-bottom: 1px solid #eee;
    }

    .modal-title {
        margin: 0;
        color: black;
        font-size: 1.25rem;
    }

    .close-button {
        background: transparent;
        border: none;
        font-size: 1.5rem;
        line-height: 1;
        color: #777;
        cursor: pointer;
        transition: color 0.2s;
    }

    .close-button:hover {
        color: #333;
    }

    .modal-body {
        padding: 20px;
        max-height: calc(85vh - 130px);
        overflow-y: auto;
    }

    .modal-footer {
        display: flex;
        justify-content: flex-end;
        gap: 10px;
        padding: 15px 20px;
        background-color: #f8f9fa;
        border-top: 1px solid #eee;
    }

    /* Styles pour le contenu du popup */
    .detail-title {
        margin-top: 0;
        margin-bottom: 20px;
        color: #2c3e50;
        border-bottom: 1px solid #eee;
        padding-bottom: 10px;
    }

    .fiche-content {
        margin-bottom: 20px;
    }

    .detail-section {
        margin-bottom: 20px;
        padding: 15px;
        background-color: #f8f9fa;
        border-radius: 8px;
    }

    .detail-section h6 {
        margin-top: 0;
        color: #FFC300;
        font-size: 1rem;
        margin-bottom: 15px;
    }

    .detail-item {
        margin-bottom: 12px;
        line-height: 1.5;
    }

    .detail-item strong {
        color: #555;
    }

    .detail-list {
        margin: 10px 0;
        padding-left: 20px;
    }

    .detail-list li {
        margin-bottom: 8px;
        padding-left: 5px;
    }

    .detail-divider {
        border: 0;
        border-top: 1px solid #eee;
        margin: 15px 0;
    }

    .hours-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 10px;
        margin-top: 10px;
        margin-bottom: 10px;
    }

    /* Overlay */
    #overlay {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0,0,0,0.5);
        z-index: 999;
        opacity: 0;
        animation: overlayFadeIn 0.3s forwards;
    }

    @keyframes overlayFadeIn {
        from { opacity: 0; }
        to { opacity: 1; }
    }

    /* Responsivité */
    @media (max-width: 768px) {
        .filter-container {
            flex-direction: column;
            gap: 15px;
            align-items: stretch;
        }

        .search-group {
            flex: 1;
        }

        #ficheModal {
            width: 95%;
            max-width: none;
        }

        .hours-grid {
            grid-template-columns: 1fr;
        }
    }
</style>

