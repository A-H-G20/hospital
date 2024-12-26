<?php
require '../PHPMailer/src/Exception.php';
require '../PHPMailer/src/PHPMailer.php';
require '../PHPMailer/src/SMTP.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "edoc";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get POST data
$appointment_id = $_POST['appointment_id'];
$action = $_POST['action'];

if ($appointment_id && $action) {
    // Determine the new status and email message
    $status = $action === 'accept' ? 'Accepted' : 'Declined';
    $email_subject = $action === 'accept' ? 'Appointment Accepted' : 'Appointment Declined';
    $email_body = $action === 'accept'
        ? "Your appointment has been accepted. Please arrive on time."
        : "Your appointment has been declined. Please contact us for further assistance.";

    // Get the patient's email and name
    $sql = "SELECT name, email FROM appointments WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $appointment_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();

    if ($row) {
        $patient_email = $row['email'];
        $patient_name = $row['name'];

        // Update the status in the database
        $update_sql = "UPDATE appointments SET status = ? WHERE id = ?";
        $update_stmt = $conn->prepare($update_sql);
        $update_stmt->bind_param("si", $status, $appointment_id);
        $update_stmt->execute();

        // Send email
        $mail = new PHPMailer(true);
        try {
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = '22131383@students.liu.edu.lb';
            $mail->Password = 'wgwqkhbvjwmclphf';
            $mail->SMTPSecure = 'tls';
            $mail->Port = 587;

            $mail->setFrom('22131383@students.liu.edu.lb', 'Doctor');
            $mail->addAddress($patient_email);

            $mail->Subject = $email_subject;
            $mail->Body = "Hello $patient_name,\n\n$email_body";

            $mail->send();
            echo "Status updated and email sent successfully.";
        } catch (Exception $e) {
            echo "Status updated, but email could not be sent. Error: {$mail->ErrorInfo}";
        }
    } else {
        echo "Appointment not found.";
    }
} else {
    echo "Invalid request.";
}

$conn->close();
?>
