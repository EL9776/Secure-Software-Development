<?php
include_once('createDatabase.php');
if (isset($_POST["resetRequest"])) {

    #User authentication to prevent timing attacks
    try {
        $selector = bin2hex(random_bytes(8));
    } catch (Exception $e) {
    }
    try {
        $token = random_bytes(40);
    } catch (Exception $e) {
    }

    #session expires in 30 mins
    $expires = date("U"). 1800;

    require 'createDatabase.php';

    $userEmail = $_POST["email"];
    $_DBconnection = new DBConnection();

    #Ensuring the user hasnt tried to reset their pwd already
    $sql = "DELETE FROM passwordReset WHERE passwordResetEmail=?";
    $stmt = mysqli_stmt_init($_DBconnection);
    if (!mysqli_stmt_prepare($stmt, $sql)) {
        echo "Error";
        exit();
    }   else {
        mysqli_stmt_bind_param($stmt, "s", $userEmail);
        mysqli_stmt_execute($stmt);
    }
    if (!mysqli_stmt_prepare($stmt, $sql)) {
        echo "Error";
        exit();
    }   else {
        #bcrypt method that is updating itself
        $hashedToken = password_hash($token, PASSWORD_DEFAULT);
        mysqli_stmt_bind_param($stmt, "ssss", $userEmail, $selector, $hashedToken, $expires);
        mysqli_stmt_execute($stmt);
    }
    mysqli_stmt_close($stmt);
    mysqli_close();

} else {
    header("signup.php");
}