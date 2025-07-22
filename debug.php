<?php
// Crée un fichier debug.php à la racine de ton projet pour tester

echo "<h2>🔍 DIAGNOSTIC FORMULAIRE</h2>";

// 1. Vérifier si on arrive sur cette page
echo "<h3>1. Cette page est-elle accessible ?</h3>";
echo "✅ Si tu vois ce message, debug.php fonctionne<br><br>";

// 2. Vérifier la méthode de requête
echo "<h3>2. Méthode de requête :</h3>";
echo "Method: " . $_SERVER['REQUEST_METHOD'] . "<br>";

// 3. Afficher toutes les données reçues
echo "<h3>3. Données POST reçues :</h3>";
if ($_POST) {
    echo "<pre>";
    print_r($_POST);
    echo "</pre>";
} else {
    echo "❌ Aucune donnée POST reçue<br>";
}

// 4. Afficher toutes les données GET
echo "<h3>4. Données GET reçues :</h3>";
if ($_GET) {
    echo "<pre>";
    print_r($_GET);
    echo "</pre>";
} else {
    echo "ℹ️ Aucune donnée GET<br>";
}

// 5. Informations sur l'URL
echo "<h3>5. Informations URL :</h3>";
echo "REQUEST_URI: " . $_SERVER['REQUEST_URI'] . "<br>";
echo "PHP_SELF: " . $_SERVER['PHP_SELF'] . "<br>";

// 6. Headers reçus
echo "<h3>6. Headers importants :</h3>";
echo "Content-Type: " . ($_SERVER['CONTENT_TYPE'] ?? 'Non défini') . "<br>";
echo "User-Agent: " . ($_SERVER['HTTP_USER_AGENT'] ?? 'Non défini') . "<br>";

// 7. Session
echo "<h3>7. Session :</h3>";
session_start();
if (isset($_SESSION['user'])) {
    echo "✅ Utilisateur connecté: ";
    print_r($_SESSION['user']);
} else {
    echo "❌ Aucun utilisateur en session<br>";
}

// 8. Erreurs PHP
echo "<h3>8. Erreurs PHP :</h3>";
$errors = error_get_last();
if ($errors) {
    echo "<pre>";
    print_r($errors);
    echo "</pre>";
} else {
    echo "✅ Aucune erreur PHP détectée<br>";
}

?>

<hr>
<h3>🧪 FORMULAIRE DE TEST</h3>
<form action="/debug.php" method="POST">
    <label>Téléphone:</label><br>
    <input type="tel" name="telephone" value="+221 77 123 456" required><br><br>
    
    <label>Solde:</label><br>
    <input type="number" name="solde" value="1000"><br><br>
    
    <button type="submit">Tester le formulaire</button>
</form>

<hr>
<h3>📝 TESTS À FAIRE</h3>
<ol>
    <li>Crée ce fichier debug.php à la racine de ton projet</li>
    <li>Va sur http://ton-site.com/debug.php</li>
    <li>Utilise le formulaire de test ci-dessus</li>
    <li>Change ton formulaire principal pour pointer vers /debug.php temporairement</li>
    <li>Analyse les résultats</li>
</ol>