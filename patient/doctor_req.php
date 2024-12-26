<?php
session_start();
include('../connection.php'); // Database connection

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require '../PHPMailer/src/Exception.php';
require '../PHPMailer/src/PHPMailer.php';
require '../PHPMailer/src/SMTP.php';

$patient_id = isset($_GET['id']) ? intval($_GET['id']) : null;
$patient = [];

if ($patient_id) {
    $query = "SELECT pid, pemail, pname, ppassword, paddress, pnic, pdob, ptel FROM patient WHERE pid = ?";
    $stmt = $database->prepare($query);
    $stmt->bind_param("i", $patient_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $patient = $result->fetch_assoc();
    } else {
        echo "No patient found with the provided ID.";
        exit;
    }
} else {
    echo "Invalid or missing patient ID.";
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_FILES['certificate']) && $_FILES['certificate']['error'] == 0) {
        $upload_dir = '../uploads/certificates/';
        
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }

        $certificate_path = $upload_dir . basename($_FILES['certificate']['name']);
        move_uploaded_file($_FILES['certificate']['tmp_name'], $certificate_path);

        $check_query = "SELECT pid FROM doctor_req WHERE pid = ?";
        $check_stmt = $database->prepare($check_query);
        $check_stmt->bind_param("i", $patient['pid']);
        $check_stmt->execute();
        $check_stmt->store_result();

        if ($check_stmt->num_rows > 0) {
            echo "You have already submitted a request to be a doctor.";
        } else {
            $insert_query = "INSERT INTO doctor_req (pid, pemail, pname, ppassword, paddress, pnic, pdob, ptel, certificate)
                            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
            $stmt = $database->prepare($insert_query);
            $stmt->bind_param("issssssss", 
                $patient['pid'], 
                $patient['pemail'], 
                $patient['pname'], 
                $patient['ppassword'], 
                $patient['paddress'], 
                $patient['pnic'], 
                $patient['pdob'], 
                $patient['ptel'], 
                $certificate_path
            );
            $stmt->execute();

            if ($stmt->affected_rows > 0) {
                $mail = new PHPMailer(true);
                try {
                    $mail->isSMTP();
                    $mail->Host = 'smtp.gmail.com';
                    $mail->SMTPAuth = true;
                    $mail->Username = '22131383@students.liu.edu.lb';
                    $mail->Password = 'wgwqkhbvjwmclphf';
                    $mail->Port = 587;
                    $mail->SMTPSecure = 'tls';

                    $mail->setFrom('your_email@gmail.com', 'Administrator');
                    $mail->addAddress($patient['pemail']);

                    $mail->isHTML(true);
                    $mail->Subject = 'Doctor Request Submission Confirmation';
                    $mail->Body = "Dear {$patient['pname']},<br>Your request to become a doctor has been successfully submitted. Our team will review your request and notify you of the outcome.";

                    $mail->send();
                    echo "Request submitted successfully. A confirmation email has been sent.";
                    header('Location: settings.php');
                    exit;
                } catch (Exception $e) {
                    echo "Request submitted, but email notification failed: {$mail->ErrorInfo}";
                }
            } else {
                echo "Failed to submit request.";
            }
        }
    } else {
        echo "Please upload a valid certificate image.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Doctor Request</title>
    <style>
      body {
    font-family: Arial, sans-serif;
    background-color: #f4f4f9;
    background-image: url("back.jpg");
    background-size: cover;
    background-position: center;
    margin: 0;
    padding: 0;
    display: flex;
    justify-content: center;
    align-items: center;
    height: 100vh;
}

.modal {
    background-color: #fff;
    border-radius: 8px;
    padding: 2rem;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
    max-width: 500px;
    width: 90%;
    text-align: center;
}

.modal h2 {
    margin-bottom: 1.5rem;
    color: #333;
}

.modal p {
    margin: 0.5rem 0;
    color: #555;
}

form {
    margin-top: 1.5rem;
}

form label {
    display: block;
    margin-bottom: 0.5rem;
    color: #333;
    font-weight: bold;
}

form input[type="file"] {
    padding: 0.5rem;
    border: 1px solid #ddd;
    border-radius: 4px;
    width: 100%;
    margin-bottom: 1rem;
}

form input[type="file"]::-webkit-file-upload-button {
    padding: 0.4rem 1rem;
    color: #fff;
    background-color: #007bff;
    border: none;
    border-radius: 4px;
    cursor: pointer;
    font-size: 0.9rem;
}

form input[type="file"]::-webkit-file-upload-button:hover {
    background-color: #0056b3;
}

form button {
    background-color: #007bff;
    color: #fff;
    border: none;
    padding: 0.7rem;
    border-radius: 4px;
    width: 100%;
    cursor: pointer;
    font-size: 1rem;
    transition: background-color 0.3s;
}

form button:hover {
    background-color: #0056b3;
}

.modal p strong {
    color: #000;
}

/* Resize background image for responsiveness */
@media (max-width: 768px) {
    body {
        background-size: contain;
    }
}

@media (max-width: 480px) {
    .modal {
        padding: 1.5rem;
    }

    form button {
        font-size: 0.9rem;
    }
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
</head>
<body>


<header>
        <a href="../patient/index.php">
            <img src="logo.png" alt="Logo" class="logo">
        </a>
    </header>

    <div class="modal">
        <h2>View Details</h2>
        <p><strong>Name:</strong> <?php echo htmlspecialchars($patient['pname'] ?? 'N/A'); ?></p>
        <p><strong>Email:</strong> <?php echo htmlspecialchars($patient['pemail'] ?? 'N/A'); ?></p>
        <p><strong>NIC:</strong> <?php echo htmlspecialchars($patient['pnic'] ?? 'N/A'); ?></p>
        <p><strong>Telephone:</strong> <?php echo htmlspecialchars($patient['ptel'] ?? 'N/A'); ?></p>
        <p><strong>Address:</strong> <?php echo htmlspecialchars($patient['paddress'] ?? 'N/A'); ?></p>
        <p><strong>Date of Birth:</strong> <?php echo htmlspecialchars($patient['pdob'] ?? 'N/A'); ?></p>
        <form method="post" action="" enctype="multipart/form-data">
            <label for="certificate">Upload Certificate:</label>
            <input type="file" name="certificate" id="certificate" accept="image/*" required>
            <button type="submit" name="submit_request">Submit Request</button>
        </form>
    </div>
</body>
</html>
