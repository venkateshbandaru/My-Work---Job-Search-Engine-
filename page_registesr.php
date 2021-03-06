<?php
		if(!empty($_POST['action']) && $_POST['action'] == 'register')
		{
			escape($_POST);
			$register_errors = array();
			$_SESSION['user_ip'] = $_SERVER['REMOTE_ADDR'];
			$_SESSION['referer'] = BASE_URL;
		
			// validation
			if ($email == '')
			{
				$register_errors['email'] = 'Email ID is required.';
			}
			if(!empty($email)){
				if(!preg_match("/^[_\.0-9a-zA-Z-]+@([0-9a-zA-Z][0-9a-zA-Z-]+\.)+[a-zA-Z]{2,6}$/i", $email)) {
						 $register_errors['email'] = 'Email ID is not valid.';
					}
					else {
					$valid = true;
					}
				/*if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
				 } else {
				  $register_errors['email'] = 'Email ID is not valid.';
				}*/
			}
			if ($pwd == '')
			{
				$register_errors['pwd'] = 'Password is required.';
			}
			if(!empty($pwd))
			{
				if(strlen($pwd) < 6 || strlen($pwd) > 15){
					$register_errors['pwd'] = 'Password should be 6 to 15 characters.';	
				}
			}
			if ($cpwd == '')
			{
				$register_errors['cpwd'] = 'Confirm Password is required.';
			}
			if(!empty($cpwd)){
				if($pwd != $cpwd)
				{
					$register_errors['match'] = "Password & Confirm Password doesn't match.";
				}
			}
			if ($contactnumber == '')
			{
				$register_errors['contactnumber'] = "Mobile No is required.";
			}
			if(!empty($contactnumber)){
				if(strlen($contactnumber) != 10)
				{
					$register_errors['contactnumber'] = "Enter your 10-digit Mobile No.";
				}
			}
			if(isset($terms) != 1){
				$register_errors['terms'] = "Please accept the terms and conditions.";
			}
			if(!isset($sms))
			{
				  $sms=0;
			}
			if(!empty($email)){
				$user = new CRegister();
				$user->userExists($email);
				$id = $user->getId();
				if($id != 0)
				{
					$register_errors['id'] = "Email ID already exits.";
					$smarty->assign('register_errors', $register_errors);
				}
				
			}

			
			if (empty($register_errors))
			{
				$contactnumber=$code.$contactnumber;
				$user = new CRegister();
				$user->userExists($email);

				if($user->insert($email, $pwd,$contactnumber,$sms))
					{
					
						$_SESSION['UserId'] = $user->getId();
 					    redirect_to(BASE_URL.'full_registration/?number='.$contactnumber.'&email='.$email);
						exit;
					}
				
						
			}
			// if errors exist, go back and edit the post
			else
			{
				$smarty->assign('register_errors', $register_errors);
			}
		}
		define('NUMBER_OF_LATEST_JOBS_TO_GET', $settings['latest_jobs']);
		$job = new Job();
		$smarty->assign('latest_jobs', $job->GetJobs(0, 0, NUMBER_OF_LATEST_JOBS_TO_GET, 0, 0));
		$template = 'login.tpl';
		
?>