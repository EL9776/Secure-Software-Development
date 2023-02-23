<?php

// Checks that a connection can be successfully made between the server and the database.
class DBConnection {
    private $server_name = "localhost";
    private $username = "root";
    private $password = "toor1234";
    function __construct() {
        try{
            $this->conn = new mysqli($this->server_name, $this->username, $this->password);
//            echo "<h1>The database has connected successfully</h1><br>";
        }
        catch (mysqli_sql_exception $e){
            echo "<h1>Problem with DB Connection</h1>";
            exit();
        }

    }

    private function createDB() {
        $sql = "CREATE DATABASE cwDB";
        try {
            $this->conn->query($sql);
            echo "<h1>The database has been successfully created.</h1><br>";
        }
        catch (mysqli_sql_exception $e){
//            echo "<h1>DB Exists</h1><br>";
        }
    }

    private function createUserTable () {
        $sql = "CREATE TABLE UserDetails (
        userID int AUTO_INCREMENT,
        email varchar(30) NOT NULL,
        passHash varchar(200) NOT NULL,
        userFilePath varchar(150) NOT NULL,
        CONSTRAINT UserDetails_pk
        PRIMARY KEY (userID));";

        try{
            $this->executeSQL($sql);
        }
        catch (mysqli_sql_exception $e){
//            echo "<h1>Table Exists</h1><br>";
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

        try{
            $this->executeSQL($sql);
        }
        catch (mysqli_sql_exception $e){
//            echo "<h1>Table Exists</h1><br>";
        }

    }

    private function executeSQL($sql){
        $this->conn->select_db('cwDB');
        $result=$this->conn->query($sql);
        return $result;
    }

    function checkDBForAccount($email,$password){
        $this->conn-> select_db('cwDB');
        if($stmt = $this->conn -> prepare("SELECT `email`, `passHash` FROM `UserDetails` WHERE BINARY `email` = ? LIMIT 1"))
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

    function addNewUser($email,$passHash,$filePath){
        $testSql="SELECT * FROM UserDetails WHERE email='$email';";
        $result = $this->executeSQL($testSql);
        if (mysqli_fetch_assoc($result)){
            echo "<h1>Account already exists</h1>";
            exit();
        }
        else{
            $sql = "INSERT INTO UserDetails(email,passHash,userFilePath) VALUES ('$email','$passHash','$filePath');";
            $this->executeSQL($sql);
            mkdir("userFiles/".substr($email,0,strpos($email,"@")));
        }

    }

    function masterGenerate()
    {
        try {
            $this->createDB();
            $this->createUserTable();
            $this->createUploadTable();
        } catch (Exception $e) {
            echo "<h1>There was an issue with the DB Generation</h1>";
        }
    }


}

// userID (PK), username, passwordHASH, pathToUserFiles

// uploadID (PK), userID, fileName, typeOfFileUploaded



?>
