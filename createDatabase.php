<?php

class DBConnection {
    private $server_name = "localhost"; // Private variables with login creds for the DB connection.
    private $username = "root";
    private $password = "toor1234";
    function __construct() { // Constructor creates connection with the DB otherwise custom exception is spit out.
        try{
            $this->conn = new mysqli($this->server_name, $this->username, $this->password);
        }
        catch (mysqli_sql_exception $e){
            echo "<h1 style='color: green; text-align:center'>Problem with DB Credentials</h1>";
            exit();
        }
        try{
            if ($stmt=$this->conn->prepare("CREATE DATABASE cwDB;")){ // Creates Schema if not exists
                $stmt->execute();
                $stmt->close();
            }
        }
        catch (mysqli_sql_exception $e){
        }
        $this->conn->select_db("cwDB");
    }


    private function createUserTable () { // Generates user data DB table with custom exceptions for mysqli errors.
        $sql = "CREATE TABLE IF NOT EXISTS `UserDetails` (
        userID int AUTO_INCREMENT,
        email varchar(80) NOT NULL,
        passHash varchar(256) NOT NULL,
        userFilePath varchar(150) NOT NULL,
        CONSTRAINT UserDetails_pk
        PRIMARY KEY (userID));";
        if ($stmt=$this->conn->prepare($sql)){
            $stmt->execute();
            $stmt->close();
        }
    }

    private function createUploadTable () { // Generates file upload audit log table with custom mysqli exceptions for error handling.
        $sql = "CREATE TABLE IF NOT EXISTS `UploadDetails` (
        uploadID int AUTO_INCREMENT,
        userID int NOT NULL,
        fileName varchar(40) NOT NULL,
        CONSTRAINT UploadDetails_pk 
        PRIMARY KEY (uploadID),
        CONSTRAINT UploadDetails_fk
        FOREIGN KEY (userID)
        REFERENCES `UserDetails`(userID) ON DELETE CASCADE ON UPDATE CASCADE);";

        if ($stmt=$this->conn->prepare($sql)){
            $stmt->execute();
            $stmt->close();
        }
    }


    function checkDBForAccount($email,$password){ // Gets user account entry from MySQL DB generates session if valid.

        if($stmt = $this->conn -> prepare("SELECT `email`,`UserID`, `passHash` FROM `UserDetails` WHERE BINARY `email` = ? LIMIT 1"))
        {
            $stmt  -> bind_param("s", $email);
            $stmt -> execute();
            $stmt -> bind_result($uemail,$uid, $uhash); // Binds Results to 3 temp variables
            $stmt -> store_result();

            while($stmt -> fetch())
            {
                if(password_verify($password, $uhash)) // Verifys the pass hash is correct
                {
                    $_SESSION['uemail'] = $email; // User session variables generated to identify logged in user
                    $_SESSION['uid'] = $uid;
                    $_SESSION['user']=substr($email,0,strpos($email,"@"));
                    Header("Location: cloudHomepage.php"); // Sends the browser to the homepage after login
                }
            }
            $stmt -> close();
            echo "<h1 style='color: green; text-align:center'>Wrong Email or password</h1>"; // Error Message if credentials are invalid.
        }
}

    function addNewUser($email,$passHash,$filePath){ // Adds new user to the database
        $this->checkExists=1;
        $testSql="SELECT * FROM UserDetails WHERE email='$email';";
        $result=$this->conn->query($testSql);
        if ($result->fetch_assoc()){
            if ($result = $this->conn -> query($testSql)) {
                while ($obj = $result->fetch_object()) { // Checks if user already exists in the DB
                    echo "<h1 style='color: green; text-align:center'>Account {$obj->email} already exists</h1>";
                }
                $result->free_result();
                exit();
            }
        }
        else {
            $sql = "INSERT INTO UserDetails(email,passHash,userFilePath) VALUES ('$email','$passHash','$filePath');";
            if ($stmt=$this->conn->prepare($sql)){ // Adds new user if not exists
                $stmt->execute();
            }
            $stmt->close(); // Creates new user directory where the foldername is the first half of the users email.
            mkdir("userFiles/".substr($email,0,strpos($email,"@")),0777,true);
        }
    }

    function uploadedFile($filename){ // Adds to audit log of any file uploaded by the user.
        $executableSql="INSERT INTO UploadDetails(userID,filename) VALUES ('{$_SESSION['uid']}','{$filename}')";
        if ($stmt=$this->conn->prepare($executableSql)){
            $stmt->execute();

            if ($stmt->error){
                echo "<h1 style='color: green; text-align:center'>Error During Upload.</h1>";
                exit();
            }
        }
        $stmt->close(); // Close statement for efficiency, avoids having open conns to DB.
    }

    function getDynamicUserStats(){ // Counts the number of files uploaded by the user via UserID.
        $executableSql="SELECT COUNT(`filename`) FROM UploadDetails WHERE userID='{$_SESSION['uid']}';";
        if ($stmt=$this->conn->prepare($executableSql)){
            $stmt->execute();
            $stmt->bind_result($amtRows);
            $stmt->store_result();
            $stmt->fetch();
            if ($amtRows==0){
                $this->result=0;
            }
            else{
                $this->result=$amtRows;
            }
            $stmt->close();
        }
        return $this->result; // Return number of uploaded files for user ready for output.
    }

    private function passwordResetTable () { // Generates the table to verify the password reset.
        $sql = "CREATE TABLE IF NOT EXISTS `passwordReset` (
                passwordResetID INT(25) AUTO_INCREMENT NOT NULL,
                passwordResetEmail VARCHAR(80) NOT NULL,
                passwordResetSelector VARCHAR(256) NOT NULL,
                passwordResetToken VARCHAR(256) NOT NULL,
                passwordResetExpires VARCHAR(80) NOT NULL,
                CONSTRAINT passwordReset_pk
                PRIMARY KEY (passwordResetID));";
        if ($stmt=$this->conn->prepare($sql)){
            $stmt->execute();
            $stmt->close();
        }
    }

    function resetRequest($selector,$token,$expires,$userEmail){
        // Ensure the user has not already attempted to reset their password.
        $sql = "DELETE FROM passwordReset WHERE passwordResetEmail=?;";

        if (!$stmt=$this->conn->prepare($sql)){
            echo "<h1 style='color: green; text-align:center'>Error, Please Try Again!</h1>";
            exit();
        }
        else{
            $stmt->bind_param("s",$userEmail);
            $stmt->execute();
        }
        // Adds new password reset request into the DB table
        $sql2 = "INSERT INTO passwordReset (passwordResetEmail, passwordResetSelector, passwordResetToken, passwordResetExpires) VALUES (?,?,?,?);";

        if (!$stmt=$this->conn->prepare($sql2)){
            echo "<h1 style='color: green; text-align:center'>Error, Please Try Again!</h1>";
            exit();
        }
        else {
            $hashedToken = password_hash($token, PASSWORD_DEFAULT); // Hashes the token to avoid exposing sensitive token in DB table.
            $stmt->bind_param("ssss",$userEmail, $selector, $hashedToken, $expires);
            $stmt->execute();
        }
        $stmt->close();
    }

    function validatePassword($password,$repeat){
        // Validate password strength
        $uppercase = preg_match('@[A-Z]@', $password); // REGEX to string match if a password matches the requirments
        $lowercase = preg_match('@[a-z]@', $password);
        $number    = preg_match('@[0-9]@', $password);
        $specialChars = preg_match('@[^\w]@', $password);

        if(!$uppercase || !$lowercase ||!$specialChars  || !$number  || strlen($password) < 9) // Logic Check for pass strength
        {
            echo "<h1 style='color: green; text-align:center'>Password should be at least 9 characters in length and should include at least one upper case letter, one number, and one special character.</h1>";
            exit();
        }
        elseif ($password !== $repeat) // ensures password fields match.
        {
            echo "<h1 style='color: green; text-align:center'>Passwords do not match</h1>";
            exit();
        }
    }

    function retreivePassRequest($selector,$validator){ // Verifys the token request in URL matches that in the DB reset table.
        if (isset($_POST['submitNewPassword'])){
            // If new password is submitted then gets the info required for verification from the DB where email matches the users input.
            $sql="SELECT UserDetails.`email`,`passwordResetSelector`,`passwordResetToken`,`passwordResetExpires`
    FROM PasswordReset,UserDetails WHERE UserDetails.`email`=`passwordResetEmail`;";
            if ($stmt=$this->conn->prepare($sql)){
                $stmt->execute();
                $stmt->bind_result($retEmail,$selectorCheck,$tokenCheck,$expireCheck);
                $stmt->store_result();
                while ($stmt->fetch()) {
                    if (strlen($validator)%2!=0){ // hex2bin fails if token is not even, avoids unexpected error to user.
                        echo "<h1 style='color: green; text-align:center'>Expired Token/Invalid Reset Link, Please Try again.</h1>";
                        exit();
                    }
                    if (($selectorCheck == $selector) && (password_verify(hex2bin($validator),$tokenCheck)==1) && ($expireCheck > time())) {
                        $this->validatePassword($_POST['newPassword'], $_POST['repeatNewPassword']); // If token data and expiry are valid then execute the change of pass hash in the user Table.
                        $sql = "UPDATE UserDetails SET passHash = ? WHERE `email`= ?;";

                        if ($stmtUpdate = $this->conn->prepare($sql)) {
                            $this->newPassHash = password_hash($_POST['newPassword'], PASSWORD_DEFAULT);
                            $stmtUpdate->bind_param("ss",$this->newPassHash, $retEmail);
                            $stmtUpdate->execute();
                            $stmtUpdate->close();
                            header("Location: index.php?success=true"); // Returns to login with success message.
                        }
                    }
                    else {
                        echo "<h1 style='color: green; text-align:center'>Expired Token/Invalid Reset Link, Please Try again.</h1>";
                        exit();
                    }
                }
                $stmt->close();
            }
            echo "<h1 style='color: green; text-align:center'>Can't reset password, Your account dosent exist.</h1>"; // If logic block is not hit then the user doesn't have an account for the requested pass reset.
            exit();
        }
    }

    function masterGenerate() // Generates all the DB tables if not exists.
    {
        try {
            $this->createUserTable();
            $this->createUploadTable();
            $this->passwordResetTable();
        } catch (Exception $e) {
            echo "<h1 style='color: green; text-align:center'>There was an issue with the DB Generation</h1>";
        }
    }
}

// userID (PK), username, passwordHASH, pathToUserFiles

// uploadID (PK), userID, fileName

?>
