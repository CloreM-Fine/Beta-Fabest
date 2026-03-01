<?php
/**
 * Reset Password Script
 * Esegui questo file per reimpostare la password admin
 * URL: https://beta.etereastudio.it/admin/reset-password.php
 */

define('ANTEAS_APP', true);
require_once __DIR__ . '/../includes/db.php';

$newPassword = 'admin123!';
$newHash = password_hash($newPassword, PASSWORD_BCRYPT);

try {
    $stmt = $pdo->prepare("UPDATE users SET password_hash = ?, is_active = 1 WHERE username = 'anteasadmin'");
    $stmt->execute([$newHash]);
    
    if ($stmt->rowCount() > 0) {
        echo "✅ Password aggiornata con successo!<br>";
        echo "Username: anteasadmin<br>";
        echo "Password: admin123!<br>";
        echo "Hash generato: " . $newHash . "<br>";
    } else {
        // Utente non esiste, crealo
        $stmt = $pdo->prepare("INSERT INTO users (username, email, password_hash, display_name, role, is_active) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->execute(['anteasadmin', 'admin@anteaslucca.local', $newHash, 'Amministratore', 'admin', 1]);
        echo "✅ Utente admin creato con successo!<br>";
        echo "Username: anteasadmin<br>";
        echo "Password: admin123!<br>";
    }
    
    echo "<br><a href='index.php'>Vai al login</a>";
    
} catch (Exception $e) {
    echo "❌ Errore: " . $e->getMessage();
}
