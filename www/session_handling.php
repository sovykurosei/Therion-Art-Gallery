<?php

session_start();

function __autoload($class_name)
{
    require_once $class_name.'.php';
}

if(isset($_POST['login']) && $_POST['login'] == 1)
{

    $email = $_POST['email'];
    $password = $_POST['password'];

    $db_connection = new database_connection();
    $connection = $db_connection->get_connection();

    $statement = null;
    $statement = $connection->prepare('SELECT account_id FROM account_attributes WHERE email = ?');
    $statement->execute(array($email));

    $account_id_array = $statement->fetch();

    $statement = null;
    $statement = $connection->prepare('SELECT password, salt FROM account_attributes WHERE account_id = ?');
    $statement->execute(array($account_id_array['account_id']));

    $account_password_salt_array = $statement->fetch();

    $server_password = $account_password_salt_array['password'];
    $server_salt = $account_password_salt_array['salt'];

    $hashed_password = hash('sha256', $password);
    $salted_password = $hashed_password . $server_salt;
    $hash_salt_password = hash('sha256', $salted_password);

    if($server_password == $hash_salt_password)
    {
        $_SESSION['logged_in'] = 1;
        $cookie_name = 'id';
        $cookie_value = 'ADD FEATURE LATER';
        setcookie($cookie_name, $cookie_value, (86400 * 30), "/");
    }

    echo $hash_salt_password . " vs " . $server_password;
}

if(isset($_POST['logout']) && $_POST['logout'] == 1)
{
    session_destroy();
}
