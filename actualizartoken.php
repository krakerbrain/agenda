<?php
require_once __DIR__ . '/classes/DatabaseSessionManager.php';
$manager = new DatabaseSessionManager();
$conn = $manager->getDB();
$sql = "SELECT id, name, email FROM users";
$stmt = $conn->prepare($sql);
$stmt->execute();
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);

foreach ($users as $user) {
    $token = hash('sha256', $user['name'] . $user['email']);

    $updateSql = "UPDATE users SET token_sha256 = :token, updated_at = now() WHERE id = :id";
    $updateStmt = $conn->prepare($updateSql);
    $updateStmt->execute([
        ':token' => $token,
        ':id' => $user['id']
    ]);
}

echo "Tokens actualizados con éxito.";
