<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;


require '../vendor/autoload.php';

function send_verification($fullname, $email, $otp){
    $mail = new PHPMailer(true);

    try {
        $smtpUsername = getenv('ABSOLUTE_CINEMA_SMTP_USERNAME') ?: 'austinfrancis.reyes.cics@ust.edu.ph';
        $smtpPassword = getenv('ABSOLUTE_CINEMA_SMTP_PASSWORD') ?: 'llpo nsao pgzk ejkt';
        $smtpPassword = preg_replace('/\s+/', '', (string) $smtpPassword);

        $mail->isSMTP();
        $mail->Host = getenv('ABSOLUTE_CINEMA_SMTP_HOST') ?: 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = $smtpUsername;
        $mail->Password = $smtpPassword;
        $mail->SMTPSecure = getenv('ABSOLUTE_CINEMA_SMTP_SECURE') ?: 'tls';
        $mail->Port = (int) (getenv('ABSOLUTE_CINEMA_SMTP_PORT') ?: 587);
        $mail->CharSet = 'UTF-8';

        $mail->setFrom($smtpUsername, 'Absolute Cinema');
        $mail->addAddress($email);

        $mail->isHTML(true);
        $mail->Subject = 'Verify Your Absolute Cinema Account';

        $escapedName = htmlspecialchars($fullname, ENT_QUOTES, 'UTF-8');
        $escapedOtp = htmlspecialchars((string) $otp, ENT_QUOTES, 'UTF-8');

        $mail->Body = '<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>
<body style="margin:0;padding:0;background-color:#0b0b0d;font-family:Arial,Helvetica,sans-serif;color:#f4f4f5;">
    <div style="max-width:640px;margin:0 auto;padding:32px 16px;">
        <div style="background:linear-gradient(180deg,#151518 0%,#0f0f12 100%);border:1px solid rgba(255,255,255,0.08);border-radius:20px;overflow:hidden;box-shadow:0 18px 40px rgba(0,0,0,0.35);">
            <div style="padding:28px 32px 20px;background:linear-gradient(135deg,#dc3545 0%,#8b1e2d 100%);color:#fff;">
                <div style="font-size:13px;letter-spacing:2px;text-transform:uppercase;opacity:0.9;">Absolute Cinema</div>
                <div style="font-size:28px;line-height:1.2;font-weight:700;margin-top:8px;">Verify Your Email</div>
                <div style="font-size:15px;opacity:0.95;margin-top:10px;">Use the code below to activate your account.</div>
            </div>
            <div style="padding:32px;">
                <p style="margin:0 0 18px;font-size:16px;line-height:1.6;">Hello ' . $escapedName . ',</p>
                <p style="margin:0 0 22px;font-size:15px;line-height:1.7;color:#d1d5db;">Thanks for creating your Absolute Cinema account. Enter the verification code below on the OTP verification page to complete your registration.</p>

                <div style="background:#15151a;border:1px solid rgba(255,255,255,0.06);border-radius:18px;padding:22px 20px;text-align:center;margin-bottom:22px;">
                    <div style="font-size:12px;letter-spacing:1.8px;text-transform:uppercase;color:#fca5a5;margin-bottom:10px;">Verification Code</div>
                    <div style="font-size:34px;line-height:1.1;letter-spacing:6px;font-weight:800;color:#ffffff;">' . $escapedOtp . '</div>
                </div>

                <div style="background:#15151a;border:1px solid rgba(255,255,255,0.06);border-radius:16px;padding:18px 20px;margin-bottom:24px;">
                    <div style="font-size:13px;letter-spacing:1px;text-transform:uppercase;color:#fca5a5;margin-bottom:8px;">Security note</div>
                    <div style="font-size:14px;line-height:1.7;color:#e5e7eb;">This code is time-sensitive. If you did not request this email, you can safely ignore it.</div>
                </div>

                <p style="margin:0;font-size:14px;line-height:1.7;color:#9ca3af;">Welcome aboard,<br><span style="color:#f4f4f5;font-weight:700;">Absolute Cinema</span></p>
            </div>
        </div>
        <p style="margin:16px 4px 0;font-size:12px;line-height:1.6;color:#6b7280;text-align:center;">This is an automated message. Do not reply to this email.</p>
    </div>
</body>
</html>';
        $mail->AltBody = "Hello {$fullname},\n\nThanks for creating your Absolute Cinema account.\n\nYour verification code is: {$otp}\n\nEnter it on the OTP verification page to complete registration.";
        $mail->send();

        echo "
        <script>
            Swal.fire({
                icon: 'success',
                title: 'Success!',
                text: 'Email Successfully Sent!',
                background: '#0f0f0f',
                color: '#ffffff',
                iconColor: '#dc3545',
                confirmButtonColor: '#dc3545',
                confirmButtonText: 'OK'
            });
        </script>
        ";

    } catch (Exception $e) {

        echo "
        <script>
            Swal.fire({
                icon: 'error',
                title: 'Email Failed!',
                text: 'Message could not be sent.',
                footer: '".$mail->ErrorInfo."',
                background: '#0f0f0f',
                color: '#ffffff',
                iconColor: '#dc3545',
                confirmButtonColor: '#dc3545'
            });
        </script>
        ";
    }
}
?>