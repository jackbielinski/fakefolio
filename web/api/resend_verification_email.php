<?php
    include "../inc/main.php";
    include "../../vendor/autoload.php";

    use PHPMailer\PHPMailer\PHPMailer;
    use PHPMailer\PHPMailer\Exception;
    use Symfony\Component\Dotenv\Dotenv;

    $userId = $_POST['user_id'] ?? $_GET['user_id'] ?? null;

    if (!is_numeric($userId)) {
        echo json_encode(['error' => 'Invalid user ID']);
        exit;
    }

    // Get the verification code from the database
    $conn = getDB();
    $sql = "SELECT verification_code FROM verification_codes WHERE associated_user = ?";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$userId]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$result) {
        echo json_encode(['error' => 'No verification code found for this user']);
        exit;
    } else {
        $verificationCode = $result['verification_code'];

        // Get the user's email address
        $email = getUserInfo($userId)['email'];
        $username = getUserInfo($userId)['username'];
        $mail = new PHPMailer(true);

        try {
            $dotenv = new Dotenv();
            $dotenv->load('../inc/.env');

            //Server settings
            $mail->isSMTP();
            $mail->Host = $_ENV["MAIL_HOST"];
            $mail->SMTPAuth = true;
            $mail->Username = $_ENV["MAIL_USERNAME"];
            $mail->Password = $_ENV["MAIL_PASSWORD"];
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->SMTPAutoTLS = false;
            $mail->Port = $_ENV["MAIL_PORT"];

            //Recipients
            $mail->setFrom($_ENV["MAIL_USERNAME"], 'Fakefolio Support');
            $mail->addAddress($email, $username);

            // Content
            $mail->isHTML(true);
            $mail->Subject = 'Verify your E-mail Address';
            $mail->Body = '<p>Hi ' . htmlspecialchars($username) . ',</p><p>Thank you, and welcome to Fakefolio! To complete your registration, please <a href="https://fakefolio.com/verify.php?verificationCode=' . htmlspecialchars($verificationCode) . '&user_id=' . htmlspecialchars($userId) . '">verify your E-mail address</a>.</p><p>Link not working? Use this verification code on-site: ' . htmlspecialchars($verificationCode) . '</p><p>If you did not create an account, please ignore this email.</p><p>Best regards,<br>Fakefolio Team</p>';

            $mail->send();
            echo json_encode(['success' => 'Verification requested successfully']);
        } catch (Exception $e) {
            echo json_encode(['error' => 'Failed to send verification request: ' . $mail->ErrorInfo]);
        }
}
?>