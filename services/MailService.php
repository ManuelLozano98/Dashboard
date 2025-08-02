<?php
require_once __DIR__ . "/../models/Mail.php";
class MailService
{

    public function sendEmailVerification($email, $username, $token)
    {
        $verifyUrl = "http://localhost/AdminDashboard/email/confirm?token=$token";
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
            return Mail::sendEmail($mail);
        } catch (Exception $e) {
            echo "Mailer Error: " . $mail->ErrorInfo;
            echo "<pre>" . $e->getMessage() . "</pre>";
        }

    }
}