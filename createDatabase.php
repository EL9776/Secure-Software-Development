<?php

// Checks that a connection can be successfully made between the server and the database.
class DBConnection {
    private $server_name = "localhost";
    private $username = "root";
    private $password = "toor1234";
    function __construct() {
        try{
            $this->conn = new mysqli($this->server_name, $this->username, $this->password);
            echo "The database has connected successfully<br>";
        }
        catch (mysqli_sql_exception $e){
            echo "Problem with DB Connection";
            exit();
        }

    }

    private function createDB() {
        $sql = "CREATE DATABASE cwDB";
        try {
            $this->conn->query($sql);
            echo "The database has been successfully created.<br>";
        }
        catch (mysqli_sql_exception $e){
            echo "DB Exists<br>";
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
            echo "Table Exists<br>";
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
            echo "Table Exists<br>";
        }

    }

    private function executeSQL($sql){
        $this->conn->select_db('cwDB');
        $result=$this->conn->query($sql);
        return $result;
    }

    function addNewUser($email,$passHash,$filePath){
        $testSql="SELECT * FROM UserDetails WHERE email='$email';";
        $result = $this->executeSQL($testSql);
        if (mysqli_fetch_assoc($result)){
            echo "Email in Use";
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
        } catch (mysqli_sql_exception $e) {
            echo "There was an issue with the DB Generation";
        }
    }
}

// userID (PK), username, passwordHASH, pathToUserFiles

// uploadID (PK), userID, fileName, typeOfFileUploaded



?>
