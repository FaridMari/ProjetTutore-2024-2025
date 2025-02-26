<style>
    .accueil-enseignant {
        text-align: center;
        padding: 2em;
    }

    .accueil-enseignant h1 {
        font-size: 2rem;
        color: #000;
        margin-bottom: 0.5em;
    }
    .accueil-enseignant h3 {
        font-size: 1.2rem;
        color: #444;
        margin-bottom: 2em;
    }

    .bulle-container {
        display: flex;
        flex-wrap: wrap;
        justify-content: center;
        gap: 2em;
    }

    .bulle {
        width: 20%;
        height: 220px;
        background-color: #FFEF65;
        color: #000;
        border-radius: 10px;
        box-shadow: 0 4px 10px rgba(0,0,0,0.2);
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        transition: transform 0.3s, box-shadow 0.3s;
        cursor: pointer;
    }

    .bulle h2 {
        font-size: 1.1rem;
        margin-bottom: 0.5em;
        text-transform: uppercase;
        color: #000;
    }

    .bulle .valeur {
        font-size: 1.5rem;
        font-weight: 600;
    }

    .voeux-container {
        display: flex;
        justify-content: center;
        align-items: center;
        margin-top: 5em;
        gap: 2em;
    }

    .separator {
        width: 2px;
        background-color: #000;
        height: 150px;
    }

    .voeux-box {
        width: auto;
        min-width: 40%;
        padding: 1em;
        background: #f9f9f9;
        border-radius: 10px;
        box-shadow: 0 2px 5px rgba(0,0,0,0.2);
    }

    .voeux-box h2 {
        font-size: 1.5rem;
        color: #333;
        margin-bottom: 1em;
    }

    .voeu, .affectation {
        padding: 0.5em;
        border-bottom: 1px solid #ddd;
        font-size: 1.1rem;
        font-weight: bold;
    }

    .voeu, .affectation span {
        margin: 0 0.5em;
    }
</style>

<div id="main-content">
    <div class="accueil-enseignant">
        <form method="post" action="index.php?action=accueilEnseignant">
            <?php
            $nom = "";
            $prenom = "";
            $role = "";
            $nb_heures_a_effectuer = "";
            $nb_heures_affectees = 0;

            use src\Db\connexionFactory;
            $pdo = connexionFactory::makeConnection();

            // Récupérer les informations de l'utilisateur
            $stmt = $pdo->prepare("SELECT * FROM utilisateurs WHERE id_utilisateur = :id_utilisateur");
            $stmt->bindParam(':id_utilisateur', $_SESSION['user_id'], PDO::PARAM_INT);
            $stmt->execute();
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($user) {
                $nom = $user['nom'];
                $prenom = $user['prenom'];
                $role = $user['role'];
                $nb_heures_a_effectuer = $user['nombre_heures'];

                // Récupérer l'ID enseignant associé à l'utilisateur
                $stmt = $pdo->prepare("SELECT id_enseignant FROM enseignants WHERE id_utilisateur = :id_utilisateur");
                $stmt->bindParam(':id_utilisateur', $_SESSION['user_id'], PDO::PARAM_INT);
                $stmt->execute();
                $enseignant = $stmt->fetch(PDO::FETCH_ASSOC);

                if ($enseignant) {
                    $id_enseignant = $enseignant['id_enseignant'];

                    // Calculer la somme des heures affectées pour cet enseignant
                    $stmt = $pdo->prepare("SELECT SUM(heures_affectees) AS total_heures_affectees FROM affectations WHERE id_enseignant = :id_enseignant");
                    $stmt->bindParam(':id_enseignant', $id_enseignant, PDO::PARAM_INT);
                    $stmt->execute();
                    $result = $stmt->fetch(PDO::FETCH_ASSOC);

                    if ($result && $result['total_heures_affectees'] !== null) {
                        $nb_heures_affectees = $result['total_heures_affectees'];
                    }
                }
            }
            ?>

            <h1>Bienvenue <?php echo $nom . " " . $prenom; ?></h1>

            <div class="bulle-container">
                <div class="bulle">
                    <h2>Role</h2>
                    <p class="valeur"><?php echo $role; ?></p>
                </div>
                <div class="bulle">
                    <h2>Heures à effectuer</h2>
                    <p class="valeur"><?php echo $nb_heures_a_effectuer; ?></p>
                </div>
                <div class="bulle">
                    <h2>Heures affectées</h2>
                    <p class="valeur"><?php echo $nb_heures_affectees; ?></p>
                </div>
            </div>
        </form>


        <div class="voeux-container">
            <div class="voeux-box">
                <h2>Voeux formulés</h2>
                <?php
                // Récupérer l'ID enseignant à partir de l'ID utilisateur
                $stmt = $pdo->prepare("SELECT id_enseignant FROM enseignants WHERE id_utilisateur = :id_utilisateur");
                $stmt->bindParam(':id_utilisateur', $_SESSION['user_id'], PDO::PARAM_INT);
                $stmt->execute();
                $enseignant = $stmt->fetch(PDO::FETCH_ASSOC);

                if ($enseignant) {
                    $id_enseignant = $enseignant['id_enseignant'];

                    // Récupérer les vœux de cet enseignant
                    $stmt = $pdo->prepare("SELECT * FROM voeux WHERE id_enseignant = :id_enseignant");
                    $stmt->bindParam(':id_enseignant', $id_enseignant, PDO::PARAM_INT);
                    $stmt->execute();
                    $voeux = $stmt->fetchAll(PDO::FETCH_ASSOC);

                    if ($voeux) {
                        foreach ($voeux as $voeu) {
                            // Récupérer les infos du cours correspondant à l'id_cours
                            $stmt = $pdo->prepare("SELECT code_cours, nom_cours FROM cours WHERE id_cours = :id_cours");
                            $stmt->bindParam(':id_cours', $voeu['id_cours'], PDO::PARAM_INT);
                            $stmt->execute();
                            $cours = $stmt->fetch(PDO::FETCH_ASSOC);

                            if ($cours) {
                                $nom_affichage = htmlspecialchars($cours['code_cours']) . " - " . htmlspecialchars($cours['nom_cours']);
                            } else {
                                $nom_affichage = "Cours inconnu";
                            }

                            echo "<div class='voeu'>";
                            echo "<span>" . $nom_affichage . "</span> | ";
                            echo "<span>CM: " . htmlspecialchars($voeu['nb_CM']) . "</span> | ";
                            echo "<span>TD: " . htmlspecialchars($voeu['nb_TD']) . "</span> | ";
                            echo "<span>TP: " . htmlspecialchars($voeu['nb_TP']) . "</span> | ";
                            echo "<span>EI: " . htmlspecialchars($voeu['nb_EI']) . "</span>";
                            echo "</div>";
                        }
                    } else {
                        echo "<p>Aucun vœu formulé.</p>";
                    }
                } else {
                    echo "<p>Aucun vœu formulé.</p>";
                }
                ?>

            </div>

            <div class="separator"></div>

            <div class="voeux-box">
                <h2>Affectations</h2>
                <?php
                if ($enseignant) {
                    $id_enseignant = $enseignant['id_enseignant'];

                    // Récupérer les affectations de cet enseignant
                    $stmt = $pdo->prepare("SELECT * FROM affectations WHERE id_enseignant = :id_enseignant");
                    $stmt->bindParam(':id_enseignant', $id_enseignant, PDO::PARAM_INT);
                    $stmt->execute();
                    $affectations = $stmt->fetchAll(PDO::FETCH_ASSOC);

                    if ($affectations) {
                        foreach ($affectations as $affectation) {
                            // Récupérer les infos du cours correspondant
                            $stmt = $pdo->prepare("SELECT code_cours, nom_cours FROM cours WHERE id_cours = :id_cours");
                            $stmt->bindParam(':id_cours', $affectation['id_cours'], PDO::PARAM_INT);
                            $stmt->execute();
                            $cours = $stmt->fetch(PDO::FETCH_ASSOC);

                            $nom_affichage = $cours ? htmlspecialchars($cours['code_cours']) . " - " . htmlspecialchars($cours['nom_cours']) : "Cours inconnu";

                            echo "<div class='affectation'>";
                            echo "<span>" . $nom_affichage . "</span> | ";
                            echo "<span>Groupe: " . htmlspecialchars($affectation['id_groupe']) . "</span> | ";
                            echo "<span>Type: " . htmlspecialchars($affectation['type_heure']) . "</span> | ";
                            echo "<span>Heures: " . htmlspecialchars($affectation['heures_affectees']) . "</span>";
                            echo "</div>";
                        }
                    } else {
                        echo "<p>Aucune affectation.</p>";
                    }
                } else {
                    echo "<p>Aucune affectation.</p>";
                }
                ?>
            </div>

    </div>
</div>