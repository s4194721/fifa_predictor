<?php
$host = getenv("mysql.railway.internal");
$dbname = getenv("railway");
$username = getenv("root");
$password = getenv("vLTadGddYNzcqJwotvEbjeItAvGGZjXl");
$port = getenv("3306");

try {
    $pdo = new PDO(
        "mysql:host=$host;port=$port;dbname=$dbname;charset=utf8mb4",
        $username,
        $password
    );

    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

} catch(PDOException $e) {
    die("Database connection failed: " . htmlspecialchars($e->getMessage()));
}
?>