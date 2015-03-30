<?php

if(isset($_SESSION['logged_in']) && $_SESSION['logged_in'] == 1)
{
    $_SESSION['avatar_url'] = $avatar_location;

    include('themes/canwhite/header_logged.tpl');
}
else
{
    include('themes/canwhite/header_not_logged.tpl');
}

function get_notifications($account_id, $connection)
{
    $query = "SELECT * FROM notifications WHERE account_id = :account_id";
    $statement = $connection->prepare($query);
    $statement->bindParam(':account_id', $account_id);
    $statement->execute();
    $account_notifications = $statement->fetchAll();
}

?>
