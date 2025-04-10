<?php
// Database connection
$conn = new PDO("mysql:host=localhost;dbname=maternal", "root", "");
$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// Retrieve email from the URL parameter
$email = $_GET['email'] ?? '';  // Getting the email from the URL parameter

// Fetch appointments for the given email
$stmt = $conn->prepare("SELECT * FROM appointments WHERE email = :email");
$stmt->bindParam(':email', $email);
$stmt->execute();
$appointments = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Appointments</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f7f6;
            margin: 0;
            padding: 0;
        }

        h2 {
            text-align: center;
            margin-top: 20px;
            color: #333;
        }

        table {
            width: 80%;
            margin: 20px auto;
            border-collapse: collapse;
            background-color: #ffffff;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        th, td {
            padding: 12px 15px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }

        th {
            background-color: #4CAF50;
            color: white;
        }

        tr:hover {
            background-color: #f1f1f1;
        }

        .status-approved {
            color: green;
            font-weight: bold;
        }

        .status-rejected {
            color: red;
            font-weight: bold;
        }

        .status-pending {
            color: orange;
            font-weight: bold;
        }

        .search-container {
            display: flex;
            justify-content: center;
            margin: 20px;
        }

        .search-container input {
            padding: 10px;
            font-size: 16px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }

        .search-container button {
            padding: 10px;
            font-size: 16px;
            margin-left: 10px;
            background-color: #4CAF50;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        .search-container button:hover {
            background-color: #45a049;
        }

        /* Modal Styles */
        .modal {
            display: none;
            position: fixed;
            z-index: 1;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgb(0,0,0);
            background-color: rgba(0,0,0,0.4);
            padding-top: 60px;
        }

        .modal-content {
            background-color: #fefefe;
            margin: 5% auto;
            padding: 20px;
            border: 1px solid #888;
            width: 80%;
        }

        .close {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
        }

        .close:hover,
        .close:focus {
            color: black;
            text-decoration: none;
            cursor: pointer;
        }

    </style>
</head>
<body>

    <h2>Your Appointments</h2>

    <!-- Search Bar to Filter Appointments -->
    <div class="search-container">
        <input type="text" id="searchInput" placeholder="Search appointments by service..." onkeyup="searchAppointments()">
        <button onclick="searchAppointments()">Search</button>
    </div>

    <!-- Appointment Table -->
    <table id="appointmentsTable">
        <tr>
            <th>Service</th>
            <th>Doctor</th>
            <th>Date</th>
            <th>Status</th>
        </tr>
        <?php foreach ($appointments as $appt): ?>
        <tr onclick="openModal(<?= htmlspecialchars($appt['id']) ?>)">
            <td><?= htmlspecialchars($appt['service']) ?></td>
            <td><?= htmlspecialchars($appt['doctor']) ?></td>
            <td><?= htmlspecialchars($appt['appointment_date']) ?></td>
            <td class="status <?= 'status-' . strtolower($appt['status']) ?>">
                <?php 
                    // Display the status based on the value in the database
                    if ($appt['status'] == 'Accepted') {
                        echo 'Accepted';
                    } elseif ($appt['status'] == 'Denied') {
                        echo 'Rejected';
                    } else {
                        echo 'Pending';
                    }
                ?>
            </td>
        </tr>
        <?php endforeach; ?>
    </table>

    <!-- Modal to View Appointment Details -->
    <div id="appointmentModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeModal()">&times;</span>
            <h3>Appointment Details</h3>
            <div id="modalContent">
                <!-- Details will be loaded here using JavaScript -->
            </div>
        </div>
    </div>

    <script>
        function searchAppointments() {
            const searchValue = document.getElementById("searchInput").value.toLowerCase();
            const table = document.getElementById("appointmentsTable");
            const rows = table.getElementsByTagName("tr");

            for (let i = 1; i < rows.length; i++) {
                const cells = rows[i].getElementsByTagName("td");
                const serviceCell = cells[0].textContent.toLowerCase();
                
                if (serviceCell.includes(searchValue)) {
                    rows[i].style.display = "";
                } else {
                    rows[i].style.display = "none";
                }
            }
        }

        // Function to open the modal and show appointment details
        function openModal(appointmentId) {
            const modal = document.getElementById("appointmentModal");
            const modalContent = document.getElementById("modalContent");

            // Fetch the appointment details using AJAX
            fetch(`get_appointment_details.php?id=${appointmentId}`)
                .then(response => response.json())
                .then(data => {
                    modalContent.innerHTML = `
                        <p><strong>Service:</strong> ${data.service}</p>
                        <p><strong>Doctor:</strong> ${data.doctor}</p>
                        <p><strong>Appointment Date:</strong> ${data.appointment_date}</p>
                        <p><strong>Status:</strong> ${data.status}</p>
                    `;
                    modal.style.display = "block";
                })
                .catch(error => console.error('Error fetching appointment details:', error));
        }

        // Function to close the modal
        function closeModal() {
            const modal = document.getElementById("appointmentModal");
            modal.style.display = "none";
        }
    </script>

</body>
</html>
