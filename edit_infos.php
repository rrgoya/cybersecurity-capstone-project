<!-- Alter data of a registered user. -->
<?php
include('config.php');
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<link href="<?php echo $design; ?>/style.css" rel="stylesheet" title="Style" />
		<title>Edit my personnal information</title>
	</head>
	<body>
		<div class="header">
			<a href="<?php echo $url_home; ?>"><img src="<?php echo $design; ?>/images/logo.png" alt="Members Area" /></a>
		</div>

<?php
//We check if the user is logged
if (isset($_SESSION['username'])) {
	//We check if the form has been sent
	if (isset($_POST['username'], $_POST['password'], $_POST['passverif'], $_POST['email'], $_POST['avatar'])) {
		//We remove slashes depending on the configuration
		if (get_magic_quotes_gpc()) {
			$_POST['username']  = stripslashes($_POST['username']);
			$_POST['password']  = stripslashes($_POST['password']);
			$_POST['passverif'] = stripslashes($_POST['passverif']);
			$_POST['email']	    = stripslashes($_POST['email']);
			$_POST['avatar']	= stripslashes($_POST['avatar']);
			$_POST['confirm']   = stripslashes($_POST['confirm']);
		}
		//We check if the two passwords are identical

		$errors = [];
		if ($_POST['password'] == $_POST['passverif']) {
			//We check if the choosen password is strong enough.
			if (checkPassword($_POST['password'], $errors)) {
				//We check if the email form is valid
				if (preg_match('#^(([a-z0-9!\#$%&\\\'*+/=?^_`{|}~-]+\.?)*[a-z0-9!\#$%&\\\'*+/=?^_`{|}~-]+)@(([a-z0-9-_]+\.?)*[a-z0-9-_]+)\.[a-z]{2,}$#i',$_POST['email'])) {
					//We protect the variables
					$username = mysqli_real_escape_string($link, $_POST['username']);
					$password = mysqli_real_escape_string($link, $_POST['password']);
					$email	= mysqli_real_escape_string($link, $_POST['email']);
					$avatar   = mysqli_real_escape_string($link, $_POST['avatar']);
					$confirm  = mysqli_real_escape_string($link, $_POST['confirm']);
					//We check if there is no other user using the same username
					$dn = mysqli_fetch_array(mysqli_query($link, 'select count(*) as nb from users where username="'.$username.'"'));
					//We check if the username changed and if it is available
					if ($dn['nb'] == 0 or $_POST['username'] == $_SESSION['username']) {
						$req = mysqli_query($link, 'select password,id,salt from users where username="'.$username.'"');
						$dn  = mysqli_fetch_array($req);
						$password = hash("sha512", $dn['salt'].$password); // Hash password with the salt to update database.
						$oldpassw = hash("sha512", $dn['salt'].$confirm);  // Hash confirm with the salt to match database.
						//We edit the user informations
						if ($dn['password'] == $oldpassw) {
							if(mysqli_query($link, 'update users set username="'.$username.'", password="'.$password.'", email="'.$email.'", avatar="'.$avatar.'" where id="'.mysqli_real_escape_string($link, $_SESSION['userid']).'"')) { 
								//We dont display the form
								$form = false;
								//We delete the old sessions so the user need to log again
								unset($_SESSION['username'], $_SESSION['userid']);
?>
		<div class="message">Your informations have successfuly been updated. You need to log again.<br />
		<a href="connexion.php">Log in</a></div>
<?php
							}
							else {
								//Otherwise, we say that an error occured
								$form	= true;
								$message = 'An error occurred while updating your informations.';
							}
						}
						else {
							//Otherwise, we say the password is incorrect.
							$form	= true;
							$message = 'The username or password is incorrect.';
						}
					}
					else {
						//Otherwise, we say the username is not available
						$form	= true;
						$message = 'The username you want to use is not available, please choose another one.';
					}
				}
				else {
					//Otherwise, we say the email is not valid
					$form	= true;
					$message = 'The email you entered is not valid.';
				}
			}
			else {
				//Otherwise, we say the password is too weak
				$form	= true;
				$message = '';
				foreach ($errors as $item) $message = $message.$item."<BR>";
			}
		}
		else
		{
			//Otherwise, we say the passwords are not identical
			$form	 = true;
			$message = 'The passwords you entered are not identical.';
		}
	}
	else $form = true;

	if ($form) {
		//We display a message if necessary
		if(isset($message)) echo '<strong>'.$message.'</strong>';

		//If the form has already been sent, we display the same values
		if (isset($_POST['username'],$_POST['password'],$_POST['email'])) {
			$username  = htmlentities($_POST['username'], ENT_QUOTES, 'UTF-8');
			$password  = '';
			$passverif = '';
			$email	   = htmlentities($_POST['email'], ENT_QUOTES, 'UTF-8');
			$avatar	   = htmlentities($_POST['avatar'], ENT_QUOTES, 'UTF-8');
		}
		else {
			//otherwise, we display the values of the database
			$dnn	   = mysqli_fetch_array(mysqli_query($link, 'select username,password,email,avatar from users where username="'.$_SESSION['username'].'"'));
			$username  = htmlentities($dnn['username'], ENT_QUOTES, 'UTF-8');
			$password  = '';
			$passverif = '';
			$email	   = htmlentities($dnn['email'], ENT_QUOTES, 'UTF-8');
			$avatar	   = htmlentities($dnn['avatar'], ENT_QUOTES, 'UTF-8');
		}
		//We display the form
?>
		<div class="content">
			<form action="edit_infos.php" method="post">
				You can edit your information:<br />
				<div class="center">
					<label for="username">Username</label><input type="text" name="username" id="username" value="<?php echo $username; ?>" readonly/><br />
					<label for="password">New Password<span class="small">(8 characters min.)</span></label><input type="password" name="password" id="password" value="" /><br />
					<label for="passverif">New Password<span class="small">(verification)</span></label><input type="password" name="passverif" id="passverif" value="" /><br />
					<label for="email">Email</label><input type="text" name="email" id="email" value="<?php echo $email; ?>" /><br />
					<label for="avatar">Avatar<span class="small">(optional)</span></label><input type="text" name="avatar" id="avatar" value="<?php echo $avatar; ?>" /><br />
					<label for="confirm">Old Password<span class="small"></span></label><input type="password" name="confirm" id="confirm" value="" /><br />
					<input type="submit" value="Send" />
				</div>
			</form>
		</div>

<?php
	}
}
else {
?>
		<div class="message">To access this page, you must login.<br />
		<a href="connexion.php">Log in</a></div>

<?php
}
?>
		<div class="foot"><a href="<?php echo $url_home; ?>">Go Home</a></div>
	</body>
</html>
