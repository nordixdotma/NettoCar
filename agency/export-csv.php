<?php
require_once '../config/db.php';
require_once '../config/session.php';

requireAgency();

// Get agency
$stmt = $con->prepare("SELECT a.*, p.csv_export_enabled FROM agencies a JOIN packs p ON a.pack_id = p.id WHERE a.owner_user_id = ?");
$stmt->bind_param("i", $_SESSION['user_id']);
$stmt->execute();
$agency = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$agency) {
    header("Location: create-agency.php");
    exit();
}

// Check if CSV export is enabled for this pack
if (!$agency['csv_export_enabled']) {
    header("Location: reservations.php?error=CSV export is not available with your current plan");
    exit();
}

$agency_id = $agency['id'];
$period = $_GET['period'] ?? 'all';

// Build query based on period
$where = "WHERE r.agency_id = ?";
$params = [$agency_id];
$types = "i";

if ($period === 'today') {
    $where .= " AND DATE(r.datetime) = CURDATE()";
} elseif ($period === 'week') {
    $where .= " AND WEEK(r.datetime) = WEEK(CURDATE())";
} elseif ($period === 'month') {
    $where .= " AND MONTH(r.datetime) = MONTH(CURDATE()) AND YEAR(r.datetime) = YEAR(CURDATE())";
}

// Get reservations
$query = "SELECT r.id, u.name as client_name, u.email, s.name as service_name, s.price, r.datetime, r.status
          FROM reservations r
          JOIN services s ON r.service_id = s.id
          JOIN users u ON r.client_user_id = u.id
          $where
          ORDER BY r.datetime DESC";

$stmt = $con->prepare($query);
$stmt->bind_param($types, ...$params);
$stmt->execute();
$reservations = $stmt->get_result();
$stmt->close();

// Generate CSV
header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename="reservations_' . date('Y-m-d_H-i-s') . '.csv"');

$output = fopen('php://output', 'w');

// Write header
fputcsv($output, ['Reservation ID', 'Client Name', 'Email', 'Service', 'Price', 'Date & Time', 'Status']);

// Write data
while ($row = $reservations->fetch_assoc()) {
    fputcsv($output, [
        $row['id'],
        $row['client_name'],
        $row['email'],
        $row['service_name'],
        '$' . number_format($row['price'], 2),
        $row['datetime'],
        $row['status']
    ]);
}

fclose($output);
exit();
?>
