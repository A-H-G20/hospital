<?php
session_start();
require 'connection.php'; // DB connection
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'PHPMailer/src/Exception.php';
require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'];

    // Check if the email exists
    $stmt = $database->prepare("SELECT pid, pname FROM patient WHERE pemail = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $stmt->bind_result($user_id, $name);
        $stmt->fetch();

        // Generate verification code
        $verification_code = rand(100000, 999999);
        $expiry_time = date("Y-m-d H:i:s", strtotime('+1 hour'));

        // Save the code to the database
        $updateStmt = $database->prepare("UPDATE patient SET reset_code = ?, reset_code_expires = ? WHERE pid = ?");
        $updateStmt->bind_param("ssi", $verification_code, $expiry_time, $user_id);
        $updateStmt->execute();

        // Send the email
        $mail = new PHPMailer(true);
        try {
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = '22131383@students.liu.edu.lb'; // Your Gmail address
            $mail->Password = 'wgwqkhbvjwmclphf'; // Your Gmail password or App PasswordLS;
            $mail->Port = 587;

            $mail->setFrom('your_email@gmail.com', 'Administrator');
            $mail->addAddress($email);

            $mail->isHTML(true);
            $mail->Subject = 'Password Reset Verification';
            $mail->Body = "Dear $name, <br>Your verification code is: <b>$verification_code</b>.<br>This code will expire in 1 hour.";

            $mail->send();
            $_SESSION['reset_email'] = $email;
            header("Location: verify_code.php");
            exit;
        } catch (Exception $e) {
            $error = "Failed to send email. Please try again.";
        }
    } else {
        $error = "Email not found!";
    }
}
?>
<!-- HTML Form for Forgot Password -->
<!DOCTYPE html>
<html>

<head>
    <title>Forgot Password</title>
    <link rel="stylesheet" href="css/reset_password.css">
    <link href="image/logo.png" rel="icon" />
</head>

<body style="background: url('R.jpg') no-repeat center center fixed; background-size: cover;">

    <form method="POST">
        <div class="logo">
            <img src="img/logo.png" alt="Logo">
        </div>
        <h2>Forgot Password</h2>
        <input type="email" name="email" required placeholder="Enter your email">
        <button type="submit">Send Verification Code</button>
        <?php if (isset($error)) echo "<p style='color:red;'>$error</p>"; ?>
    </form>

</body>

</html>