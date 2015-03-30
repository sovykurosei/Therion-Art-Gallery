<?php

session_start();

function __autoload($class_name)
{
    require_once $class_name.'.php';
}
if(isset($_GET['logout']) && $_GET['logout'] == 1)
{
    session_destroy();
    session_start();
}

if(isset($_POST['login']) && $_POST['login'] == 1)
{

    $email = $_POST['email'];
    $password = $_POST['password'];

    $db_connection = new database_connection();
    $connection = $db_connection->get_connection();

    $statement = null;
    $statement = $connection->prepare('SELECT account_id FROM account_attributes WHERE attribute_name = ? AND attribute_value = ?');
    $statement->execute(array('email', $email));

    $account_id_array = $statement->fetch();

    $statement = null;
    $statement = $connection->prepare('SELECT attribute_name, attribute_value FROM account_attributes WHERE account_id = ?');
    $statement->execute(array($account_id_array['account_id']));
    $raw_account_info = $statement->fetchAll();

    foreach($raw_account_info as $value)
    {
        $account_info[$value['attribute_name']] = $value['attribute_value'];
    }

    $account_password = $account_info['password'];
    $account_salt = $account_info['salt'];
    $account_long_name = $account_info['long_name'];

    $hashed_password = hash('sha256', $password);
    $salted_password = $hashed_password . $account_salt;
    $hash_salt_password = hash('sha256', $salted_password);

    if($account_password == $hash_salt_password)
    {
        $_SESSION['logged_in'] = 1;
        $_SESSION['long_name'] = $account_long_name;
        $cookie_name = 'id';
        $cookie_value = 'ADD FEATURE LATER';
        setcookie($cookie_name, $cookie_value, (86400 * 30), "/");
    }
}
