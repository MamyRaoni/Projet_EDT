<?php

// Tableau des créneaux horaires (chaque créneau de 2 heures)
$horaires_cours = [
    "07:00-09:00",
    "09:00-11:00",
    "11:00-13:00",
    "13:00-15:00",
    "15:00-17:00",
    "17:00-19:00"
];

// Disponibilités des professeurs (chaque prof a des disponibilités avec jour + plage horaire)
$disponibilites_profs = [
    'Prof1' => [
        'Lundi' => ['07:00-09:00', '09:00-11:00', '11:00-13:00'],
        'Mardi' => ['07:00-09:00', '11:00-13:00'],
        'Mercredi' => ['07:00-09:00', '11:00-13:00'],
    ],
    'Prof2' => [
        'Lundi' => ['09:00-11:00', '13:00-15:00', '17:00-19:00'],
        'Mercredi' => ['07:00-09:00', '15:00-17:00'],
    ],
    'Prof3' => [
        'Mardi' => ['07:00-09:00', '09:00-11:00', '13:00-15:00'],
        'Mercredi' => ['09:00-11:00', '13:00-15:00'],
    ],
];
// Matières enseignées par les professeurs avec volumes horaires (en heures)
$matieres_profs = [
    'Prof1' => [
        'Mathématiques' => 6,
        'Physique' => 4
    ],
    'Prof2' => [
        'Chimie' => 5,
        'Biologie' => 3
    ],
    'Prof3' => [
        'Informatique' => 7,
    ]
];
$classes = [
    'Seconde' => [
        'Mathématiques' => 8,
        'Physique' => 4,
        'Chimie' => 4
    ],
    'Première' => [
        'Biologie' => 6,
        'Informatique' => 6
    ]
];

// Les jours de la semaine
$jours_semaine = ['Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi'];
// Initialisation de l'affectation des professeurs par créneau
$emploi_du_temps = [];

// Suivi des heures enseignées par chaque professeur pour chaque matière
$heures_enseignees = [];
foreach ($matieres_profs as $prof => $matieres) {
    foreach ($matieres as $matiere => $volume) {
        $heures_enseignees[$prof][$matiere] = 0;
    }
}

$profs_horaires_occupees = [];
$classes_horaires_occupees = [];
// Boucle sur chaque classe
foreach ($classes as $classe => $matieres_classe) {
    // Initialisation de l'emploi du temps pour cette classe
    $emploi_du_temps[$classe] = [];

    // Boucle sur chaque jour de la semaine
    foreach ($jours_semaine as $jour) {
        // Boucle sur chaque créneau horaire pour ce jour
        foreach ($horaires_cours as $horaire) {
            $emploi_du_temps[$classe][$jour][$horaire] = [];
            $profs_affectes = []; // Tableau pour suivre les professeurs affectés à un créneau horaire pour cette classe

            // Boucle sur chaque matière pour cette classe
            foreach ($matieres_classe as $matiere => $volume_total) {
                if ($volume_total > 0) {
                    // Chercher un professeur disponible pour cette matière
                    foreach ($disponibilites_profs as $prof => $disponibilites_par_jour) {
                        if (isset($disponibilites_par_jour[$jour]) && in_array($horaire, $disponibilites_par_jour[$jour])) {
                            if (isset($matieres_profs[$prof][$matiere]) && $heures_enseignees[$prof][$matiere] < $matieres_profs[$prof][$matiere]) {
                                // Vérifier que le professeur n'est pas déjà affecté à un autre cours pendant ce créneau dans une autre classe
                                if (!isset($profs_horaires_occupees[$jour][$horaire][$prof])) {
                                    // Vérifier que la classe n'a pas déjà un cours pendant ce créneau
                                    if (!isset($classes_horaires_occupees[$classe][$jour][$horaire])) {
                                        // Affecter ce professeur à cette matière pour cette classe
                                        $emploi_du_temps[$classe][$jour][$horaire][] = "$prof enseigne $matiere";
                                        // Marquer ce professeur comme affecté pour ce créneau horaire pour toutes les classes
                                        $profs_horaires_occupees[$jour][$horaire][$prof] = true;
                                        // Marquer cette classe comme ayant un cours pour ce créneau
                                        $classes_horaires_occupees[$classe][$jour][$horaire] = true;
                                        // Incrémenter le volume d'heures enseignées pour cette matière
                                        $heures_enseignees[$prof][$matiere] += 2; // Chaque créneau est de 2 heures
                                        // Réduire le volume horaire requis pour la classe
                                        $classes[$classe][$matiere] -= 2;
                                        // Si le volume horaire requis pour cette matière est atteint, on passe à la matière suivante
                                        if ($classes[$classe][$matiere] <= 0) {
                                            break;
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }
    }
}

// Afficher les emplois du temps pour chaque classe
foreach ($emploi_du_temps as $classe => $jours) {
    echo "<h2>Emploi du Temps pour $classe</h2>";
    foreach ($jours as $jour => $creaneaux) {
        echo "<h3>$jour</h3>";
        foreach ($creaneaux as $horaire => $profs) {
            echo "  Créneau $horaire : " . (empty($profs) ? 'Aucun professeur disponible' : implode(", ", $profs)) . "<br>";
        }
    }
}