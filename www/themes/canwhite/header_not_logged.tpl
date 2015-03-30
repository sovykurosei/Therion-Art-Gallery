<div class = "header-outline center">
    <form name = "input" action = "<?php echo $_SERVER['REQUEST_URI'] ?>" method = "post">
    <input type = "hidden" name = "login" value = "1" />
    <div class = "new-line">
        <div class = "header-left">
            <div>Email</div>
        </div>
        <div class = "header-center">
            <input type = "text" name = "email" />
        </div>
        <div class = "header-right">
            <div><input type = "submit" value = "Submit" /></div>
        </div>
    </div>
    <div class = "new-line">
        <div class = "header-left">
            <div>Password </div>
        </div>
        <div class = "header-center">
            <input type = "password" name = "password" />
        </div>
        <div class = "header-right">
            <div>Or <a href = "register.php">Register Here</a></div>
        </div>
    </div>
    </form>
</div>
