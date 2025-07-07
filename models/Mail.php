<?php

require_once '../vendor/autoload.php';
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->load();
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class Mail
{
    public static function createMailer(): PHPMailer
    {
        $mail = new PHPMailer(true);
        $mail->isSMTP();
        $mail->Host = $_ENV["MAIL_HOST"];
        $mail->SMTPAuth = true;
        $mail->Username = $_ENV["MAIL_USERNAME"];
        $mail->Password = $_ENV["MAIL_PASSWORD"];
        $mail->SMTPSecure = $_ENV["MAIL_SMTPSECURE"];
        $mail->Port = $_ENV["MAIL_PORT"];
        $mail->setFrom($_ENV["MAIL_USERNAME"], $_ENV["MAIL_NAME"]);
        $mail->isHTML(true);
        $mail->CharSet = 'UTF-8';

        return $mail;
    }

        public static function sendEmailVerification($email, $username, $token)
    {
        $verifyUrl = "http://localhost/AdminDashboard/api/users?token=$token";
        try {
            $mail = Mail::createMailer();
            $mail->addAddress($email, $username);
            $mail->Subject = 'Verify Your Email Address';
            $mail->Body = '
            <div style="font-family: Arial, sans-serif; max-width: 600px; margin: auto;">
                <h2>Hello ' . htmlspecialchars($username) . ',</h2>
                <p>Thank you for registering. Please click the link below to verify your email address:</p>
                <p><a href="' . $verifyUrl . '" style="padding: 10px 20px; background-color: #28a745; color: white; text-decoration: none; border-radius: 5px;">Verify Email</a></p>
                <p>If the button doesn\'t work, copy and paste this URL into your browser:</p>
                <p>' . $verifyUrl . '</p>
                <p>If you didn\'t register on our platform, please ignore this email.</p>
            </div>';
            $mail->AltBody = "Hello $username,\nPlease verify your email: $verifyUrl" . " If you didn't register on our platform, please ignore this email.";
            return $mail->send();
        } catch (Exception $e) {
            echo "Mailer Error: " . $mail->ErrorInfo;
            echo "<pre>" . $e->getMessage() . "</pre>";
        }

    }

}