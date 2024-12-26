<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Patient Appointment</title>
    <link rel="stylesheet" href="style.css">
</head>

<style>
        body {
            margin: 0;
            padding: 0;
            font-family: Arial, sans-serif;
            background-image: url('back.jpg');
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            height: 100vh;
            overflow: hidden;
            color: #fff;
        }
        .appointment-form button {
    width: 100%;
    padding: 0.7rem;
    background-color: #0d7eef;
    color: #fff;
    border: none;
    border-radius: 4px;
    cursor: pointer;
    transition: background-color 0.3s;
    margin-top: 1rem;
    font-size: 1.2rem; /* Makes the text larger */
    font-family: 'Arial', sans-serif; /* Sets the font style */
    font-weight: bold; /* Makes the text bold */
    text-transform: uppercase; /* Optional: Capitalizes all letters */
}

.appointment-form button:hover {
    background-color: #0ba0e5;
}
/* Success message styling */
.success-box {
    position: fixed;
    top: 20px;
    right: -300px;
    background-color: #056dec;
    color: #fff;
    padding: 1rem 2rem;
    border-radius: 8px;
    font-size: 1rem;
    opacity: 0;
    transition: transform 0.5s ease, opacity 0.5s ease;
}

/* Show the success box */
.success-box.show {
    transform: translateX(-320px);
    opacity: 1;
}


header {
    position: absolute;
    top: 10px;
    right: 10px; /* Changed from left to right */
    z-index: 1000; /* Ensures it stays on top of other elements */
}

header a {
    display: inline-block;
    text-decoration: none;
}

.logo {
    width: 100px; /* Adjust size as needed */
    height: auto; /* Keeps aspect ratio */
    display: block;
    cursor: pointer;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.3); /* Adds shadow */
    border-radius: 8px; /* Optional: Slightly rounds the edges */
    transition: box-shadow 0.3s ease; /* Smooth shadow transition on hover */
}

.logo:hover {
    box-shadow: 0 6px 12px rgba(0, 0, 0, 0.5); /* Shadow becomes stronger on hover */
}

    </style>
<body>
<?php
// Assuming you're including the connection file here as well
include("../connection.php");

session_start();

if(isset($_SESSION["user"])) {
    if ($_SESSION["user"] == "" || $_SESSION['usertype'] != 'p') {
        header("location: ../login.php");
    } else {
        $useremail = $_SESSION["user"];
    }
} else {
    header("location: ../login.php");
}

// Get the user data from the database
$userrow = $database->query("SELECT * FROM patient WHERE pemail='$useremail'");
$userfetch = $userrow->fetch_assoc();
$username = $userfetch["pname"];
$useremail = $userfetch["pemail"];
$userphone = $userfetch["ptel"];
?>


<header>
        <a href="../patient/index.php">
            <img src="logo.png" alt="Logo" class="logo">
        </a>
    </header>

    <div class="appointment-form animate-form">
        <h2>Book an Appointment</h2>
        <form action="" method="POST" id="appointmentForm">
            <div class="form-content">
                <label for="name">Name:</label>
                <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($username); ?>" readonly>
            </div>

            <div class="form-content">
                <label for="email">Email:</label>
                <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($useremail); ?>" readonly>
            </div>

            <div class="form-content">
                <label for="phone">Phone:</label>
                <input type="tel" id="phone" name="phone" value="<?php echo htmlspecialchars($userphone); ?>" readonly>
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

<script>
    // Prevent selecting past dates
    document.addEventListener('DOMContentLoaded', () => {
        const dateInput = document.getElementById('date');
        const today = new Date().toISOString().split('T')[0];
        dateInput.setAttribute('min', today);
    });
</script>


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
