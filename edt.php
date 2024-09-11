<?php
class EmploiDuTemps {
    private $cours;
    private $professeurs;
    private $salles;
    private $creneaux;
    private $emploiDuTemps;

    public function __construct($cours, $professeurs, $salles, $creneaux) {
        $this->cours = $cours;
        $this->professeurs = $professeurs;
        $this->salles = $salles;
        $this->creneaux = $creneaux;
        $this->emploiDuTemps = [];
    }

    public function generer() {
        // Trier les cours par durée décroissante
        usort($this->cours, function($a, $b) {
            return $b['duree'] - $a['duree'];
        });

        foreach ($this->cours as $cours) {
            foreach ($this->creneaux as $creneau) {
                if ($this->estDisponible($cours, $creneau)) {
                    $this->emploiDuTemps[$creneau['jour']][$creneau['heure']] = $cours;
                    break;
                }
            }
        }
    }

    private function estDisponible($cours, $creneau) {
        // Vérifier si le professeur est disponible
        if (!isset($this->professeurs[$cours['professeur']][$creneau['jour']][$creneau['heure']]) ||
            $this->professeurs[$cours['professeur']][$creneau['jour']][$creneau['heure']] !== 'disponible') {
            return false;
        }

        // Vérifier si la salle est disponible et adaptée
        if (!isset($this->salles[$cours['salle']][$creneau['jour']][$creneau['heure']]) ||
            $this->salles[$cours['salle']][$creneau['jour']][$creneau['heure']] !== 'disponible') {
            return false;
        }

        // Ajouter d'autres vérifications si nécessaire (conflits d'étudiants, etc.)
        return true;
    }

    public function afficher() {
        echo "<table>";
        echo "<tr><th>Heure</th>";
        foreach ($this->creneaux as $creneau) {
            echo "<th>" . $creneau['jour'] . " " . $creneau['heure'] . "</th>";
        }
        echo "</tr>";
    
        foreach ($this->emploiDuTemps as $jour => $creneauxJour) {
            echo "<tr><td>" . $jour . "</td>";
            foreach ($creneauxJour as $heure => $cours) {
                echo "<td>";
                if ($cours) {
                    echo $cours['nom'];
                }
                echo "</td>";
            }
            echo "</tr>";
        }
        echo "</table>";
    }
}


$cours = [
    [
        'id' => 1,
        'nom' => 'Mathématiques',
        'duree' => 90, // en minutes
        'professeur' => 'Dupont',
        'salle' => 'A101',
        'etudiants' => [1, 2, 3, 4] // liste des étudiants inscrits
    ],[
        'id' => 2,
        'nom' => 'Physique',
        'duree' => 50, // en minutes
        'professeur' => 'Dupont',
        'salle' => 'A101',
        'etudiants' => [1, 2, 3, 4] // liste des étudiants inscrits
    ],
];
$professeurs = [
    'Dupont'=>[
        'lundi'=>['9h-10h'=>'disponible','10h-11h'=>'indisponible','14h-16h'=>'disponible'],
    ],
];
$salles = [
    'A101' => [
        'capacite' => 30,
        'equipement' => ['tableau', 'ordinateur', 'projecteur']
    ],
];
$creneaux = [
    ['jour'=>'lundi','heure'=>[
        '9h-10h',
        '10h-11h',
        '14h-16h',
        '16h-18h',
    ],
],
    ['jour'=>'mardi','heure'=>[
        '9h-10h',
        '10h-11h',
        '14h-16h',
        '16h-18h',
    ],
],
    ['jour'=>'mercredi','heure'=>[
    '9h-10h',
    '10h-11h',
    '14h-16h',
    '16h-18h',
],
],
    ['jour'=>'jeudi','heure'=>[
        '9h-10h',
        '10h-11h',
        '14h-16h',
        '16h-18h',
    ],
],
    ['jour'=>'vendredi','heure'=>[
    '9h-10h',
    '10h-11h',
    '14h-16h',
    '16h-18h',
],
],

    // ...
];

$edt = new EmploiDuTemps($cours, $professeurs, $salles, $creneaux);
$edt->generer();
$edt->afficher();