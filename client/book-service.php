<?php
require_once '../config/db.php';
require_once '../config/session.php';

requireClient();

$error = '';
$success = '';

// Get all agencies with their services
$agencies = $con->query("SELECT DISTINCT a.id, a.name FROM agencies a JOIN services s ON a.id = s.agency_id ORDER BY a.name");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $agency_id = intval($_POST['agency_id'] ?? 0);
    $service_id = intval($_POST['service_id'] ?? 0);
    $datetime = trim($_POST['datetime'] ?? '');

    if ($agency_id === 0 || $service_id === 0 || empty($datetime)) {
        $error = "All fields are required.";
    } else {
        // Verify service belongs to agency
        $stmt = $con->prepare("SELECT id FROM services WHERE id = ? AND agency_id = ?");
        $stmt->bind_param("ii", $service_id, $agency_id);
        $stmt->execute();
        $service_check = $stmt->get_result();
        $stmt->close();

        if ($service_check->num_rows === 0) {
            $error = "Invalid service selection.";
        } else {
            $stmt = $con->prepare("INSERT INTO reservations (agency_id, service_id, client_user_id, datetime, status) VALUES (?, ?, ?, ?, 'waiting')");
            $stmt->bind_param("iiis", $agency_id, $service_id, $_SESSION['user_id'], $datetime);

            if ($stmt->execute()) {
                $success = "Reservation booked successfully!";
            } else {
                $error = "Failed to book reservation.";
            }
            $stmt->close();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Book Service - NETTOCAR</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container">
            <a class="navbar-brand" href="../index.php">NETTOCAR</a>
            <div class="collapse navbar-collapse">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="dashboard.php">Dashboard</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="../auth/logout.php">Logout</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card shadow">
                    <div class="card-body p-5">
                        <h2 class="card-title mb-4">Book a Service</h2>

                        <?php if ($error): ?>
                            <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
                        <?php endif; ?>

                        <?php if ($success): ?>
                            <div class="alert alert-success"><?php echo htmlspecialchars($success); ?></div>
                        <?php endif; ?>

                        <form method="POST" id="bookingForm">
                            <div class="mb-3">
                                <label for="agency_id" class="form-label">Select Agency</label>
                                <select class="form-select" id="agency_id" name="agency_id" required onchange="loadServices()">
                                    <option value="">Choose an agency...</option>
                                    <?php while ($agency = $agencies->fetch_assoc()): ?>
                                        <option value="<?php echo $agency['id']; ?>"><?php echo htmlspecialchars($agency['name']); ?></option>
                                    <?php endwhile; ?>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label for="service_id" class="form-label">Select Service</label>
                                <select class="form-select" id="service_id" name="service_id" required>
                                    <option value="">Choose a service...</option>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label for="datetime" class="form-label">Date & Time</label>
                                <input type="datetime-local" class="form-control" id="datetime" name="datetime" required>
                            </div>

                            <button type="submit" class="btn btn-primary w-100">Book Reservation</button>
                        </form>

                        <a href="dashboard.php" class="btn btn-secondary w-100 mt-2">Back to Dashboard</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function loadServices() {
            const agencyId = document.getElementById('agency_id').value;
            const serviceSelect = document.getElementById('service_id');
            
            if (!agencyId) {
                serviceSelect.innerHTML = '<option value="">Choose a service...</option>';
                return;
            }

            fetch(`../api/get-services.php?agency_id=${agencyId}`)
                .then(response => response.json())
                .then(data => {
                    serviceSelect.innerHTML = '<option value="">Choose a service...</option>';
                    data.forEach(service => {
                        const option = document.createElement('option');
                        option.value = service.id;
                        option.textContent = `${service.name} - $${parseFloat(service.price).toFixed(2)}`;
                        serviceSelect.appendChild(option);
                    });
                })
                .catch(error => console.error('Error:', error));
        }
    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
