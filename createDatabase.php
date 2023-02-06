<?php

// Checks that a connection can be successfully made between the server and the database.
class DBConnection {
    private $server_name = "localhost";
    private $username = "username";
    private $password = "password";
    function __construct() {
        $conn = new mysqli($this->server_name, $this->username, $this->password);
        if($conn -> connect_error) {
            die("The connection has failed: " . $conn -> connect_error);
        }
        echo "The database has connected successfully";
        return $this -> $conn;
    }

    function createDB($conn) {
        $sql = "CREATE DATABASE cwDB";
        if ($conn -> query($sql) === TRUE)
        {
            echo "The database has been successfully created.";
        }
        else {
            echo "There was an error creating the database: " . $conn -> error;
        }
    }

    private function createUserTable ($conn) {
        $sql = "CREATE TABLE UserDetails (
        userID int AUTO_INCREMENT,
        username varchar NOT NULL,
        passwordHash varchar NOT NULL,
        pathToUserFiles varchar NOT NULL,
        PRIMARY KEY (userID))";
        $this->checkTableCreated($conn, $sql);
    }

    private function createUploadTable ($conn) {
        $sql = "CREATE TABLE UploadDetails (
        uploadID int AUTO_INCREMENT PRIMARY KEY,
        userID int NOT NULL,
        fileName varchar NOT NULL,
        typeOfFileUploaded varchar NOT NULL,
        PRIMARY KEY (uploadID),
        FOREIGN KEY (userID) REFERENCES UserDetails(userID)
        )";
        $this->checkTableCreated($conn, $sql);
    }

    function checkTableCreated ($conn, $sql) {
        if ($conn -> query($sql) === TRUE) {
            echo "The database table was successfully created";
        }
        else {
            echo "There was an error creating the database table: " . $conn -> error;
        }
    }

}




// userID (PK), username, passwordHASH, pathToUserFiles

// uploadID (PK), userID, fileName, typeOfFileUploaded



?>
