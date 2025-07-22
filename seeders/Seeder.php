<?php

use Cloudinary\Cloudinary;
use Cloudinary\Configuration\Configuration;
use Dotenv\Dotenv;


require_once __DIR__ . '/../vendor/autoload.php';
$cloud = require __DIR__ . '/../app/config/cloudinary.php';
var_dump($cloud);


Configuration::instance([
    'cloud' => [
        'cloud_name' => $cloud['cloud_name'],
        'api_key'    => $cloud['api_key'],
        'api_secret' => $cloud['api_secret'],
    ],
    'url' => ['secure' => true]
]);

$cloudinary = new Cloudinary(Configuration::instance());


// 📦 Connexion base de données
$envPath = __DIR__ . '/../.env';
if (!file_exists($envPath)) {
    die("❌ Fichier .env introuvable à : $envPath\n");
}

$dotenv = Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->load();

// Vérification des variables obligatoires
$requiredVars = ['DSN', 'DB_USER', 'DB_PASSWORD'];
foreach ($requiredVars as $var) {
    if (empty($_ENV[$var])) {
        die("❌ Variable d'environnement manquante : $var\n");
    }
}

// Connexion à la base de données
try {
    echo "🔗 Tentative de connexion avec DSN : {$_ENV['DSN']}\n";
    echo "👤 Utilisateur : {$_ENV['DB_USER']}\n\n";

    $pdo = new PDO($_ENV['DSN'], $_ENV['DB_USER'], $_ENV['DB_PASSWORD']);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    echo "✅ Connexion réussie à la base de données\n\n";
} catch (PDOException $e) {
    echo "❌ Connexion échouée : " . $e->getMessage() . "\n";
     die();
}

// 👥 Données des citoyens
$citoyens = [
    [
        'nom' => 'Gueye', 'prenom' => 'Ramatoulaye', 'date_naissance' => '1995-01-02', 'lieu_naissance' => 'Dakar', 'numero_cni' => 'CNI1090', 'recto' => 'recto1.png', 'verso' => 'verso1.png'
    ]/* ,
    [
        'nom' => 'Ndour', 'prenom' => 'Moussa', 'date_naissance' => '1998-05-11', 'lieu_naissance' => 'Thiès', 'numero_cni' => 'CNI1002', 'recto' => 'recto2.png', 'verso' => 'verso2.png'
    ],
    [
        'nom' => 'Sow', 'prenom' => 'Awa', 'date_naissance' => '2003-07-25', 'lieu_naissance' => 'Kaolack', 'numero_cni' => 'CNI1003', 'recto' => 'recto3.png', 'verso' => 'verso3.png'
    ],
    [
        'nom' => 'Fall', 'prenom' => 'Cheikh', 'date_naissance' => '1990-01-15', 'lieu_naissance' => 'Saint-Louis', 'numero_cni' => 'CNI1004', 'recto' => 'recto4.png', 'verso' => 'verso4.png'
    ],
    [
        'nom' => 'Thiam', 'prenom' => 'Aminata', 'date_naissance' => '1997-03-19', 'lieu_naissance' => 'Ziguinchor', 'numero_cni' => 'CNI1005', 'recto' => 'recto5.png', 'verso' => 'verso5.png'
    ],
    [
        'nom' => 'Thiam', 'prenom' => 'Ibrahima', 'date_naissance' => '1997-09-17', 'lieu_naissance' => 'Kolda', 'numero_cni' => 'CNI1006', 'recto' => 'recto6.png', 'verso' => 'verso6.png'
    ],
    [
        'nom' => 'Camara', 'prenom' => 'Fatou', 'date_naissance' => '1993-03-10', 'lieu_naissance' => 'Fatick', 'numero_cni' => 'CNI1007', 'recto' => 'recto7.png', 'verso' => 'verso7.png'
    ],
    [
        'nom' => 'Ba', 'prenom' => 'Mamadou', 'date_naissance' => '1996-08-03', 'lieu_naissance' => 'Tambacounda', 'numero_cni' => 'CNI1008', 'recto' => 'recto8.png', 'verso' => 'verso8.png'
    ], */
];

// 📤 Upload et insertion
foreach ($citoyens as $citoyen) {
    try {
        $imagePathRecto = __DIR__ . '/images/' . $citoyen['recto'];
        $imagePathVerso = __DIR__ . '/images/' . $citoyen['verso'];

        $uploadRecto = $cloudinary->uploadApi()->upload($imagePathRecto, ['folder' => 'cni/recto']);
        $uploadVerso = $cloudinary->uploadApi()->upload($imagePathVerso, ['folder' => 'cni/verso']);

        $urlRecto = $uploadRecto['secure_url'];
        $urlVerso = $uploadVerso['secure_url'];

        $stmt = $pdo->prepare("
            INSERT INTO citoyen (nom, prenom, date_naissance, lieu_naissance, numero_cni, photorecto, photoverso)
            VALUES (:nom, :prenom, :date_naissance, :lieu_naissance, :numero_cni, :photorecto, :photoverso)
        ");

        $stmt->execute([
            'nom' => $citoyen['nom'],
            'prenom' => $citoyen['prenom'],
            'date_naissance' => $citoyen['date_naissance'],
            'lieu_naissance' => $citoyen['lieu_naissance'],
            'numero_cni' => $citoyen['numero_cni'],
            'photorecto' => $urlRecto,
            'photoverso' => $urlVerso,
        ]);

        echo "✅ {$citoyen['nom']} inséré avec succès\n";
    } catch (Exception $e) {
        echo "❌ Erreur avec {$citoyen['nom']} : " . $e->getMessage() . "\n";
    }
}
