<?php

// Import PHPMailer classes into the global namespace
// These must be at the top of your script, not inside a function
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\PHPMailer;


// If necessary, modify the path in the require statement below to refer to the
// location of your Composer autoload.php file.
require 'vendor/autoload.php';


// Adding DotEnv file into it
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

// Replace sender@example.com with your "From" address.
// This address must be verified with Amazon SES.
$sender = 'noreply@learninghub.pk';
$senderName = 'Learning Hub Pvt. Ltd.';

// Replace recipient@example.com with a "To" address. If your account
// is still in the sandbox, this address must be verified.
$recipient = 'info@learninghub.pk';

// Replace smtp_username with your Amazon SES SMTP user name.
$usernameSmtp = getenv('usernameSmtp');

// Replace smtp_password with your Amazon SES SMTP password.
$passwordSmtp =  getenv('passwordSmtp');

// Specify a configuration set. If you do not want to use a configuration
// set, comment or remove the next line.
// $configurationSet = 'ConfigSet';

// If you're using Amazon SES in a region other than US West (Oregon),
// replace email-smtp.us-west-2.amazonaws.com with the Amazon SES SMTP
// endpoint in the appropriate region.
$host = getenv('host');
$port =  getenv('port');

// The subject line of the email
$subject = 'Learning Hub-Contact Form';

 
// contact form data
$name = $_POST['name'];
$email = $_POST['email'];
$phone = $_POST['phone'];
$message = $_POST['message'];

// The plain-text body of the email
$bodyText =  $message;

// The HTML-formatted body of the email
$bodyHtml = 'Sender:    '. $name .'<br> Sender Email:   '. $email . '<br> Sender Phone Number:  ' . $phone . '<br> containing following message:<br>'.$message ;

$mail = new PHPMailer(true);

try {
    // Specify the SMTP settings.
    $mail->isSMTP();
    $mail->setFrom($sender, $senderName);
    $mail->Username   = $usernameSmtp;
    $mail->Password   = $passwordSmtp;
    $mail->Host       = $host;
    $mail->Port       = $port;
    $mail->SMTPAuth   = true;
    $mail->SMTPSecure = 'tls';
    $mail->addCustomHeader('X-SES-CONFIGURATION-SET', $configurationSet);

    // Specify the message recipients.
    $mail->addAddress($recipient);
    // You can also add CC, BCC, and additional To recipients here.

    // Specify the content of the message.
    $mail->isHTML(true);
    $mail->Subject    = $subject;
    $mail->Body       = $bodyHtml;
    $mail->AltBody    = $bodyText;
    $mail->Send();
    echo "Email sent!" , PHP_EOL;
} catch (phpmailerException $e) {
    echo "An error occurred. {$e->errorMessage()}", PHP_EOL; //Catch errors from PHPMailer.
} catch (Exception $e) {
    echo "Email not sent. {$mail->ErrorInfo}", PHP_EOL; //Catch errors from Amazon SES.
}
