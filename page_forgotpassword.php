<?php
define('IN_SCRIPT', true);
// Start a session
session_start();
error_reporting(E_ALL ^ E_NOTICE);
//this function will display error messages in alert boxes, used for login forms so if a field is invalid it will still keep the info
//use error('foobar');
function error($msg) 
{
	?>
	<html>
	<head>
	<script language="JavaScript">
	<!--
	alert("<?=$msg?>");
	history.back();
	//-->
	</script>
	</head>
	<body>
	</body>
	</html>
	<?
	exit;
}
//This functions checks and makes sure the email address that is being added to database is valid in format.
function check_email_address($email) 
{
	// First, we check that there's one @ symbol, and that the lengths are right
	if(!empty($email))
	{
		if(!preg_match("/^[_\.0-9a-zA-Z-]+@([0-9a-zA-Z][0-9a-zA-Z-]+\.)+[a-zA-Z]{2,6}$/i", $email))
		{
			$errors['email'] = 'Email ID is not valid.';
		}
		else 
		{
			$valid = true;
		}
	}
	// Split it into sections to make life easier
	$email_array = explode("@", $email);
	$local_array = explode(".", $email_array[0]);
	for ($i = 0; $i < sizeof($local_array); $i++) 
	{
		if(($email))
		{
			if(!preg_match("/^[_\.0-9a-zA-Z-]+@([0-9a-zA-Z][0-9a-zA-Z-]+\.)+[a-zA-Z]{2,6}$/i", $email)) 
			{
				$errors['username'] = 'Email ID is not valid.';
			}
			else 
			{
				$valid = true;
			}
		}
	}	 
	return true;
}
if (isset($_POST['submit']))
{
	if ($_POST['forgotpassword']=='') 
	{
		error('Please Fill in Email.');
	}
	if(get_magic_quotes_gpc()) 
	{
		$forgotpassword = htmlspecialchars(stripslashes($_POST['forgotpassword']));
	}
	else
	{
		$forgotpassword = htmlspecialchars($_POST['forgotpassword']);
	}
	//Make sure it's a valid email address, last thing we want is some sort of exploit!
	if (!check_email_address($_POST['forgotpassword'])) 
	{
		error('Email Not Valid - Must be in format of name@domain.tld');
	}
	// Lets see if the email exists
	$sql = "SELECT * FROM user_info WHERE email = '".$forgotpassword."'";
	$result = mysql_query($sql)or die('Could not find member: ' . mysql_error());
	$rows= mysql_num_rows($result);
	while($info = mysql_fetch_array($result))
	{
		$firstname = $info['firstname'];
	}
	if ($rows == 0) 
	{
		error('Email Not Found!');
	}
	//Generate a RANDOM MD5 Hash for a password
	$random_password=md5(uniqid(rand()));
	//Take the first 8 digits and use them as the password we intend to email the user
	$emailpassword=substr($random_password, 0, 8);
	//Encrypt $emailpassword in MD5 format for the database
	$newpassword = md5($emailpassword);
	// Make a safe query
	$query = sprintf("UPDATE `user_info` SET `password` = '%s'
	WHERE `email` = '$forgotpassword'",
	mysql_real_escape_string($newpassword));
	mysql_query($query)or die('Could not update members: ' . mysql_error());
	//Email out the infromation
	$subject = "Your New Password";
	 $message = "Hello &nbsp;$firstname,
	----------------------------</br>
	Email-Id: $forgotpassword
	Your new password: $emailpassword
	---------------------------- <br/>
	This password has been automatically generated by our system.Please login to your scan4jobs account with this new password.<br/>
	Best Regards,<br/>
	scan4jobs.com<br/>
	http://www.scan4jobs.com.";
	escape($_POST);
	$password = new Postman();
	if ($password->Mailforgotpassword($forgotpassword, $subject, $message,  "sivareddy103@gmail.com"))
	{
	redirect_to(BASE_URL.'detaillogin/?msg=Check your email, we sent your Password of your scan4jobs account.');
	}
}
else 
	{
		$template = 'forgotpassword.tpl';
	}
?>

