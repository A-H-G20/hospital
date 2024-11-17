<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Patient Appointment</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="appointment-form animate-form">
        <h2>Book an Appointment</h2>
        <form action="" method="POST" id="appointmentForm">
            <div class="form-content">
                <label for="name">Name:</label>
                <input type="text" id="name" name="name" required>
            </div>

            <div class="form-content">
                <label for="email">Email:</label>
                <input type="email" id="email" name="email" required>
            </div>

            <div class="form-content">
                <label for="phone">Phone:</label>
                <input type="tel" id="phone" name="phone" required>
            </div>

            <div class="form-content">
                <label for="age">Age:</label>
                <input type="number" id="age" name="age" required min="1">
            </div>

            <div class="form-content">
                <label for="gender">Gender:</label>
                <select id="gender" name="gender" required>
                    <option value="Male">Male</option>
                    <option value="Female">Female</option>
                    <option value="Other">Other</option>
                </select>
            </div>

            <div class="form-content">
                <label for="disease">Disease Type:</label>
                <select id="disease" name="disease_type" required onchange="fetchDoctors(this.value)">
                    <option value="">Select Disease Type</option>
                    <?php
                    include 'connection.php';
                    $result = $conn->query("SELECT id, sname FROM specialties");
                    while ($row = $result->fetch_assoc()) {
                        echo "<option value='{$row['id']}'>{$row['sname']}</option>";
                    }
                    ?>
                </select>
            </div>

            <div class="form-content">
                <label for="doctor">Choose Doctor:</label>
                <select id="doctor" name="doctor" required>
                    <option value="">Select a doctor</option>
                </select>
            </div>

            <div class="form-content">
                <label for="date">Appointment Date:</label>
                <input type="date" id="date" name="date" required>
            </div>

            <div class="form-content">
                <label for="time">Appointment Time:</label>
                <input type="time" id="time" name="time" required>
            </div>

            <button type="submit">Submit</button>
        </form>
    </div>
    <div class="success-box" id="successBox">
        <p>Appointment booked successfully!</p>
    </div>
    <script src="script.js"></script>
    <script>
        function fetchDoctors(specialtyId) {
            const doctorSelect = document.getElementById('doctor');
            doctorSelect.innerHTML = '<option value="">Loading...</option>'; // Show loading message
            
            fetch(`get_doctors.php?specialty_id=${specialtyId}`)
                .then(response => response.json())
                .then(data => {
                    doctorSelect.innerHTML = '<option value="">Select a doctor</option>'; // Reset the dropdown
                    data.forEach(doctor => {
                        doctorSelect.innerHTML += `<option value="${doctor.docid}">${doctor.docname}</option>`;
                    });
                })
                .catch(error => {
                    console.error('Error fetching doctors:', error);
                    doctorSelect.innerHTML = '<option value="">Error loading doctors</option>'; // Error message
                });
        }
    </script>
</body>
</html>
<?php
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    include 'connection.php';

    $name = $_POST['name'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $disease_type = $_POST['disease_type'];
    $doctor = $_POST['doctor'];
    $date = $_POST['date'];
    $time = $_POST['time'];

    // Prepare the SQL statement
    $stmt = $conn->prepare("INSERT INTO appointments (name, email, phone, disease_type, doctor, date, time) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("sssssss", $name, $email, $phone, $disease_type, $doctor, $date, $time);
    
    if ($stmt->execute()) {
        echo "success";
    } else {
        echo "error";
    }

    $stmt->close();
    $conn->close();
}
?>
