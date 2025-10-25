<?php
require_once '../config/db.php';

header('Content-Type: application/json');

$agency_id = intval($_GET['agency_id'] ?? 0);

if ($agency_id === 0) {
    echo json_encode([]);
    exit();
}

$stmt = $con->prepare("SELECT id, name, price FROM services WHERE agency_id = ? ORDER BY name");
$stmt->bind_param("i", $agency_id);
$stmt->execute();
$result = $stmt->get_result();

$services = [];
while ($row = $result->fetch_assoc()) {
    $services[] = $row;
}

$stmt->close();

echo json_encode($services);
?>
