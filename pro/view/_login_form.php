<form action="" method="POST" enctype="application/x-www-form-urlencoded" >
    <div>
        <label><?= '*Name' ?></label>
        <input type="text" name="user[name]" autofocus />
    </div>

    <div>
        <label><?= '*Password' ?></label>
        <input type="password" name="user[password]" />
    </div>

    <div>
        <label><?= password_hash('admin', PASSWORD_BCRYPT) ?></label>
    </div>



    <div class="actiondiv"><input type="submit" value="Login" /></div>

</form>