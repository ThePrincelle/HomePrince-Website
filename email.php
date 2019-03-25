<?php
	//PHP Email Sender
	//Created by Maxime Princelle

    require 'PHPMailer/src/PHPMailer.php';
    require 'PHPMailer/src/Exception.php';
    require 'PHPMailer/src/SMTP.php';

    use PHPMailer\PHPMailer\PHPMailer;
    use PHPMailer\PHPMailer\Exception;

    $namexp = $_POST['name'];
    $mailexp = $_POST['emailcontact'];
    $subject = $_POST['subject'];
    $message = $_POST['message'];

    $to = "contact@homeprince.princelle.org";
	$sub = "Msg de $namexp : $subject";
    $contenu = "<b>Nom expéditeur: </b> $namexp <br> <b>Email expéditeur: </b> $mailexp <br> <b>Numéro de téléphone: </b> $subject <br><br> <b>Message: </b><br> $message";

    $mail = new PHPMailer(false);                              // Passing `true` enables exceptions
    try
    {
        //Server settings
        $mail->SMTPDebug = 0;                                 // Enable verbose debug output
        $mail->CharSet = 'UTF-8';
        $mail->Encoding = 'base64';
        $mail->isSendMail();                                      // Set mailer to use SMTP
        $mail->Host = 'auth.smtp.1and1.fr;smtp.ionos.fr';  // Specify main and backup SMTP servers
        $mail->SMTPAuth = true;                               // Enable SMTP authentication
        $mail->Username = 'maxime@princelle.org';                 // SMTP username
        $mail->Password = 'oCsy7r#FAvJ*';                           // SMTP password
        $mail->SMTPSecure = 'tls';                            // Enable TLS encryption, `ssl` also accepted
        $mail->Port = 587;                                    // TCP port to connect to

        //Recipients
        $mail->setFrom('contact@princelle.org', 'Formulaire de contact - princelle.org');
        $mail->addAddress($to, 'Contact - Maxime Princelle');
        $mail->AddReplyTo($mailexp, $namexp);

        //Content
        $mail->isHTML(true);                                  // Set email format to HTML
        $mail->Subject = $sub;

        $mail->Body = $contenu;
        $mail->AltBody = $contenu;

        $mail->send();
        //echo 'Message has been sent';
        header("Location: ../success.html");
    }
    catch (Exception $e)
    {
        //echo 'Message could not be sent. Mailer Error: ', $mail->ErrorInfo;
        header("Location: ../error.html");
    }
?>
