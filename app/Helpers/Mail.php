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
		$this->mail->Host = env('MAIL_HOST');
		$this->mail->Port = env('MAIL_PORT');
		$this->mail->Username = env('MAIL_USERNAME');
		$this->mail->Password = env('MAIL_PASSWORD');
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
		$this->mail->setFrom(env('MAIL_FROM_ADDRESS'), env('MAIL_FROM_NAME'));
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
		$this->mail->AltBody = "de: ".env('ALT_BODY');

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