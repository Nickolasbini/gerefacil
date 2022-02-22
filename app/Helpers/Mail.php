<?php

namespace App\Helpers;

use PHPMailer\PHPMailer\PHPMailer;
use Exception;

/**
 * 
 */
class Mail
{
	/** @var PHPMailer */
	private $mail;

	function __construct()
	{
		$this->mail = new PHPMailer(true);
		// Configuation
		$this->mail->isSMTP();
		$this->mail->SMTPAuth = true;
		$this->mail->SMTPSecure = 'tls';
		$this->mail->CharSet = 'utf-8';
		// Server and Email info
		$this->mail->Host = MAIL_CONFIGURATION['host'];
		$this->mail->Port = MAIL_CONFIGURATION['port'];
		$this->mail->Username = MAIL_CONFIGURATION['user'];
		$this->mail->Password = MAIL_CONFIGURATION['passwd'];
		$this->mail->addCustomHeader('Importance', 'high');
	}

	/**
	* Sends message to informed mail(s)
	* @release 2021-01-30
	* @param <array>  mail addresses to send to
	* @param <string> title of the email
	* @param <string> body of email, its content in HTML
	* @param <array>  attachments
	*				  example: [
	*								'img name' => 'image path'
	*						   ]
	* @param <array>  embeddedImgs
	*				  example: [
	*								'img name used as HTML' => 'image path'
	*						   ]
	* 			      <img alt="PHPMailer" src="cid:img name used as HTML">
	*
	* @return <indexed array> keys: <bool>   success or false
	*								<string> success or error message
	*/
	public function sendMail($toEmail = [], $subject = '', $message = '', $attachments = [], $embeddedImgs = [])
	{
		// Sender
		$this->mail->setFrom(MAIL_CONFIGURATION['fromMail'], MAIL_CONFIGURATION['fromName']);
		// Receiver
		if(is_array($toEmail)){
			foreach($toEmail as $email){
				$this->mail->addAddress($email);
			}
		}else{
			$this->mail->addAddress($toEmail);
		}

		// Title of email
		$this->mail->Subject = $subject;

		foreach($embeddedImgs as $imgName => $path){
			$this->mail->AddEmbeddedImage($path, $imgName);
		}

		$this->mail->msgHTML($message);

		// Sender default message
		$this->mail->AltBody = "de: ".MAIL_CONFIGURATION['AltBody'];

		// Adding attachments
		if(!empty($attachments)){
			foreach($attachments as $attachmentName => $pathName){
				$this->mail->AddAttachment($pathName);
			}
		}

		try{
			$this->mail->send();

			$response = [
				'success' => true,
				'message' => 'mail sent with success'
			];
			return $response;
		}catch(Exception $exception){
			$response = [
				'success' => false,
				'message' => $exception
			];
			return $response;
		}

	}
}