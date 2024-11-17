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
        <form action="appointment.php" method="POST" id="appointmentForm">
            <div class="form-content">
                <label for="name">Name:</label>
                <input type="text" id="name" name="name"   required>
            </div>

           

            <div class="form-content">
                <label for="phone">Phone:</label>
                <input type="tel" id="phone" name="phone" required>
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

            fetch('get_doctors.php?specialty_id=' + specialtyId)
                .then(response => response.json())
                .then(data => {
                    doctorSelect.innerHTML = '<option value="">Select a doctor</option>'; // Reset the dropdown
                    data.forEach(doctor => {
                        doctorSelect.innerHTML += `<option value="${doctor.docid}">${doctor.docname}</option>`;
                    });
                })
                .catch(error => {
                    console.error('Error fetching doctors:', error);
                    doctorSelect.innerHTML = '<option value="">Error loading doctors</option>';
                });
        }

        
    </script>

</body>
</html>
