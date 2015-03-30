<?php

include("session_handling.php");

function __autoload($class_name)
{
    require_once $class_name.'.php';
}

if(isset($_GET['register']) && $_GET['register'] == 1)
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
echo <<<HEREDOC

<html>
<head>
<link rel="stylesheet" type="text/css" href="themes/canwhite/style.css">
</head>
<body>
<div class = "header">REGISTERED</div>
    <div class = "center outline">
        <div class = "legend">Great Success!</div>
        <div class = "segment legend-spacer">
            <div class = "text-center">You have successfully registered!</div>
        </div>
    </div>
</body>
</html>
HEREDOC;
    }
    else
    {
        /*
echo <<<HEREDOC

<html>
<head>
<link rel="stylesheet" type="text/css" href="themes/canwhite/style.css">
</head>
<body>
<div class = "header">REGISTERED... NOT!</div>
    <div class = "center outline">
        <div class = "legend">Epic Fail!</div>
        <div class = "segment legend-spacer">
            <div class = "text-center">Here is what happened:
HEREDOC;
print_r($error_list);
echo <<<HEREDOC
</div>
        </div>
    </div>
</body>
</html>
HEREDOC;
*/
    }
}
else
{

echo <<<HEREDOC

<html>
<head>
<link rel="stylesheet" type="text/css" href="themes/canwhite/style.css">
</head>
<body>

<div class = "header">REGISTRATION</div>
<form name = "input" action = "register.php?register=1" method = "post">
<div>
    <div class = "center outline">
        <div class = "legend">Account</div>
        <div class = "segment legend-spacer">
            <div class = "left">Email Address</div>
            <div class = "right"><input type = "text" name = "email" "/></div>
        </div>
        <div class = "segment">
        <div class = "left">Password</div>
            <div class = "right"><input type = "password" name = "password" /></div>
        </div>
        <div class = "segment">
            <div class = "left">Retype Password</div>
            <div class = "right"><input type = "password" name = "password_retype" /></div>
        </div>
        <div class = "segment">
            <div class = "left">Short Name</div>
            <div class = "right"><input type = "text" name = "short_name" /></div>
        </div>
        <div class = "segment">
            <div class = "left">Long Name</div>
            <div class = "right"><input type = "text" name = "long_name" /></div>
        </div>
    </div>
</div>

    <div class = "center submit-outline">
        <div class = "legend">Submit</div>
        <div class = "segment legend-spacer">
            <div class = "text-center"><input type = "submit" value = "Submit" /></div>
        </div>
    </div>
</form>

HEREDOC;
}
?>
