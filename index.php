<?php
// Initialisation des données
$subjects = [
    ['name' => 'Mathématiques', 'hours' => 3, 'professor' => 'Prof A', 'semester' => 'odd'],
    ['name' => 'Physique', 'hours' => 2, 'professor' => 'Prof B', 'semester' => 'even'],
    ['name' => 'Anglais', 'hours' => 4, 'professor' => 'Prof B', 'semester' => 'odd'],
    ['name' => 'Français', 'hours' => 2, 'professor' => 'Prof A', 'semester' => 'even'],
    ['name' => 'Malagasy', 'hours' => 3, 'professor' => 'Prof A', 'semester' => 'odd'],
    // Ajouter d'autres matières
];

$classrooms = [
    ['name' => 'Salle 101', 'capacity' => 30],
    ['name' => 'Salle 102', 'capacity' => 25],
    // Ajouter d'autres salles
];

$professorsAvailability = [
    'Prof A' => ['Lundi 8h-10h', 'Lundi 10h-12h', 'Mardi 10h-12h', 'Mardi 14h-16h', 'Mercredi 10h-12h'],
    'Prof B' => ['Lundi 8h-10h', 'Mardi 8h-10h', ' Mercredi 8h-10h', 'Mercredi 10h-12h', 'Mercredi 14h-16h'],
    // Ajouter les disponibilités des autres professeurs
];

$schedule = []; // Emploi du temps vide

function generateSchedule(&$schedule, $subjects, $classrooms, $professorsAvailability, $index = 0) {
    // Si toutes les matières ont été placées, l'emploi du temps est valide
    if ($index >= count($subjects)) {
        return true;
    }
    
    $subject = $subjects[$index];

    foreach ($classrooms as $classroom) {
        foreach ($professorsAvailability[$subject['professor']] as $timeSlot) {
            if (canPlaceSubject($schedule, $subject, $classroom, $timeSlot)) {
                placeSubject($schedule, $subject, $classroom, $timeSlot);
                
                // On passe à la matière suivante
                if (generateSchedule($schedule, $subjects, $classrooms, $professorsAvailability, $index + 1)) {
                    return true;
                }
                
                // Si placer la matière suivante échoue, on retire la matière actuelle (backtrack)
                removeSubject($schedule, $subject);
            }
        }
    }

    // Si aucune salle/créneau n'a fonctionné, on doit revenir en arrière
    return false;
}

// Fonction pour vérifier si on peut placer une matière
function canPlaceSubject($schedule, $subject, $classroom, $timeSlot) {
    // Vérification de la disponibilité de la salle
    foreach ($schedule as $entry) {
        if ($entry['classroom'] === $classroom['name'] && $entry['time'] === $timeSlot) {
            return false; // La salle est déjà occupée à ce créneau
        }
    }

    // Vérification que la salle peut contenir tous les étudiants (simplifié)
    if ($classroom['capacity'] < 25) { // Par exemple, nombre d'étudiants = 25
        return false;
    }

    // Vérification de la disponibilité du professeur
    foreach ($schedule as $entry) {
        if ($entry['professor'] === $subject['professor'] && $entry['time'] === $timeSlot) {
            return false; // Le professeur est déjà occupé à ce créneau
        }
    }

    // Si toutes les conditions sont remplies, on peut placer la matière
    return true;
}

// Fonction pour placer une matière dans l'emploi du temps
function placeSubject(&$schedule, $subject, $classroom, $timeSlot) {
    $schedule[] = [
        'subject' => $subject['name'],
        'classroom' => $classroom['name'],
        'time' => $timeSlot,
        'professor' => $subject['professor'],
    ];
}

// Fonction pour retirer une matière de l'emploi du temps (backtracking)
function removeSubject(&$schedule, $subject) {
    foreach ($schedule as $key => $entry) {
        if ($entry['subject'] === $subject['name']) {
            unset($schedule[$key]);
        }
    }
}

// Exécution de l'algorithme de génération de l'emploi du temps
if (generateSchedule($schedule, $subjects, $classrooms, $professorsAvailability)) {
    echo "Emploi du temps généré avec succès :";
    print_r($schedule);
} else {
    echo "Impossible de générer l'emploi du temps avec les contraintes données.";
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Emploi du Temps</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f5f5f5;
            margin: 0;
            padding: 20px;
        }
        h1 {
            text-align: center;
            color: #333;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
            background-color: #fff;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        table th, table td {
            border: 1px solid #ddd;
            padding: 12px;
            text-align: center;
        }
        table th {
            background-color: #4CAF50;
            color: white;
        }
        table tr:nth-child(even) {
            background-color: #f2f2f2;
        }
    </style>
</head>
<body>

    <h1>Emploi du Temps</h1>

    <table>
        <thead>
            <tr>
                <th>Matière</th>
                <th>Professeur</th>
                <th>Salle</th>
                <th>Créneau Horaire</th>
            </tr>
        </thead>
        <tbody>
            <?php
            foreach ($schedule as $entry) {
                echo "<tr>";
                echo "<td>" . htmlspecialchars($entry['subject']) . "</td>";
                echo "<td>" . htmlspecialchars($entry['professor']) . "</td>";
                echo "<td>" . htmlspecialchars($entry['classroom']) . "</td>";
                echo "<td>" . htmlspecialchars($entry['time']) . "</td>";
                echo "</tr>";
            }
            ?>
        </tbody>
    </table>

</body>
</html>

