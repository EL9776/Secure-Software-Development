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

}




// userID (PK), username, passwordHASH, pathToUserFiles

// uploadID (PK), userID, fileName, typeOfFileUploaded



?>
