<?php

require_once __DIR__ . '/../vendor/autoload.php';

function prompt(string $message): string {
    echo $message;
    return trim(fgets(STDIN));
}

function writeEnvIfNotExists(array $config): void {
    $envPath = __DIR__ . '/../.env';
    if (!file_exists($envPath)) {
        $env = <<<ENV
DB_DRIVER={$config['driver']}
DB_HOST={$config['host']}
DB_PORT={$config['port']}
DB_NAME={$config['dbname']}
DB_USER={$config['user']}
DB_PASSWORD={$config['pass']}
ROUTE_WEB=http://localhost:8000/

dns = "{$config['driver']}:host={$config['host']}; dbname={$config['dbname']};port={$config['port']}"
ENV;
        file_put_contents($envPath, $env);
        echo ".env généré avec succès à la racine du projet.\n";
    } else {
        echo "Le fichier .env existe déjà, aucune modification.\n";
    }
}

$driver = strtolower(prompt("Quel SGBD utiliser ? (mysql / pgsql) : "));
$host = prompt("Hôte (default: 127.0.0.1) : ") ?: "127.0.0.1";
$port = prompt("Port (default: 3307 ou 5433) : ") ?: ($driver === 'pgsql' ? "5433" : "3307");
$user = prompt("Utilisateur (default: root) : ") ?: "root";
$pass = prompt("Mot de passe : ");
$dbName = prompt("Nom de la base à créer : ");

try {
    $initialDb = $driver === 'pgsql' ? 'postgres' : null;
    $dsn = "$driver:host=$host;port=$port" . ($initialDb ? ";dbname=$initialDb" : '');
    $pdo = new PDO($dsn, $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    if ($driver === 'mysql') {
        $pdo->exec("CREATE DATABASE IF NOT EXISTS `$dbName` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
        echo "Base MySQL `$dbName` créée avec succès.\n";
    } elseif ($driver === 'pgsql') {
        $check = $pdo->query("SELECT 1 FROM pg_database WHERE datname = '$dbName'")->fetch();
        if (!$check) {
            $pdo->exec("CREATE DATABASE \"$dbName\"");
            echo "Base PostgreSQL `$dbName` créée.\n";
        } else {
            echo "ℹ La base PostgreSQL `$dbName` existe déjà.\n";
        }
    }

    $dsn = "$driver:host=$host;port=$port;dbname=$dbName";
    $pdo = new PDO($dsn, $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    if ($driver === 'mysql') {
        $tables = [
            "CREATE TABLE IF NOT EXISTS citoyen (
                id INT AUTO_INCREMENT PRIMARY KEY,
                nom VARCHAR(100),
                prenom VARCHAR(100),
                date_naissance DATE,
                lieu_naissance VARCHAR(150),
                numero_cni VARCHAR(20) UNIQUE NOT NULL,
                photorecto TEXT,
                photoverso TEXT
            )",

            "CREATE TABLE IF NOT EXISTS journalisation (
                id INT AUTO_INCREMENT PRIMARY KEY,
                date_recherche DATE,
                heure_recherche TIME,
                localisation VARCHAR(255),
                ip VARCHAR(45),
                statut ENUM('success', 'echec'),
                id_citoyen INT,
                FOREIGN KEY (id_citoyen) REFERENCES citoyen(id)
                    ON DELETE CASCADE ON UPDATE CASCADE
            )"
        ];
    } else {
        $pdo->exec("DO $$
        BEGIN
            IF NOT EXISTS (SELECT 1 FROM pg_type WHERE typname = 'statut_type') THEN
                CREATE TYPE statut_type AS ENUM ('success', 'echec');
            END IF;
        END$$;");

        $tables = [
            "CREATE TABLE IF NOT EXISTS citoyen (
                id SERIAL PRIMARY KEY,
                nom VARCHAR(100),
                prenom VARCHAR(100),
                date_naissance DATE,
                lieu_naissance VARCHAR(150),
                numero_cni VARCHAR(20) UNIQUE NOT NULL,
                photorecto TEXT,
                photoverso TEXT
            )",

            "CREATE TABLE IF NOT EXISTS journalisation (
                id SERIAL PRIMARY KEY,
                date_recherche DATE DEFAULT CURRENT_DATE,
                heure_recherche TIME DEFAULT CURRENT_TIME,
                localisation VARCHAR(255),
                ip VARCHAR(45),
                statut statut_type,
                id_citoyen INT,
                FOREIGN KEY (id_citoyen) REFERENCES citoyen(id)
                    ON DELETE CASCADE ON UPDATE CASCADE
            )"
        ];
    }

    foreach ($tables as $sql) {
        $pdo->exec($sql);
    }

    echo "Tables `citoyen` et `journalisation` créées dans `$dbName`.\n";

    writeEnvIfNotExists([
        'driver' => $driver,
        'host' => $host,
        'port' => $port,
        'user' => $user,
        'pass' => $pass,
        'dbname' => $dbName
    ]);

} catch (Exception $e) {
    echo "Erreur : " . $e->getMessage() . "\n";
}
