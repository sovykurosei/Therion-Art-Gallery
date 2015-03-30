<?php

class database_connection
{
    private $connection;
    function __construct()
    {
        $host = 'localhost';
        $username = 'root';
        $password = 'tag';
        $database = 'tag';

        $setup_connection = "mysql:host=$host;dbname=$database";
        $this->connection = new PDO($setup_connection, $username, $password);
    }

    function get_connection()
    {
        return $this->connection;
    }
}

?>
