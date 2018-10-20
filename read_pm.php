<?php
include('config.php');
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<link href="<?php echo $design; ?>/style.css" rel="stylesheet" title="Style" />
		<title>Read a PM</title>
	</head>
	<body>
		<div class="header">
			<a href="<?php echo $url_home; ?>"><img src="<?php echo $design; ?>/images/logo.png" alt="Members Area" /></a>
		</div>

<?php
//We check if the user is logged
if (isset($_SESSION['username'])) {
	//We check if the ID of the discussion is defined
	if (isset($_GET['id'])) {
		$id = intval($_GET['id']);
		//We get the title and the narrators of the discussion
		$req1 = mysqli_query($link, 'select title, user1, user2 from pm where id="'.$id.'" and id2="1"');
		$dn1  = mysqli_fetch_array($req1);
		//We check if the discussion exists
		if (mysqli_num_rows($req1) == 1) {
			//We check if the user have the right to read this discussion
			if ($dn1['user1'] == $_SESSION['userid'] or $dn1['user2'] == $_SESSION['userid']) {
				//The discussion will be placed in read messages
				if($dn1['user1'] == $_SESSION['userid']) {
					$u2 = $dn1['user2'];
					mysqli_query($link, 'update pm set user1read="yes" where id="'.$id.'" and id2="1"');
					$user_partic = 2;
				}
				else {
					$u2 = $dn1['user1'];
					mysqli_query($link, 'update pm set user2read="yes" where id="'.$id.'" and id2="1"');
					$user_partic = 1;
				}
				//We get the list of the messages
				$req2 = mysqli_query($link, 'select pm.timestamp, pm.message, users.id as userid, users.username, users.avatar, pm.user1, pm.user2, pm.tag from pm, users where pm.id="'.$id.'" and users.id=pm.user1 order by pm.id2');

				//We check if the form has been sent
				if (isset($_POST['message']) and $_POST['message'] != '') {
					$message = $_POST['message'];
					//We remove slashes depending on the configuration
					if (get_magic_quotes_gpc()) $message = stripslashes($message);

					//We protect the variables
					$message = mysqli_real_escape_string($link, nl2br(htmlentities($message, ENT_QUOTES, 'UTF-8')));					

					$cipher = "aes-128-gcm";
					$ivlen  = openssl_cipher_iv_length($cipher);
					$iv     = openssl_random_pseudo_bytes($ivlen);
					$key    = getKey($_SESSION['userid'], $u2);
					$tag    = null;
					$method = openssl_get_cipher_methods();
					if (in_array($cipher, $method)) {
						$iv = openssl_random_pseudo_bytes($ivlen);
						$ciphertext_raw = openssl_encrypt($message, $cipher, $key, $options=OPENSSL_RAW_DATA, $iv, $tag);
						$hmac = hash_hmac('sha256', $ciphertext_raw, $key, $as_binary=true);
						$ciphertext = base64_encode($iv.$hmac.$ciphertext_raw);    //store $cipher, $iv, and $tag for decryption later
						//We send the message and we change the status of the discussion to unread for the recipient
						if (mysqli_query($link, 'insert into pm (id, id2, title, user1, user2, message, timestamp, user1read, user2read, tag)values("'.$id.'", "'.(intval(mysqli_num_rows($req2))+1).'", "", "'.$_SESSION['userid'].'", "", "'.$ciphertext.'", "'.time().'", "", "", "'.$tag.'")')) {
							if (mysqli_query($link, 'update pm set user'.$user_partic.'read="yes" where id="'.$id.'" and id2="1"')) {
?>
								<div class="message">Your message has successfully been sent.<br />
								<a href="read_pm.php?id=<?php echo $id; ?>">Go to the discussion</a></div>
<?php						}
							else { ?>
								<div class="message">An error occurred while updating message status.<br />
								<a href="read_pm.php?id=<?php echo $id; ?>">Go to the discussion</a></div>
<?php						}
						}
						else { ?>
								<div class="message">An error occurred in message sending.<br />
								<a href="read_pm.php?id=<?php echo $id; ?>">Go to the discussion</a></div>
<?php					}
					}
					else { ?>
							<div class="message">Encryption method not supportted.<br />
							<a href="read_pm.php?id=<?php echo $id; ?>">Go to the discussion</a></div>
<?php				}
				}
				else {
				//We display the messages ?>
		<div class="content">
			<h1><?php echo $dn1['title']; ?></h1>
			<table class="messages_table">
				<tr>
					<th class="author">User</th>
					<th>Message</th>
				</tr>
<?php				while ($dn2 = mysqli_fetch_array($req2)) {
						$cipher = "aes-128-gcm";
						$ivlen  = openssl_cipher_iv_length($cipher);
						$key    = getKey($_SESSION['userid'], $u2);
						$method = openssl_get_cipher_methods();
						if (in_array($cipher, $method)) {
							$c    = base64_decode($dn2['message']);
							$iv   = substr($c, 0, $ivlen);
							$hmac = substr($c, $ivlen, $sha2len=32);
							$ciphertext_raw = substr($c, $ivlen+$sha2len);
							$decrypted = openssl_decrypt($ciphertext_raw, $cipher, $key, $options=OPENSSL_RAW_DATA, $iv, $dn2['tag']);
							$calcmac = hash_hmac('sha256', $ciphertext_raw, $key, $as_binary=true);
							if (!hash_equals($hmac, $calcmac)) $decrypted = "Message decryption integrity failed."; //PHP 5.6+ timing attack safe comparison
						}
						else $decrypted = "Decryption algorithm unsupported.";
?>
				<tr>
					<td class="author center">
<?php					if ($dn2['avatar'] != '') echo '<img src="'.htmlentities($dn2['avatar']).'" alt="Image Perso" style="max-width:100px;max-height:100px;" />'; ?>
				<br /><a href="profile.php?id=<?php echo $dn2['userid']; ?>"><?php echo $dn2['username']; ?></a></td>
					<td class="left"><div class="date">Sent: <?php echo date('m/d/Y H:i:s' ,$dn2['timestamp']); ?></div>
							<?php echo $decrypted; ?></td>
				</tr>
<?php					}
						//We display the reply form ?>
			</table><br />
			<h2>Reply</h2>
			<div class="center">
				<form action="read_pm.php?id=<?php echo $id; ?>" method="post">
					<label for="message" class="center">Message</label><br />
					<textarea cols="40" rows="5" name="message" id="message"></textarea><br />
					<input type="submit" value="Send" />
				</form>
			</div>
		</div>
		<?php
				}
			}
			else echo '<div class="message">You dont have the rights to access this page.</div>';
		}
		else echo '<div class="message">This discussion does not exists.</div>';
	}
	else echo '<div class="message">The discussion ID is not defined.</div>';
}
else echo '<div class="message">You must be logged to access this page.</div>';
?>
		<div class="foot"><a href="list_pm.php">Go to my personnal messages</a></div>
	</body>
</html>
