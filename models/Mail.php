<?php

require_once '../vendor/autoload.php';
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->load();
use PHPMailer\PHPMailer\PHPMailer;

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
}