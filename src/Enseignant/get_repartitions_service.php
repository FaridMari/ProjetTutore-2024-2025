<?php
// src/Enseignant/get_repartitions_service.php

// Active l'affichage des erreurs (pour le développement)
error_reporting(E_ALL);
ini_set('display_errors', 1);



    // Charger les dépendances nécessaires (ajustez les chemins selon votre structure)
require_once __DIR__ . '/../modele/CoursDTO.php';
require_once __DIR__ . '/../modele/RepartitionHeuresDTO.php';

// Définir le Content-Type en JSON
header('Content-Type: application/json');

try {


    // Récupérer les données JSON envoyées par le client
    $data = json_decode(file_get_contents('php://input'), true);
    if (!isset($data['course_ids']) || !is_array($data['course_ids'])) {
        echo json_encode([]);
        exit;
    }
    $course_ids = $data['course_ids'];

    // Instancier les DTO
    $repartitionDTO = new RepartitionHeuresDTO();
    $coursDTO = new CoursDTO();

    $result = [];
    foreach ($course_ids as $course_id) {
        // Récupérer la répartition pour chaque cours
        $repartitions = $repartitionDTO->findByIdCours($course_id);
        foreach ($repartitions as $r) {
            $cours = $coursDTO->findById($r->getIdCours());
            $result[] = [
                'nomCours'        => $cours ? $cours->getNomCours() : 'Inconnu',
                'semaineDebut'    => $r->getSemaineDebut(),
                'semaineFin'      => $r->getSemaineFin(),
                'nbHeuresSemaine' => $r->getNbHeuresSemaine(),
                'semestre'        => $r->getSemestre()
            ];
        }
    }
    echo json_encode($result);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}
