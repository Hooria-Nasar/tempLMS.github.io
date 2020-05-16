<?php

// A function definition for google recaptcha
function reCaptcha($recaptcha)
{
    $secret = "6LfhKPgUAAAAAHpbhonXfSK7QFWtZfrO-zX4PQ8t";
    $ip = $_SERVER['REMOTE_ADDR'];

    $postvars = array("secret" => $secret, "response" => $recaptcha, "remoteip" => $ip);
    $url = "https://www.google.com/recaptcha/api/siteverify";
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $postvars);
    $data = curl_exec($ch);
    curl_close($ch);

    return json_decode($data, true);
}s

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
$sender = 'fahadkhanchughtai@gmail.com';
$senderName = $_POST['name'];

// Replace recipient@example.com with a "To" address. If your account
// is still in the sandbox, this address must be verified.
$recipient = 'info@learninghub.pk';

// Replace smtp_username with your Amazon SES SMTP user name.
$usernameSmtp = getenv('SMTP_USERNAME');

// Replace smtp_password with your Amazon SES SMTP password.
$passwordSmtp =  getenv('SMTP_PASSWORD');

// Specify a configuration set. If you do not want to use a configuration
// set, comment or remove the next line.
// $configurationSet = 'ConfigSet';

// If you're using Amazon SES in a region other than US West (Oregon),
// replace email-smtp.us-west-2.amazonaws.com with the Amazon SES SMTP
// endpoint in the appropriate region.
$host = getenv('SMTP_HOST');
$port =  getenv('SMTP_PORT');

// The subject line of the email
$subject = 'Contact';


// contact form data
$name = $_POST['name'];
$email = $_POST['email'];
$phone = $_POST['phone'];
$message = $_POST['message'];

// The plain-text body of the email
$bodyText =  $message;

// The HTML-formatted body of the email
$bodyHtml = 'Sender:    ' . $name . '<br> Sender Email:   ' . $email . '<br> Sender Phone Number:  ' . $phone . '<br> containing following message:<br>' . $message;

$mail = new PHPMailer(true);



// the code for captcha
$recaptcha = $_POST['g-recaptcha-response'];
$res = reCaptcha($recaptcha);
if (!$res['success']) {
    // Error
}
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
    echo "Email sent!", PHP_EOL;
} catch (phpmailerException $e) {
    echo "An error occurred. {$e->errorMessage()}", PHP_EOL; //Catch errors from PHPMailer.
} catch (Exception $e) {
    echo "Email not sent. {$mail->ErrorInfo}", PHP_EOL; //Catch errors from Amazon SES.
}
