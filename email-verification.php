<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="image/logo.png" rel="icon" />
    <title>Verification Code</title>
    <link rel="stylesheet" href="css/verify.css">
</head>
<body style="background: url('R.jpg') no-repeat center center fixed; background-size: cover;">
    <div class="container">
        <img src="img/logo.png" alt="Logo" onclick="window.location.href='';">
        <form method="POST">
            <input type="hidden" name="email" value="<?php echo isset($_GET['email']) ? htmlspecialchars($_GET['email']) : ''; ?>">
            <input type="text" name="verification_code" placeholder="Enter verification code" required />
            <input type="submit" name="verify_email" value="Verify Email">
        </form>
    </div>

    <?php
    include('connection.php');
    if (isset($_POST["verify_email"])) {
        $email = $_POST["email"];
        $verification_code = $_POST["verification_code"];
        $verified = 1; // Assuming you want to set verified as 1
        $sql = "UPDATE patient SET email_verified_at = NOW(), verification_code = NULL, verified = '" . $verified . "' WHERE pemail = '" . $email . "' AND verification_code = '" . $verification_code . "'";
        $result  = mysqli_query($database, $sql);
        if (mysqli_affected_rows($database) == 0) {
            die("Verification code failed.");
        }
        header("Location: login.php");
        exit();
    }
    ?>
</body>
</html>
