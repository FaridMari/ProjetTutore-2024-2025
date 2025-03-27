<?php

namespace src\Action;

class GestionnaireVisualisationFichesRessources
{
    public function execute(): string
    {
        ob_start();
        ?>
        <!DOCTYPE html>
        <html lang="fr">
        <head>
            <meta charset="UTF-8">
            <title>Visualisation ressources</title>
            <link rel="stylesheet" href="src/Action/layout.css">
            <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
            <!-- CSS DataTables -->
            <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
        </head>
        <body>
        <?php
        include 'src/Gestionnaire/Navbar_Generique.html';

        include 'src/Gestionnaire/Page_VisualisationRessources.php';
        ?>
        <!-- jQuery & DataTables JS -->
        <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
        <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>

        <script>
            $(document).ready(function () {
                $('#coursTable').DataTable({
                    "pageLength": 10,
                    "language": {
                        "lengthMenu": "Afficher _MENU_ fiches par page",
                        "zeroRecords": "Aucune fiche trouvée",
                        "info": "Page _PAGE_ sur _PAGES_",
                        "infoEmpty": "Aucune fiche disponible",
                        "infoFiltered": "(filtré sur _MAX_ fiches)",
                        "search": "Recherche :",
                        "paginate": {
                            "previous": "Précédent",
                            "next": "Suivant"
                        }
                    }
                });
            });
        </script>

        </body>
        </html>
        <?php
        return ob_get_clean();
    }

}