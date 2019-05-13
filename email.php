<?php
	//PHP Email Sender
	//Created by Maxime Princelle

	//error_reporting(-1);
	//ini_set('display_errors', true);

	header('Access-Control-Allow-Origin: *');
	header('Content-Type: application/json');

    require 'PHPMailer/src/PHPMailer.php';
    require 'PHPMailer/src/Exception.php';
    require 'PHPMailer/src/SMTP.php';

    use PHPMailer\PHPMailer\PHPMailer;
	use PHPMailer\PHPMailer\Exception;

	$sender="";

	$namexp="";
	$mailexp="";
	$subject="";
	$message="";

	function urlToDomain($url) {
		return implode(array_slice(explode('/', preg_replace('/https?:\/\/(www\.)?/', '', $url)), 0, 1));
	}

	function getOrigin() {
		$origin = urlToDomain($_SERVER['HTTP_ORIGIN']);
		return $origin;
	}

	//Responses
	function res_spam(){
		header("Location: ./error.html");
        exit();
	}
	
	function res_success(){
		header("Location: ./success.html");
        exit();
	}

	function res_error(){
		if (getOrigin() === "homeprince.princelle.org") {
			header("Location: ./error.html");

		} else {
			echo "You are not authorized to use this API.";
		}
        exit();
	}

	function get_inputs(){
		if (getOrigin() === "homeprince.princelle.org") {

				$GLOBALS['sender'] = 'Formulaire - homeprince.princelle.org';

				$GLOBALS['namexp'] = $_POST['name'];
				$GLOBALS['mailexp'] = $_POST['emailcontact'];
				$GLOBALS['subject'] = $_POST['subject'];
				$GLOBALS['message'] = $_POST['message'];

		} else {
			res_error();
		}
	}

	//Check origin and redirect
	function check_origin(){
		if (getOrigin() === "homeprince.princelle.org") {
			return true;
		}
		return false;
	}

    //Check if email exists
    function conform_mail(){
		$domain = explode("@", $GLOBALS['mailexp'], 2);
		return checkdnsrr($domain[1]);
    }

    //Récupération de l'adresse IP
    function getRealIpAddr() {  
        if (!empty($_SERVER['HTTP_CLIENT_IP'])) {  //check ip from share internet
            $ip=$_SERVER['HTTP_CLIENT_IP'];
        } 
        elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {  //to check ip is pass from proxy
            $ip=$_SERVER['HTTP_X_FORWARDED_FOR'];
        } 
        else {
            $ip=$_SERVER['REMOTE_ADDR'];
        }

        return $ip;
    }

    //Fonction envoi mail
    function sendMail(){
        $g_namexp = $GLOBALS['namexp'];
        $g_mailexp = $GLOBALS['mailexp'];
        $g_subject = $GLOBALS['subject'];
        $g_message = $GLOBALS['message'];

        $ip = getRealIpAddr();

        $g_sender = $GLOBALS['sender'];

        if ( ($g_namexp == "" | $g_subject == "" | $g_message == "" | $g_mailexp == "") ) {
			res_error();
        }

        $to = "princellemaxime@gmail.com";
        $sub = "Msg de $g_namexp : $g_subject";
        $contenu = "<b>Nom expéditeur: </b> $g_namexp <br><br> <b>Email expéditeur: </b> $g_mailexp <br> <b>Adresse IP : </b> $ip <br><br> <b>Sujet: </b> $g_subject <br><br> <b>Message: </b><br> $g_message";

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
            $mail->setFrom('contact@princelle.org', $g_sender);
            $mail->addAddress($to, 'Contact - Maxime Princelle');
            $mail->AddReplyTo($g_mailexp, $g_namexp);

            //Content
            $mail->isHTML(true);                                  // Set email format to HTML
            $mail->Subject = $sub;

            $mail->Body = $contenu;
            $mail->AltBody = $contenu;

            $mail->send();
            
            res_success();
        }
        catch (Exception $e)
        {
            res_error();
        }
	}
	
	get_inputs();

    function check_conform(){
        if ( check_origin() && conform_mail() ){
            return true;
        }
        return false;
    }

    if( check_conform() ){
        sendMail();
    } else {
        res_spam();
    }
?>

