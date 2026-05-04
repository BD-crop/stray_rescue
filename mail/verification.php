<?php


include_once __DIR__."/PHPMailer/src/PHPMailer.php";
include_once __DIR__."/PHPMailer/src/SMTP.php";
include_once __DIR__."/PHPMailer/src/Exception.php";

function send_mail($name, $email, $id, $table_name) {



    try {
        $mail = new PHPMailer\PHPMailer\PHPMailer;

        $mail->isSMTP();  
        $mail->Host = 'smtp.gmail.com';  
        $mail->SMTPAuth = true;  
        $mail->Username = "strayrescue4@gmail.com";  
        $mail->Password = "vddv qonq qcqu hlof";  
        $mail->SMTPSecure = PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;  

        $mail->setFrom('strayrescue4@gmail.com', 'Stray_Rescue'); 
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