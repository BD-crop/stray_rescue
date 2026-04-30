<?php
require 'vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use Dotenv\Dotenv;

$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->load();


function send_mail($name, $email, $id, $table_name) {
    $mail = new PHPMailer(true); 

    try {
        $mail->isSMTP();  
        $mail->Host = 'smtp.gmail.com';  
        $mail->SMTPAuth = true;  
        $mail->Username = getenv('gmail');  
        $mail->Password = getenv('passw');  
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;  
        $mail->Port = 587;  

        $mail->setFrom('farhanadib577@gmail.com', 'Stray_Rescue'); 
        $mail->addAddress($email, $name);  

        $mail->Subject = 'Email Verification';
        $mail->isHTML(true);  
        $mail->Body = '
            <html>
            <body>
                <p>Hi ' . $name . ',</p>
                <p>Click the button below to verify your email:</p>
                <a href="http://localhost:80/dashboard/email_verification/email_verification.php?id=' . $id . '&table_name=' . $table_name . '&email=' . urlencode($email) . '" style="text-decoration: none; padding: 12px 24px; background-color: #4CAF50; color: white; font-size: 16px; font-weight: bold; text-align: center; border-radius: 5px; display: inline-block;">
                    Verify Email
                </a>
            </body>
            </html>';

        if ($mail->send()) {
            echo 'Email has been sent successfully!';
        } else {
            echo 'Mailer Error: ' . $mail->ErrorInfo;
        }

    } catch (Exception $e) {
        exit('Error: ' . $e->getMessage());
    }
}

?>