<?php

$connection_object = new database_connection();
$connection = $connection_object->get_connection();

class database_connection
{
    private $connection;
    function __construct()
    {
        $host = 'localhost';
        $username = 'tagger';
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