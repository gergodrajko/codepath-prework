<?php
// Adatbázis kapcsolati adatok
$servername = "localhost"; // Szerver neve
$username = "root";        // Adatbázis felhasználónév
$password = "";            // Adatbázis jelszó
$dbname = "memory_game";   // Adatbázis neve

// Kapcsolódás a szerverhez (adatbázis nélkül)
$conn = new mysqli($servername, $username, $password);

// Kapcsolódási hiba ellenőrzése
if ($conn->connect_error) {
    die("Kapcsolódási hiba: " . $conn->connect_error);
}

// Adatbázis létrehozása, ha nem létezik
$sql = "CREATE DATABASE IF NOT EXISTS $dbname";
if ($conn->query($sql) === TRUE) {
    echo "Adatbázis létrehozva vagy már létezik.<br>";
} else {
    echo "Hiba az adatbázis létrehozásakor: " . $conn->error;
}

// Kapcsolódás az adatbázishoz
$conn->select_db($dbname);

// Tábla létrehozása, ha nem létezik
$sql = "CREATE TABLE IF NOT EXISTS game_results (
    id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    player_name VARCHAR(50) NOT NULL,
    score INT(6) NOT NULL,
    game_size VARCHAR(10) NOT NULL,
    date TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";

if ($conn->query($sql) === TRUE) {
    echo "Tábla létrehozva vagy már létezik.<br>";
} else {
    echo "Hiba a tábla létrehozásakor: " . $conn->error;
}

// JSON adatok fogadása
$data = json_decode(file_get_contents('php://input'), true);

// Adatok kinyerése
$player_name = $data['name'];
$score = $data['score'];
$game_size = $data['size'];
$date = $data['date'];

// SQL lekérdezés előkészítése
$stmt = $conn->prepare("INSERT INTO game_results (player_name, score, game_size, date) VALUES (?, ?, ?, ?)");
$stmt->bind_param("siss", $player_name, $score, $game_size, $date);

// Lekérdezés végrehajtása
if ($stmt->execute()) {
    echo json_encode(["status" => "success", "message" => "Eredmény sikeresen mentve!"]);
} else {
    echo json_encode(["status" => "error", "message" => "Hiba az eredmény mentésekor: " . $stmt->error]);
}

// Kapcsolat bezárása
$stmt->close();
$conn->close();
?>