<?php
	include('config.php');
?>

<html>
	<head>
		<title>Database Dump</title>
	</head>
	<body>

<?php
//We display all tables
//
//Users
//We get all rows of users

echo "user<BR>";
echo "id,username,password,e-mail,avatar,signup_date,salt<BR>";
$req = mysqli_query($link, 'select * from users');

while($dnn = mysqli_fetch_array($req))
{
	echo $dnn['id'].",";
	echo htmlentities($dnn['username'], ENT_QUOTES, 'UTF-8').",";
	echo htmlentities($dnn['password'], ENT_QUOTES, 'UTF-8').",";
	echo htmlentities($dnn['email'], ENT_QUOTES, 'UTF-8').",";
	echo htmlentities($dnn['avatar'], ENT_QUOTES, 'UTF-8').",";
	echo htmlentities(date("Y-m-d H:i:s", $dnn['signup_date']), ENT_QUOTES, 'UTF-8').",";
	echo htmlentities($dnn['salt'], ENT_QUOTES, 'UTF-8')."<BR>";
}

//Users
//We get all rows of users
echo "<BR>pm<BR>";
echo "id,id2,title,user1,user2,message,timestamp,user1read,user2read,tag<BR>";
$req = mysqli_query($link, 'select * from pm');

while($dnn = mysqli_fetch_array($req))
{
	echo $dnn['id'].",";
	echo $dnn['id2'].",";
	echo htmlentities($dnn['title'], ENT_QUOTES, 'UTF-8').",";
	echo htmlentities($dnn['user1'], ENT_QUOTES, 'UTF-8').",";
	echo htmlentities($dnn['user2'], ENT_QUOTES, 'UTF-8').",";
	echo htmlentities($dnn['message'], ENT_QUOTES, 'UTF-8').",";
	echo htmlentities(date("Y-m-d H:i:s", $dnn['timestamp']), ENT_QUOTES, 'UTF-8').",";
	echo htmlentities($dnn['user1read'], ENT_QUOTES, 'UTF-8').",";
	echo htmlentities($dnn['user2read'], ENT_QUOTES, 'UTF-8').",";
	echo htmlentities($dnn['tag'], ENT_QUOTES, 'UTF-8')."<BR>";
}

//Encryption keys for messages
//We get all rows of users
echo "<BR>messagekeys<BR>";
echo "user1,user2,mskey<BR>";
$req = mysqli_query($link, 'select * from messagekeys');

while($dnn = mysqli_fetch_array($req))
{
	echo htmlentities($dnn['user1'], ENT_QUOTES, 'UTF-8').",";
	echo htmlentities($dnn['user2'], ENT_QUOTES, 'UTF-8').",";
	echo htmlentities($dnn['mskey'], ENT_QUOTES, 'UTF-8')."<BR>";
}
?>
	</body>
</html>