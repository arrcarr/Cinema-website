<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require_once __DIR__ . '/../vendor/autoload.php';

function send_booking_confirmation($fullname, $email, $movieTitle, $showtimeText, $seats)
{
    if (empty($email)) {
        return [false, 'Recipient email address is missing.'];
    }

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
        $mail->addAddress($email, $fullname);
        $mail->isHTML(true);
        $mail->Subject = 'Movie Booking Verification';

        $seatList = is_array($seats) ? implode(', ', $seats) : $seats;
        $escapedName = htmlspecialchars($fullname, ENT_QUOTES, 'UTF-8');
        $escapedMovieTitle = htmlspecialchars($movieTitle, ENT_QUOTES, 'UTF-8');
        $escapedShowtimeText = htmlspecialchars($showtimeText, ENT_QUOTES, 'UTF-8');
        $escapedSeatList = htmlspecialchars($seatList, ENT_QUOTES, 'UTF-8');

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
                <div style="font-size:28px;line-height:1.2;font-weight:700;margin-top:8px;">Booking Confirmed</div>
                <div style="font-size:15px;opacity:0.95;margin-top:10px;">Your reservation has been saved successfully.</div>
            </div>
            <div style="padding:32px;">
                <p style="margin:0 0 18px;font-size:16px;line-height:1.6;">Hi ' . $escapedName . ',</p>
                <p style="margin:0 0 22px;font-size:15px;line-height:1.7;color:#d1d5db;">Here is your booking summary. Please keep this email for your records and show it if needed at the cinema.</p>

                <table role="presentation" cellpadding="0" cellspacing="0" style="width:100%;border-collapse:collapse;margin:0 0 24px;">
                    <tr>
                        <td style="padding:14px 0;border-bottom:1px solid rgba(255,255,255,0.08);color:#9ca3af;width:34%;">Movie</td>
                        <td style="padding:14px 0;border-bottom:1px solid rgba(255,255,255,0.08);font-weight:700;">' . $escapedMovieTitle . '</td>
                    </tr>
                    <tr>
                        <td style="padding:14px 0;border-bottom:1px solid rgba(255,255,255,0.08);color:#9ca3af;">Showtime</td>
                        <td style="padding:14px 0;border-bottom:1px solid rgba(255,255,255,0.08);font-weight:700;">' . $escapedShowtimeText . '</td>
                    </tr>
                    <tr>
                        <td style="padding:14px 0;color:#9ca3af;">Seats</td>
                        <td style="padding:14px 0;font-weight:700;">' . $escapedSeatList . '</td>
                    </tr>
                </table>

                <div style="background:#15151a;border:1px solid rgba(255,255,255,0.06);border-radius:16px;padding:18px 20px;margin-bottom:24px;">
                    <div style="font-size:13px;letter-spacing:1px;text-transform:uppercase;color:#fca5a5;margin-bottom:8px;">Next step</div>
                    <div style="font-size:14px;line-height:1.7;color:#e5e7eb;">Arrive a few minutes early and present your booking details at the counter if requested.</div>
                </div>

                <p style="margin:0;font-size:14px;line-height:1.7;color:#9ca3af;">Enjoy the show,<br><span style="color:#f4f4f5;font-weight:700;">Absolute Cinema</span></p>
            </div>
        </div>
        <p style="margin:16px 4px 0;font-size:12px;line-height:1.6;color:#6b7280;text-align:center;">This is an automated booking confirmation email. Please do not reply directly to this message.</p>
    </div>
</body>
</html>';
        $mail->AltBody = "Hi {$fullname},\n\nYour movie booking has been confirmed.\n\nMovie: {$movieTitle}\nShowtime: {$showtimeText}\nSeats: {$seatList}\n\nEnjoy the show at Absolute Cinema.";

        $mail->send();
        return [true, ''];
    } catch (Exception $e) {
        $errorMessage = 'Booking email failed: ' . $e->getMessage();
        error_log($errorMessage);
        return [false, $e->getMessage()];
    }
}
?>