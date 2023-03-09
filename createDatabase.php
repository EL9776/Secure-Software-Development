<?php

// Checks that a connection can be successfully made between the server and the database.
class DBConnection {
    private $server_name = "localhost";
    private $username = "root";
    private $password = "root1234";
    function __construct() {
        $this->conn = new mysqli($this->server_name, $this->username, $this->password);
        if ($this->conn->connect_error){
            echo "<h1>Problem with DB Connection</h1>";
            exit();
        }
        if ($stmt=$this->conn->prepare("CREATE DATABASE cwDB;")){
            $stmt->execute();
            $stmt->close();
        }
        $this->conn->select_db("cwDB");
    }

    private function createUserTable () {
        $sql = "CREATE TABLE UserDetails (
        userID int AUTO_INCREMENT,
        email varchar(30) NOT NULL,
        passHash varchar(200) NOT NULL,
        userFilePath varchar(150) NOT NULL,
        CONSTRAINT UserDetails_pk
        PRIMARY KEY (userID));";
        if ($stmt=$this->conn->prepare($sql)){
            $stmt->execute();
            $stmt->close();
        }
    }

    private function createUploadTable () {
        $sql = "CREATE TABLE UploadDetails (
        uploadID int AUTO_INCREMENT,
        userID int NOT NULL,
        fileName varchar(30) NOT NULL,
        typeOfFileUploaded varchar(10) NOT NULL,
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

    function checkDBForAccount($email,$password){
        $this->conn-> select_db('cwDB');
        if($stmt = $this->conn -> prepare("SELECT `email`, `passHash` FROM `UserDetails` WHERE BINARY `email` = ? LIMIT 1;"))
        {
            $stmt  -> bind_param("s", $email);
            $stmt -> execute();
            $stmt -> bind_result($uid, $uhash);
            $stmt -> store_result();

            while($stmt -> fetch())
            {
                if(password_verify($password, $uhash))
                {
                    $_SESSION['uemail'] = $email;
                    $_SESSION['uid'] = $uid;
                    $_SESSION['user']=substr($email,0,strpos($email,"@"));
                    Header("Location: cloudHomepage.php");
                }
            }
            $stmt -> close();
            echo "Wrong Email or password";
        }
}

    function addNewUser($email,$passHash,$filePath){ // Adds new user to the database
        $this->checkExists=1;
        $testSql="SELECT * FROM UserDetails WHERE email='$email';";
        $result=$this->conn->query($testSql);
        if ($result->fetch_assoc()){
            if ($result = $this->conn -> query($testSql)) {
                while ($obj = $result->fetch_object()) {
                    echo "<h1 style='color: green; text-align:center'>Account {$obj->email} already exists</h1>";
                }
                $result->free_result();
                exit();
            }
        }
        else{
            $sql = "INSERT INTO UserDetails(email,passHash,userFilePath) VALUES ('$email','$passHash','$filePath');";
            if ($stmt=$this->conn->prepare($sql)){
                $stmt->execute();
            }
            $stmt->close();
            mkdir("userFiles/".substr($email,0,strpos($email,"@")),0777,true);
        }
    }

    function masterGenerate()
    {
        try {
            $this->createUserTable();
            $this->createUploadTable();
            $this->passwordResetTable();
        } catch (Exception $e) {
            echo "<h1>There was an issue with the DB Generation</h1>";
        }
    }
    private function passwordResetTable () {
        $sql = "CREATE TABLE passwordReset (
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

        $sql = "DELETE FROM passwordReset WHERE passwordResetEmail=?;";

        if (!$stmt=$this->conn->prepare($sql)){
            echo "Error";
            exit();
        }
        else{
            $stmt->bind_param("s",$userEmail);
            $stmt->execute();
        }

        $sql2 = "INSERT INTO passwordReset (passwordResetEmail, passwordResetSelector, passwordResetToken, passwordResetExpires) VALUES (?,?,?,?);";

        if (!$stmt=$this->conn->prepare($sql2)){
            echo "error";
            exit();
        }
        else {
            $hashedToken = password_hash($token, PASSWORD_DEFAULT);
            $stmt->bind_param("ssss",$userEmail, $selector, $hashedToken, $expires);
            $stmt->execute();
        }
        $stmt->close();
    }

    function validatePassword($password,$repeat){
        // Validate password strength
        $uppercase = preg_match('@[A-Z]@', $password);
        $lowercase = preg_match('@[a-z]@', $password);
        $number    = preg_match('@[0-9]@', $password);
        $specialChars = preg_match('@[^\w]@', $password);

        if(!$uppercase || !$lowercase ||!$specialChars  || !$number  || strlen($password) < 9)
        {
            echo 'Password should be at least 9 characters in length and should include at least one upper case letter, one number, and one special character.';
            exit();
        }
        elseif ($password !== $repeat)
        {
            echo "passwords do not match";
            exit();
        }
    }

    function retreivePassRequest($selector,$validator){
        if (isset($_POST['submitNewPassword'])){

            $sql="SELECT UserDetails.`email`,`passwordResetSelector`,`passwordResetToken`,`passwordResetExpires`
    FROM PasswordReset,UserDetails WHERE UserDetails.`email`=`passwordResetEmail`;";
            if ($stmt=$this->conn->prepare($sql)){
                $stmt->execute();
                $stmt->bind_result($retEmail,$selectorCheck,$tokenCheck,$expireCheck);
                $stmt->store_result();
                while ($stmt->fetch()) {
                    if (($selectorCheck == $selector) && (password_verify(hex2bin($validator),$tokenCheck)==1) && ($expireCheck > time())) {
                        $this->validatePassword($_POST['newPassword'], $_POST['repeatNewPassword']);
                        $sql = "UPDATE UserDetails SET passHash = ? WHERE `email`= ?;";

                        if ($stmtUpdate = $this->conn->prepare($sql)) {
                            $this->newPassHash = password_hash($_POST['newPassword'], PASSWORD_DEFAULT);
                            $stmtUpdate->bind_param("ss",$this->newPassHash, $retEmail);
                            $stmtUpdate->execute();
                            $stmtUpdate->close();
                            header("Location: index.php");
                        }
                    } else {
                        echo "<h1>Expired Token/Invalid Reset Link, Please Try again.</h1>";
                        exit();
                    }
                }
                $stmt->close();
            }
        }
    }
}

// userID (PK), username, passwordHASH, pathToUserFiles

// uploadID (PK), userID, fileName, typeOfFileUploaded



?>
