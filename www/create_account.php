<?php

class create_account
{
    function __construct($account_info, $connection)
    {
        $email = $account_info['email'];
        $password = $account_info['password'];
        $retype_password = $account_info['password_retype'];
        $short_name = $account_info['short_name'];
        $long_name = $account_info['long_name'];
        $rank = $account_info['rank'];

        $hashed_password = hash('sha256', $password);
        $salt = hash('sha256', time());
        $salted_password = $hashed_password . $salt;
        $hash_salt_password = hash('sha256', $salted_password);

        $timestamp = time();

        $query = 'INSERT INTO accounts (timestamp) VALUES (:timestamp)';
        $statement = $connection->prepare($query);
        $statement->bindParam(':timestamp', $timestamp);
        $results = $statement->execute();

        $account_id = $connection->lastInsertId();

        $attribute_list['short_name'] = $short_name;
        $attribute_list['password'] = $hash_salt_password;
        $attribute_list['long_name'] = $long_name;
        $attribute_list['salt'] = $salt;
        $attribute_list['rank'] = $rank;
        $attribute_list['email'] = $email;
        $attribute_list['theme'] = 'canwhite';

        foreach($attribute_list as $key => $value)
        {
            if(!empty($value))
            {
                $query = 'INSERT INTO account_attributes (account_id, timestamp, attribute_name, attribute_value, active) VALUES (:account_id, :timestamp, :attribute_name, :attribute_value, :active)';
                $statement = $connection->prepare($query);
                $statement->bindParam(':account_id', $account_id);
                $statement->bindParam(':timestamp', $timestamp);
                $statement->bindParam(':attribute_name', $key);
                $statement->bindParam(':attribute_value', $value);
                $statement->bindValue(':active', 1);
                $statement->execute();
            }
        }
    }
}

?>
