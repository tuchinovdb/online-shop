<?php

$pdo = new PDO('pgsql:host=postgres_db;port=5432;dbname=mydb', 'user', 'pass');

//$pdo->exec("INSERT INTO users (name, email, password) VALUES ('Ivan', 'ivanov@mail.ru', 'qwert')");

$statement = $pdo->query("SELECT * FROM users WHERE id = 3");
$users = $statement->fetch();
echo '<pre>';
print_r($users);
echo '<pre>';