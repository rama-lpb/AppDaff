<?php
require_once __DIR__ . '/../../vendor/autoload.php';
use Cloudinary\Cloudinary;
use Cloudinary\Configuration\Configuration;

// Config Cloudinary
$config = require __DIR__ . '/../../config/cloudinary.php';
Configuration::instance([
    'cloud' => [
        'cloud_name' => $config['cloud_name'],
        'api_key'    => $config['api_key'],
        'api_secret' => $config['api_secret'],
    ],
    'url' => ['secure' => true]
]);
$cloudinary = new Cloudinary();

// Autoriser CORS (optionnel)
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Content-Type");
header("Content-Type: application/json");

// Méthode uniquement POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(["error" => "Méthode non autorisée"]);
    exit;
}

// Vérifie que c'est bien du multipart/form-data (images)
if (!isset($_FILES['photo_recto']) || !isset($_FILES['photo_verso'])) {
    http_response_code(400);
    echo json_encode(["error" => "Images manquantes"]);
    exit;
}

// Récupération des données envoyées
$nom = $_POST['nom'] ?? null;
$prenom = $_POST['prenom'] ?? null;
$numero_cni = $_POST['numero_cni'] ?? null;
$date_naissance = $_POST['date_naissance'] ?? null;

// Validation simple
if (!$nom || !$prenom || !$numero_cni || !$date_naissance) {
    http_response_code(400);
    echo json_encode(["error" => "Champs obligatoires manquants"]);
    exit;
}

try {
    // Upload images
    $url_recto = $cloudinary->uploadApi()->upload($_FILES['photo_recto']['tmp_name'], [
        'folder' => 'cni/recto'
    ])['secure_url'];

    $url_verso = $cloudinary->uploadApi()->upload($_FILES['photo_verso']['tmp_name'], [
        'folder' => 'cni/verso'
    ])['secure_url'];

    // Insertion en base
    $pdo = new PDO("mysql:host=localhost;dbname=daf_db", "root", "");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $stmt = $pdo->prepare("
        INSERT INTO citoyens (nom, prenom, numero_cni, date_naissance, photo_recto_url, photo_verso_url)
        VALUES (?, ?, ?, ?, ?, ?)
    ");
    $stmt->execute([$nom, $prenom, $numero_cni, $date_naissance, $url_recto, $url_verso]);

    // Réponse JSON
    echo json_encode([
        "success" => true,
        "message" => "Citoyen enregistré",
        "data" => [
            "nom" => $nom,
            "prenom" => $prenom,
            "numero_cni" => $numero_cni,
            "photo_recto_url" => $url_recto,
            "photo_verso_url" => $url_verso
        ]
    ]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(["error" => "Erreur serveur", "details" => $e->getMessage()]);
}
