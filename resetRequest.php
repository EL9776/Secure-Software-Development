<?php
include_once('createDatabase.php');
if (isset($_POST["resetRequest"])) {

    #User authentication to prevent timing attacks
    $selector = bin2hex(random_bytes(8));
    $token = random_bytes(40);


    $url = "newPassword.php?selector = " . $selector . "&validator = " . bin2hex($token);

    #session expires in 30 mins
    $expires = time() + 1800;

    $userEmail = $_POST["email"];
    $_DBconnection = new DBConnection();
    $_DBconnection->conn->select_db("cwDB");

    #Ensuring the user hasnt tried to reset their pwd already
    $sql = "DELETE FROM passwordReset WHERE passwordResetEmail=?";

    if (!$stmt=$_DBconnection->conn->prepare($sql)){
        echo "Error";
    }
    else{
        $stmt->bind_param("s",$userEmail);
        $stmt->execute();
    }
//    $stmt = mysqli_stmt_init($_DBconnection->conn);
//    if (!mysqli_stmt_prepare($stmt, $sql)) {
//        echo "Error";
//        exit();
//    }
//    else {
//        mysqli_stmt_bind_param($stmt, "s", $userEmail);
//        mysqli_stmt_execute($stmt);
//    }

    $sql2 = "INSERT INTO passwordReset (passwordResetEmail, passwordResetSelector, passwordResetToken, passwordResetExpires) VALUES (?,?,?,?);";

    if (!$stmt=$_DBconnection->conn->prepare($sql2)){
        echo "error";
    }
    else {
        $hashedToken = password_hash($token, PASSWORD_DEFAULT);
        $stmt->bind_param("ssss",$userEmail, $selector, $hashedToken, $expires);
        $stmt->execute();
        echo mysqli_stmt_error($stmt);
    }
    $stmt->close();
//    $stmt->close();
//    $stmt = mysqli_stmt_init($_DBconnection->conn);
//
//    if (!mysqli_stmt_prepare($stmt, $sql)) {
//        echo "Error";
//        exit();
//    }
//    else {
        #bcrypt method that is updating itself
//        $hashedToken = password_hash($token, PASSWORD_DEFAULT);
//        mysqli_stmt_bind_param($stmt, "ssss", $userEmail, $selector, $hashedToken, $expires);
//        mysqli_stmt_execute($stmt);
//    }
//    mysqli_stmt_close($stmt);
//    mysqli_close($_DBconnection->conn);

    #Sends the reset password email to the user
    $to = $userEmail;
    $subject = 'Reset your Password';
    $message = '<p>You have requested to reset your password. If you would like to continue please use the link below to create a new password.</p>';
    $message .= '<p> Password Reset:</br>';
    $message .= '<a href="' . $url . '">' . $url . '</a></p>';

    $headers = "From: SSD Cloud <resetpassword@ssdcloud.com>\r\n";
    $headers .= "Reply-To: resetpassword@ssdcloud.com\r\n";
    $headers .= "Content-type: text/html\r\n";

    mail($to, $subject, $message,$headers);
//    header("Location: forgotPassword.php?reset=success");
//    header("Location: index.php?reset=success");

}
else {
    header("Location: index.php");
}