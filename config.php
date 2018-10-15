<?php
//We start sessions
session_start();

/******************************************************
------------------Required Configuration---------------
Please edit the following variables so the members area
can work correctly.
******************************************************/

//We log to the DataBase. Access data hardcoded.
$heroku_svr = 'us-cdbr-iron-east-01.cleardb.net:3306'; // Server's URL
$heroku_usr = 'bcb94ff664a17f';                        // Root user.
$heroku_pwd = 'c4780c9e';                              // Password.
$heroku_sch = 'heroku_f71fa6cda1bf9f5';                // Schema.
$link = new mysqli($heroku_svr, $heroku_usr, $heroku_pwd, $heroku_sch);
if (!$link) {
    die('Could not connect: ' . mysql_error());
}

//Webmaster Email
$mail_webmaster = 'knewaz@gmail.com';

//Top site root URL
$url_root = 'https://fast-scrubland-99567.herokuapp.com/';

/******************************************************
-----------------Optional Configuration----------------
******************************************************/

//Home page file name
$url_home = 'index.php';

//Design Name
$design = 'default';
?>

<?php
function checkPassword($pwd, &$errors) {
    $errors_init = $errors;

    if (strlen($pwd) < 8) {
        $errors[] = "Password must have at least 8 characters!";
    }

    if (!preg_match("#[0-9]+#", $pwd)) {
        $errors[] = "Password must include at least one number!";
    }

    if (!preg_match("#[a-zA-Z]+#", $pwd)) {
        $errors[] = "Password must include at least one letter!";
    }

    if (!preg_match("#[a-z]+#", $pwd)) {
        $errors[] .= "Password must include at least one lowercase letter!";
    }

	if( !preg_match("#[A-Z]+#", $pwd) ) {
		$errors[] .= "Password must include at least one uppercase letter!";
	}

	if( !preg_match("#\W+#", $pwd) ) {
		$errors[] .= "Password must include at least one symbol!";
	}

    return ($errors == $errors_init);
}
?>