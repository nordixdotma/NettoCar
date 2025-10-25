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
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            background: linear-gradient(135deg, #ff4500 0%, #ff6b35 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            padding: 1rem 0;
        }

        .booking-wrapper {
            width: 100%;
            max-width: 450px;
            padding: 1rem;
        }

        .booking-card {
            background: white;
            border-radius: 6px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.15);
            overflow: hidden;
        }

        .booking-header {
            background: linear-gradient(135deg, #ff4500 0%, #ff6b35 100%);
            padding: 2rem 1.5rem;
            text-align: center;
            color: white;
        }

        .booking-header h1 {
            font-size: 1.75rem;
            font-weight: 700;
            margin: 0;
        }

        .booking-body {
            padding: 2rem 1.5rem;
        }

        .form-group {
            margin-bottom: 1.25rem;
        }

        .form-label {
            display: block;
            font-weight: 600;
            color: #1a1a1a;
            margin-bottom: 0.5rem;
            font-size: 0.9rem;
        }

        .form-control,
        .form-select {
            width: 100%;
            padding: 0.75rem 1rem;
            border: 1px solid #e0e0e0;
            border-radius: 4px;
            font-size: 0.95rem;
            transition: all 0.2s ease;
            font-family: inherit;
        }

        .form-control:focus,
        .form-select:focus {
            outline: none;
            border-color: #ff4500;
            box-shadow: 0 0 0 3px rgba(255, 69, 0, 0.1);
        }

        .btn {
            width: 100%;
            padding: 0.85rem 1rem;
            border: none;
            border-radius: 4px;
            font-weight: 600;
            font-size: 0.95rem;
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .btn-book {
            background: linear-gradient(135deg, #ff4500 0%, #ff6b35 100%);
            color: white;
            margin-top: 0.5rem;
        }

        .btn-book:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(255, 69, 0, 0.3);
        }

        .btn-back {
            background: #e9ecef;
            color: #1a1a1a;
            margin-top: 0.75rem;
        }

        .btn-back:hover {
            background: #dee2e6;
            color: #1a1a1a;
        }

        .alert {
            padding: 0.75rem 1rem;
            border-radius: 4px;
            margin-bottom: 1.25rem;
            border-left: 4px solid;
            font-size: 0.9rem;
        }

        .alert-danger {
            background-color: #fff5f5;
            color: #c53030;
            border-left-color: #fc8181;
        }

        .alert-success {
            background-color: #f0fdf4;
            color: #166534;
            border-left-color: #86efac;
        }
    </style>
</head>
<body>
    <div class="booking-wrapper">
        <div class="booking-card">
            <div class="booking-header">
                <h1>Book a Service</h1>
            </div>

            <div class="booking-body">
                <?php if ($error): ?>
                    <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
                <?php endif; ?>

                <?php if ($success): ?>
                    <div class="alert alert-success"><?php echo htmlspecialchars($success); ?></div>
                <?php endif; ?>

                <form method="POST" id="bookingForm">
                    <div class="form-group">
                        <label for="agency_id" class="form-label">Select Agency</label>
                        <select class="form-select" id="agency_id" name="agency_id" required onchange="loadServices()">
                            <option value="">Choose an agency...</option>
                            <?php while ($agency = $agencies->fetch_assoc()): ?>
                                <option value="<?php echo $agency['id']; ?>"><?php echo htmlspecialchars($agency['name']); ?></option>
                            <?php endwhile; ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="service_id" class="form-label">Select Service</label>
                        <select class="form-select" id="service_id" name="service_id" required>
                            <option value="">Choose a service...</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="datetime" class="form-label">Date & Time</label>
                        <input type="datetime-local" class="form-control" id="datetime" name="datetime" required>
                    </div>

                    <button type="submit" class="btn btn-book">Book Reservation</button>
                </form>

                <a href="dashboard.php" class="btn btn-back">Back to Dashboard</a>
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
