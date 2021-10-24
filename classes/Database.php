<?php

$db_conn = mysqli_connect("localhost","root","","react_php_crud");

class Database
{

    // CHANGE THE DB INFO ACCORDING TO YOUR DATABASE
    private $db_host = 'localhost';
    private $db_name = 'hackaton2021';
    private $db_username = 'root';
    private $db_password = 'кщщещдф228';



    public function dbConnection()
    {

        try {
            $conn = new PDO('mysql:host=' . $this->db_host . ';dbname=' . $this->db_name, $this->db_username, $this->db_password);
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            return $conn;
        } catch (PDOException $e) {
            echo "Connection error " . $e->getMessage();
            exit;
        }

    }
}
$address_site = "http://vegg/";
header("Location: ".$address_site."");
