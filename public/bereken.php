<?php

// ==== DB instellingen (zelfde als je werkende test) ====
$host = "127.0.0.1";      // als dit niet werkt: "localhost"
$db   = "strooier";
$user = "root";
$pass = "ServBay.dev";

// ==== connect ====
$pdo = new PDO(
    "mysql:host=$host;dbname=$db;charset=utf8mb4",
    $user,
    $pass,
    [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]
);

// ==== TEMPERATUUR TEST (later vervangen door Weerlive) ====
$temp = -3;

// ==== berekenen ====
$totalMinutes = 0;

$roads = $pdo->query("SELECT * FROM roads")->fetchAll();

foreach ($roads as $road) {
    $stmt = $pdo->prepare("
        SELECT aantal
        FROM rules
        WHERE road_id = ?
          AND ? BETWEEN temp_min AND temp_max
        LIMIT 1
    ");
    $stmt->execute([$road['id'], $temp]);
    $rule = $stmt->fetch();

    $aantal = $rule ? (int)$rule['aantal'] : 1;

    $totalMinutes += ((int)$road['strooi_duur']) * $aantal;
}

$minutesPerTruck = 480;
$trucks = (int)ceil($totalMinutes / $minutesPerTruck);

// ==== output ====
echo "<h1>Temperatuur: {$temp} °C</h1>";
echo "<h2>Totaal werk: {$totalMinutes} minuten</h2>";
echo "<h2>Benodigde strooiwagens: {$trucks}</h2>";
