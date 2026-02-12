<?php
declare(strict_types=1);

require __DIR__ . '/../vendor/autoload.php';

use Dotenv\Dotenv;

Dotenv::createImmutable(dirname(__DIR__))->safeLoad();

function envv(string $key, $default = null) {
    $v = $_ENV[$key] ?? $_SERVER[$key] ?? getenv($key);
    return ($v === false || $v === null || $v === '') ? $default : $v;
}

$pdo = new PDO(
    "mysql:host=" . envv('DB_HOST', '127.0.0.1') . ";dbname=" . envv('DB_NAME', 'strooier') . ";charset=utf8mb4",
    envv('DB_USER', 'root'),
    envv('DB_PASSWORD', ''),
    [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]
);

// Weerlive temperatuur ophalen
$apiKey  = envv('WEERLIVE_API_KEY');
$locatie = envv('WEERLIVE_LOCATIE', 'Sneek');

$temp = null;

if ($apiKey) {
    $url = "https://weerlive.nl/api/weerlive_api_v2.php?key=" . urlencode($apiKey) . "&locatie=" . urlencode($locatie);

    $json = @file_get_contents($url);

    if ($json !== false) {
        $data = json_decode($json, true);

        // meest voorkomende plek
        $t = $data['liveweer'][0]['temp'] ?? null;

        if ($t !== null && $t !== '') {
            $temp = (float) str_replace(',', '.', (string)$t);
        }
    }
}

// fallback als API faalt
if ($temp === null) {
    $temp = -3.0;
}

$roads = $pdo->query("SELECT * FROM roads ORDER BY id ASC")->fetchAll();

$totalMinutes = 0;

echo "<h1>Zoutstrooi management</h1>";
echo "<p>Temperatuur: " . htmlspecialchars((string)$temp) . " °C</p>";
echo "<p>Locatie: " . htmlspecialchars((string)$locatie) . "</p>";

echo "<hr>";
echo "<h2>Wegen</h2>";

if (!$roads) {
    echo "<p>Geen wegen gevonden. Vul eerst de database.</p>";
    exit;
}

echo "<ul>";

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

    $aantal = $rule ? (int)$rule['aantal'] : 0;
    $duur = (int)$road['strooi_duur'];
    $minuten = $duur * $aantal;

    $totalMinutes += $minuten;

    echo "<li>";
    echo "Weg: " . htmlspecialchars((string)$road['naam']) . " | ";
    echo "Duur: {$duur} min | ";
    echo "Strooien: {$aantal}x | ";
    echo "Werk: {$minuten} min";
    echo "</li>";
}

echo "</ul>";

$minutesPerTruck = 480;
$trucksNeeded = (int) ceil($totalMinutes / $minutesPerTruck);

echo "<hr>";
echo "<p>Totaal werk: {$totalMinutes} minuten</p>";
echo "<p>Strooiwagens nodig: {$trucksNeeded}</p>";
