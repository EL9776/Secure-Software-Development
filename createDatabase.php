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
        return $this->conn = $conn;
    }
}
$database = new DBConnection();

?>
