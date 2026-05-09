<?php
session_start();


if (!isset($_SESSION['user_id'])) {
header('Location: /login-form.php');
exit;
}

$pdo = new PDO('pgsql:host=postgres_db;port=5432;dbname=mydb', 'user', 'pass', [
PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
]);

$stmt = $pdo->query('SELECT * FROM products');
$products = $stmt->fetchAll();
require_once __DIR__ . '/catalog-page.php';