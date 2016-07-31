<?php

class Postman
{
	private $emailTranslator;
	
	function __construct()
	{ 
		$this->emailTranslator = new EmailTranslator(LANG_CODE);
	}

	// Send a job post to a friend
	public function MailSendToFriend($friend_email, $my_email)
	{
		$subject = $this->emailTranslator->GetSendToFriendSubject();
		$msg = $_SERVER['HTTP_REFERER'];
		
		$msg .= $this->emailTranslator->GetSendToFriendMsg($my_email);
		if ($friend_email != '' && $my_email != '' && validate_email($friend_email) && validate_email($my_email))
		{
			$mailer = $this->getConfiguredMailer();
			
			$mailer->SetFrom($my_email);
    		$mailer->AddAddress($friend_email);
			$mailer->Subject = $subject;
			$mailer->Body = $this->nl2br($msg);
			$mailer->AltBody = $msg;
			
			if ($mailer->Send())
			{
				return true;
			}
			else
			{
				return false;
			}
		}
		else
		{
			return false;
		}
	}
	
	// Send email to company when applying online
	public function MailApplyOnline($data)
	{
		$mailer = $this->getConfiguredMailer();
		$subject = $this->emailTranslator->GetApplyOnlineSubject($data);
		
		$msg = $data['apply_msg'];
		
		$msg .= $this->emailTranslator->GetApplyOnlineMsg($_SERVER['HTTP_REFERER']);
		
    	$mailer->SetFrom($data['apply_email'], $data['apply_name']);
    	$mailer->AddAddress($data['company_email'], $data['company_name']);
    	$mailer->Subject = $subject;
    	$mailer->Body = $this->nl2br($msg);
    	$mailer->AltBody = $msg;
    	
		if ($data['attachment_filename'] != '')
		{
    		$mailer->AddAttachment($data['attachment_path'], $data['attachment_filename']);
		}

		if ($mailer->Send())
		{
			return true;
		}
		else
		{
			return false;
		}
	}
	
	// Send email to forgt password users 
	public function Mailforgotpassword($email_id,$subject,$msg,$from)
	{
		/*echo $email_id."email_id<br/>".$subject."subject<br/>".$msg."msg<br/>".$from."from<br/>";*/
		

		$mailer = $this->getConfiguredMailer();
		$mailer->SetFrom($from,"admin scan4jobs");
    	$mailer->AddAddress($email_id,"abc");
    	$mailer->Subject = $subject;
    	$mailer->Body = $this->nl2br($msg);
    	$mailer->AltBody = $msg;
    	if ($mailer->Send())
		{
			return true;
		}
		else
		{
			return false;
		}
	}
	// Send mail to admin when a job is posted
	public function MailPublishToAdmin($data)
	{
		$subject = $this->emailTranslator->GetPublishToAdminSubject($data);
		
		$msg = '';
		
		if ($data['postRequiresModeration'])
		{
			$activateUrl = BASE_URL . "activate/" . $data['id'] . "/" . $data['auth'] . "/";
			$msg = $this->emailTranslator->GetPublishToAdminExtraMsg($activateUrl);
		}
		
		$data['job_url'] = BASE_URL . URL_JOB .'/' . $data['id'] . '/' . $data['url_title'] . '/';
		$data['edit_url'] = BASE_URL . "post/" . $data['id'] . "/" . $data['auth'] . "/";
		$data['deactivate_url'] =  BASE_URL . "deactivate/" . $data['id'] . "/" . $data['auth'] . "/";
		$data['poster_ip'] = $_SERVER['REMOTE_ADDR'];
		
		$msg .= $this->emailTranslator->GetPublishToAdminMsg($data);
		
		$mailer = $this->getConfiguredMailer();
			
		$mailer->SetFrom(NOTIFY_EMAIL, SITE_NAME);
    	$mailer->AddAddress(NOTIFY_EMAIL);
		$mailer->Subject = $subject;
		$mailer->Body = $this->nl2br($msg);
		$mailer->AltBody = $msg;
		
		$mailer->Send();
	}
	
	// Send mail to user when posting first time (thus the post needs to be moderated)
	public function MailPublishPendingToUser($poster_email)
	{
		$subject = $this->emailTranslator->GetPublishPendingToUserSubject();
		$msg = $this->emailTranslator->GetPublishPendingToUserMsg();
		
		if ($poster_email != '' && validate_email($poster_email))
		{
			$mailer = $this->getConfiguredMailer();
			
			$mailer->SetFrom(NOTIFY_EMAIL, SITE_NAME);
	    	$mailer->AddAddress($poster_email);
			$mailer->Subject = $subject;
			$mailer->Body = $this->nl2br($msg);
			$mailer->AltBody = $msg;
			
			$mailer->Send();
		}
	}
	
	// Send mail to user when a post is published
	public function MailPublishToUser($data, $url=BASE_URL)
	{	
		$subject = $this->emailTranslator->GetPublishToUserSubject();
				
		$data['job_url'] = $url . URL_JOB ."/" . $data['id'] . "/" . $data['url_title'] . "/";
		$data['edit_url'] = $url . "post/" . $data['id'] . "/" . $data['auth'] . "/";
		$data['deactivate_url'] =   $url . "deactivate/" . $data['id'] . "/" . $data['auth'] . "/";
		$msg = $this->emailTranslator->GetPublishToUserMsg($data);
		
		if ($data['poster_email'] != '' && validate_email($data['poster_email']))
		{
			$mailer = $this->getConfiguredMailer();
			
			$mailer->SetFrom(NOTIFY_EMAIL, SITE_NAME);
	    	$mailer->AddAddress($data['poster_email']);
			$mailer->Subject = $subject;
			$mailer->Body = $this->nl2br($msg);
			$mailer->AltBody = $msg;
			
			$mailer->Send();
		}
	}
	
	// Send mail to user when a post is activated (after first-time post)
 	public function MailPostActivatedToUser($data, $url=BASE_URL)
    {
        $this->MailPublishToUser($data, $url);
    }
	
	// Send mail to admin when someone posts a new spam word
	public function MailReportSpam($data)
	{
		$job_title = BASE_URL . URL_JOB .'/' . $data['id'] . '/' . $data['url_title'] . '/';
		
		$data['job_title'] = $job_title;
		$data['edit_url'] = BASE_URL . "post/" . $data['id'] . "/" . $data['auth'] . "/";
		$data['deactivate_url'] = BASE_URL . "deactivate/" . $data['id'] . "/" . $data['auth'] . "/";
		$data['poster_ip'] = $_SERVER['REMOTE_ADDR'];
		
		$subject = $this->emailTranslator->GetReportSpamSubject($job_title);
		$msg = $this->emailTranslator->GetReportSpamMsg($data);
		
		$mailer = $this->getConfiguredMailer();
			
		$mailer->SetFrom(NOTIFY_EMAIL, SITE_NAME);
    	$mailer->AddAddress(NOTIFY_EMAIL);
		$mailer->Subject = $subject;
		$mailer->Body = $this->nl2br($msg);
		$mailer->AltBody = $msg;
		
		$mailer->Send();
	}
	
	public function MailContact($name, $email, $msg)
	{
		$data['sender_name'] = $name;
		$data['sender_email'] = $email;
		$data['poster_ip'] = $_SERVER['REMOTE_ADDR'];
		$data['created_on'] = date('Y-m-d H:i');
		$subject = $this->emailTranslator->GetContactSubject();
		$msg .= $this->emailTranslator->GetContactMsg($data);
		
		$mailer = $this->getConfiguredMailer();
			
		$mailer->SetFrom($email, $name);
    	$mailer->AddAddress(NOTIFY_EMAIL);
		$mailer->Subject = $subject;
		$mailer->Body = $this->nl2br($msg);
		$mailer->AltBody = $msg;
		
		if($mailer->Send())
		{
			return true;
		}
		else
		{
			return false;
		}	
	}

	private function getConfiguredMailer()
	{
		global $settings;
		
		$mailer = new PHPMailer();
		$mailer->Mailer = $settings['mailer_mailer'];
		$mailer->CharSet = $settings['mailer_encoding'];
		
		if($settings['mailer_mailer'] == 'smtp')
		{
			$mailer->Host = $settings['mailer_smtp_host'];
			$mailer->Port = $settings['mailer_smtp_port'];

			if ($settings['mailer_smtp_requires_authentication'])
			{
				$mailer->SMTPAuth = true;
				$mailer->SMTPSecure = $settings['mailer_smtp_secure_connection_prefix'];
				
				$mailer->Username = $settings['mailer_smtp_username'];
				$mailer->Password = $settings['mailer_smtp_password'];
			}
		}
		
		return $mailer;
	}
	
	private function nl2br($text)
	{
		return str_replace(array("\r\n", "\r", "\n"), "<br />", $text);
	}
	public function Mailuser($email_id,$from)
	{
		/*echo $email_id."email_id<br/>".$subject."subject<br/>".$msg."msg<br/>".$from."from<br/>";*/
		

		$mailer = $this->getConfiguredMailer();
		$mailer->SetFrom($from,"admin scan4jobs");
    	$mailer->AddAddress($email_id,"abc");
    	$mailer->Body = $this->nl2br($msg);
    	if ($mailer->Send())
		{
			return true;
		}
		else
		{
			return false;
		}
	}
	// Send email to new registration user 
	public function Mailreguser($email_id,$subject,$msg,$from)
	{
     	$mailer = $this->getConfiguredMailer();
		$mailer->SetFrom($from,"admin scan4jobs");
    	$mailer->AddAddress($email_id,"abc");
    	$mailer->Subject = $subject;
    	$mailer->Body = $this->nl2br($msg);
    	$mailer->AltBody = $msg;
    	if ($mailer->Send())
		{
			return true;
		}
		else
		{
			return false;
		}
	}
}
?>