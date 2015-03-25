<?php
/*
   Copyright 2011 Sovy Kurosei

   Licensed under the Apache License, Version 2.0 (the "License");
   you may not use this file except in compliance with the License.
   You may obtain a copy of the License at

       http://www.apache.org/licenses/LICENSE-2.0

   Unless required by applicable law or agreed to in writing, software
   distributed under the License is distributed on an "AS IS" BASIS,
   WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
   See the License for the specific language governing permissions and
   limitations under the License.
*/

session_start();

function __autoload($class_name)
{
    require_once $class_name.'.php';
}

class create_tag
{
    private $error_list;
    function __construct($db_type, $db_name, $db_hostname, $db_port, $db_username, $db_password, $db_tag_username, $db_tag_password)
    {
        if($db_type == "mysql")
        {
            $setup_connection = "mysql:host=$db_hostname;dbname=$db_name";
            $connection = new PDO($setup_connection, $db_username, $db_password);
        }

        $statement = $connection->prepare('DROP TABLE nodes, node_attributes, account_attributes, accounts, notifications, tags, entity_tags');
        $statement->execute();

        $statement = null;
        $statement = $connection->prepare("DROP USER 'tagger'@'localhost'");
        $statement->execute();

        $statement = null;
        $statement = $connection->prepare('CREATE TABLE nodes (node_id INT NOT NULL AUTO_INCREMENT, PRIMARY KEY (node_id), timestamp INT NOT NULL)');
        $statement->execute();

        $statement = null;
        $statement = $connection->prepare('CREATE TABLE node_attributes (attribute_id INT NOT NULL AUTO_INCREMENT, PRIMARY KEY (attribute_id), node_id INT NOT NULL, INDEX (node_id), account_id INT NOT NULL, timestamp INT NOT NULL, attribute_name VARCHAR(64) NOT NULL, attribute_value TEXT NOT NULL, active INT NOT NULL)');
        $statement->execute();

        $statement = null;
        $statement = $connection->prepare('CREATE TABLE account_attributes (attribute_id INT NOT NULL AUTO_INCREMENT, PRIMARY KEY (attribute_id), account_id INT NOT NULL, INDEX (account_id), timestamp INT NOT NULL, attribute_name VARCHAR(64) NOT NULL, attribute_value TEXT NOT NULL, active INT NOT NULL)');
        $statement->execute();

        $statement = null;
        $statement = $connection->prepare('CREATE TABLE accounts (account_id INT NOT NULL AUTO_INCREMENT, PRIMARY KEY (account_id), timestamp INT NOT NULL)');
        $statement->execute();

        $statement = null;
        $statement = $connection->prepare('CREATE TABLE notifications (notification_id INT NOT NULL AUTO_INCREMENT, PRIMARY KEY (notification_id), timestamp INT NOT NULL, account_id INT NOT NULL, INDEX (account_id), node_id INT NOT NULL, active INT NOT NULL)');
        $statement->execute();

        $statement = null;
        $statement = $connection->prepare('CREATE TABLE tags (tag_id INT NOT NULL AUTO_INCREMENT, PRIMARY KEY (tag_id), tag_name VARCHAR(160), UNIQUE (tag_name))');
        $statement->execute();

        $statement = null;
        $statement = $connection->prepare('CREATE TABLE entity_tags (entity_tag_id INT NOT NULL AUTO_INCREMENT, PRIMARY KEY (entity_tag_id), timestamp INT NOT NULL, entity_id INT NOT NULL, account_id INT, tag_id INT NOT NULL, type INT NOT NULL, active INT NOT NULL)');
        $statement->execute();

        $main_db_username = $db_username;
        $main_db_username_password = $db_password;

        if(isset($db_tag_username))
        {
            $working_query = "CREATE USER '$db_tag_username'@'localhost' IDENTIFIED BY '$db_tag_password'";
            $statement = $connection->prepare($working_query);
            $statement->execute();

            $working_query = "GRANT SELECT, INSERT, UPDATE ON $db_name.* to '$db_tag_username'@'localhost'";
            $statement = $connection->prepare($working_query);
            $statement->execute();

            $main_db_username = $db_tag_username;
            $main_db_username_password = $db_tag_password;
        }

        $handle = fopen('database_connection.php', 'w') or die("can't open file");
        $file_contents = '<?php

$connection_object = new database_connection();
$connection = $connection_object->get_connection();

class database_connection
{
    private $connection;
    function __construct()
    {
        $host = \'' . $db_hostname . '\';
        $username = \'' . $main_db_username . '\';
        $password = \'' . $main_db_username_password . '\';
        $database = \'' . $db_name . '\';

        $setup_connection = "mysql:host=$host;dbname=$database";
        $this->connection = new PDO($setup_connection, $username, $password);
    }

    function get_connection()
    {
        return $this->connection;
    }
}

?>';
        fwrite($handle, $file_contents);
        fclose($handle);
    }

    function get_error_list()
    {
        return $this->error_list;
    }
}

if(!isset($_POST['step']))
{
    include('themes/deeppurple/setup.tpl');
}
elseif($_POST['step'] == 1)
{
    $error_list = null;
    if(!isset($skip_db_creation))
    {
        $db_type = $_POST['db_type'];
        $db_hostname = $_POST['db_hostname'];
        $db_port = $_POST['db_port'];
        $db_name = $_POST['db_name'];
        $db_username = $_POST['db_username'];
        $db_password = $_POST['db_password'];
        $db_tag_username = $_POST['db_tag_username'];
        $db_tag_password = $_POST['db_tag_password'];
        $db_tag_password_retype = $_POST['db_tag_password_retype'];

        if($db_type == null)
        {
            $error_list[] = "Database type not selected.";
        }
        if($db_hostname == null)
        {
            $error_list[] = "Database hostname not entered.";
        }
        if($db_name == null)
        {
            $error_list[] = "Database name not entered.";
        }
        if($db_username == null)
        {
            $error_list[] = "Database username not entered.";
        }
        if($db_tag_password != $db_tag_password_retype)
        {
            $error_list[] = "TAG passwords do not match.";
        }
    }

    if(isset($error_list))
    {

    }
    else
    {
        if(!isset($skip_db_creation))
        {
            $create_tag = new create_tag($db_type, $db_name, $db_hostname, $db_port, $db_username, $db_password, $db_tag_username, $db_tag_password);
            $error_list = $create_tag->get_error_list();
        }
        if(isset($error_list))
        {

        }
        else
        {
            include('themes/deeppurple/setup2.tpl');
        }
    }
}
elseif($_POST['step'] == 2)
{
    include('database_connection.php');
    $account_info['email'] = $_POST['email'];
    $account_info['password'] = $_POST['password'];
    $account_info['password_retype'] = $_POST['password'];
    $account_info['short_name'] = $_POST['short_name'];
    $account_info['long_name'] = $_POST['long_name'];
    $account_info['rank'] = 'administrator';

    $ca_object = new create_account($account_info, $connection);

    if(empty($error_list))
    {
        include('themes/deeppurple/setup3.tpl');
    }
    else
    {
        print_r($error_list);
    }
}


echo <<<HEREDOC

<html>
<head>
</head>
<body>
<form name = "input" action = "setup.php" method = "post">
    <div class = "width">
        <div class = "legend">Database Details</div>
        <div class = "container">
            <div class = "segment">
                <div class = "left">Database type</div>
                <div class = "right"><select name = "db_type"><option value = "mysql">MySQL</option></select></div>
            </div>
            <div class = "segment">
                <div class = "left">Database Server Hostname</div>
                <div class = "right"><input type = "text" name = "db_hostname" value = "localhost" /></div>
            </div>
            <div class = "segment">
                <div class = "left">Database Server Port</div>
                <div class = "right"><input type = "text" name = "db_port" /></div>
            </div>
            <div class = "spacer"></div>
            <div class = "segment">
                <div class = "left">Database Name</div>
                <div class = "right"><input type = "text" name = "db_name" value = "tag"/></div>
            </div>
            <div class = "segment">
                <div class = "left">Database Username</div>
                <div class = "right"><input type = "text" name = "db_username" value = "root"/></div>
            </div>
            <div class = "segment">
                <div class = "left">Database Password</div>
                <div class = "right"><input type = "password" name = "db_password" value = "tag"/></div>
            </div>
        </div>
    </div>
    <div class = "width">
        <div class = "legend">TAG User</div>
        <div class = "container">
                <div class = "segment">
                <div class = "left">TAG Username</div>
                <div class = "right"><input type = "text" name = "db_tag_username" value = "tagger"/></div>
            </div>
            <div class = "segment">
                <div class = "left">TAG Password</div>
                <div class = "right"><input type = "password" name = "db_tag_password" value = "tag"/></div>
            </div>
            <div class = "segment">
                <div class = "left">Retype Password</div>
                <div class = "right"><input type = "password" name = "db_tag_password_retype" value = "tag"/></div>
            </div>
        </div>
    </div>
    <div class = "center spacer">
        <input type = "hidden" name = "step" value = "1">
        <input type = "submit" value = "Submit" />
    </div>

    <div class = "width">
        <div class = "legend">Register Admin Account</div>
        <div class = "container">
            <div class = "segment">
                <div class = "left">Email Address</div>
                <div class = "right"><input type = "text" name = "email" value = "sovy.kurosei@gmail.com"/></div>
            </div>
            <div class = "segment">
                <div class = "left">Password</div>
                <div class = "right"><input type = "password" name = "password" value = "sovy"/></div>
            </div>
            <div class = "segment">
                <div class = "left">Retype Password</div>
                <div class = "right"><input type = "password" name = "password_retype" value = "sovy"/></div>
            </div>
            <div class = "segment">
                <div class = "left">Short Name</div>
                <div class = "right"><input type = "text" name = "short_name" value = "sovy"/></div>
            </div>
            <div class = "segment">
                <div class = "left">Long Name</div>
                <div class = "right"><input type = "text" name = "long_name" value = "Sovy Kurosei"/></div>
            </div>
        </div>
    </div>








    <form name = "input" action = "index.php" method = "post">
<div class = "width">
    <div class = "legend">Success!</div>
    <div class = "container">
        <div class = "segment center">Installation and configuration is a success!</div>
    </div>
</div>
<div class = "center spacer">
    <input type = "submit" value = "Submit" />
</div>
</form>
?>
