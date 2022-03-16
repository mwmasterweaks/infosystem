<?php

namespace App\Helpers;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;
use Carbon\Carbon;
use Borisuu\Telnet\TelnetClient;

class Logger
{

    //Register this Logger.php into config/app.php file
    public function log($date, $userID, $userName, $ControllerName, $functionName, $logType, $message)
    {
        $filenameDate = date("Ym");
        $myfile = fopen("logs/log" . $filenameDate . ".txt", "a") or die("Unable to open file!");
        $txt = $date . "\t--\t" . $userID . "\t--\t" . $userName . "\t--\t" . $ControllerName .
            "\t--\t" . $functionName . "\t--\t" . $logType . "\t--\t" . $message . "\n\n";
        fwrite($myfile, $txt);
        fclose($myfile);
        return "ok";
    }

    public function logError($date, $userID, $userName, $ControllerName, $functionName, $logType, $message)
    {
        $filenameDate = date("Ym");
        $myfile = fopen("logs/ErrorLog" . $filenameDate . ".txt", "a") or die("Unable to open file!");
        $txt = $date . "\t--\t" . $userID . "\t--\t" . $userName . "\t--\t" . $ControllerName .
            "\t--\t" . $functionName . "\t--\t" . $logType . "\t--\t" . $message . "\n\n";
        fwrite($myfile, $txt);
        fclose($myfile);
        return "ok";
    }

    public function logText($date, $userID, $userName, $ControllerName, $functionName, $logType, $message)
    {
        $filenameDate = date("Ymd");
        $myfile = fopen("logs/TextLogs" . $filenameDate . ".txt", "a") or die("Unable to open file!");
        $txt = $date . "\t--\t" . $userID . "\t--\t" . $userName . "\t--\t" . $ControllerName .
            "\t--\t" . $functionName . "\t--\t" . $logType . "\t--\t" . $message . "\n\n";
        fwrite($myfile, $txt);
        fclose($myfile);
        return "ok";
    }

    public function mailerZimbra($subject, $message, $sender, $senderName, $sendTo, $CCTO)
    {
        $mail = new PHPMailer(true);
        $mail->SMTPDebug = 0;
        $mail->isSMTP();

        $mail->Host = 'appmta.dctechmicro.com'; //'smtp.gmail.com';
        $mail->SMTPAuth = false;
        $mail->SMTPAutoTLS = false;
        $mail->SMTPSecure = false;
        $mail->Port = 25;
        $mail->CharSet = 'utf-8';
        $mail->SMTPOptions = array(
            'ssl' => array(
                'verify_peer' => false,
                'verify_peer_name' => false,
                'allow_self_signed' => true
            )
        );
        $mail->setFrom('customeractivation@dctechmicro.com', "DCTECH Mailer");
        if ($sendTo != null)
            foreach ($sendTo as $item) {
                $item = (object) $item;
                $mail->addAddress($item->email, $item->name);
            }
        if ($CCTO != null)
            foreach ($CCTO as $item) {
                $item = (object) $item;
                $mail->addCC($item->email, $item->name);
            }
        $mail->isHTML(true);  // Set email format to HTML
        $mail->Subject = $subject;
        $mail->Body    = $message;

        if (!$mail->send()) {
            return 'Mailer Error: ' . $mail->ErrorInfo;
        } else {
            return 'ok';
        }
    }

    public function mailer($subject, $message, $sender, $senderName, $sendTo, $CCTO)
    {
        $mail = new PHPMailer(true);
        $mail->SMTPDebug = 0;
        //$mail->SMTPDebug = SMTP::DEBUG_SERVER;
        $mail->isSMTP();

        $mail->Host = 'appmta.dctechmicro.com'; //'smtp.gmail.com';
        $mail->SMTPAuth = false;
        //$mail->Username = 'customeractivation@dctechmicro.com';
        //$mail->Password = 'dctech123';
        //$mail->SMTPSecure = 'tls';
        $mail->SMTPAutoTLS = false;
        $mail->SMTPSecure = false;
        $mail->Port = 25;
        $mail->CharSet = 'utf-8';
        $mail->SMTPOptions = array(
            'ssl' => array(
                'verify_peer' => false,
                'verify_peer_name' => false,
                'allow_self_signed' => true
            )
        );
        $mail->setFrom('customeractivation@dctechmicro.com', "DCTECH Mailer");
        if ($sendTo != null)
            foreach ($sendTo as $item) {
                $item = (object) $item;
                $mail->addAddress($item->email, $item->name);
            }
        if ($CCTO != null)
            foreach ($CCTO as $item) {
                $item = (object) $item;
                $mail->addCC($item->email, $item->name);
            }

        //$mail->addReplyTo($request->email, 'Mailer');
        //$mail->addCC('pbismonte@dctechmicro.com');
        //$mail->addBCC('his-her-email@gmail.com');
        //Attachments (optional)
        // $mail->addAttachment('/var/tmp/file.tar.gz');			// Add attachments
        // $mail->addAttachment('/tmp/image.jpg', 'new.jpg');	// Optional name

        $mail->isHTML(true);                                                                     // Set email format to HTML
        $mail->Subject = $subject;
        $mail->Body    = $message;

        if (!$mail->send()) {
            return 'Mailer Error: ' . $mail->ErrorInfo;
        } else {
            return 'ok';
        }
    }

    public function mailerGmail($subject, $message, $sender, $senderName, $sendTo, $CCTO)
    {
        $mail = new PHPMailer();
        // Server settings
        //$mail->SMTPDebug = SMTP::DEBUG_SERVER;   // mag return ni ug shit
        $mail->SMTPDebug = 1;
        $mail->isSMTP();

        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPSecure = "ssl";
        $mail->SMTPSecure = "tls";
        $mail->SMTPAuth = true;
        $mail->Port = 465;
        $mail->Username = 'mwmasterweaks@gmail.com';
        $mail->Password = 'mweakthegenius';

        // $mail->CharSet = 'utf-8';
        // $mail->SMTPOptions = array(
        //     'ssl' => array(
        //         'verify_peer' => false,
        //         'verify_peer_name' => false,
        //         'allow_self_signed' => true
        //     )
        // );

        $mail->setFrom('r11cnc.dctech@gmail.com', "DCTECH Mailer");
        if ($sendTo != null)
            foreach ($sendTo as $item) {
                $item = (object) $item;
                $mail->addAddress($item->email, $item->name);
            }
        if ($CCTO != null)
            foreach ($CCTO as $item) {
                $item = (object) $item;
                $mail->addCC($item->email, $item->name);
            }

        //$mail->addReplyTo($request->email, 'Mailer');
        //$mail->addCC('pbismonte@dctechmicro.com');
        //$mail->addBCC('his-her-email@gmail.com');

        //Attachments (optional)
        // $mail->addAttachment('/var/tmp/file.tar.gz');			// Add attachments
        // $mail->addAttachment('/tmp/image.jpg', 'new.jpg');	// Optional name

        $mail->isHTML(true);                                                                     // Set email format to HTML
        $mail->Subject = $subject;
        $mail->Body    = $message;

        if (!$mail->send()) {
            return 'Mailer Error: ' . $mail->ErrorInfo;
        } else {
            return 'ok';
        }
        // if (!$mail->send()) {
        //     return 'Mailer Error: ' . $mail->ErrorInfo;
        // } else {
        //     return 'Message sent!';
        //     //Section 2: IMAP
        //     //Uncomment these to save your message in the 'Sent Mail' folder.
        //     #if (save_mail($mail)) {
        //     #    echo "Message saved!";
        //     #}
        // }
    }

    public function mailerUser($subject, $message, $senderEmail, $senderPass, $sendTo, $CCTO)
    {
        $mail = new PHPMailer(true);
        $mail->SMTPDebug = 0;
        $mail->isSMTP();
        if (strpos($senderEmail, '@dctechmicro.com') !== false) {
            $mail->Host = 'mail.dctechmicro.com';
            $mail->Port = 25;
            $mail->CharSet = 'utf-8';
            $mail->SMTPOptions = array(
                'ssl' => array(
                    'verify_peer' => false,
                    'verify_peer_name' => false,
                    'allow_self_signed' => true
                )
            );
        } else {
            $mail->Host = 'smtp.gmail.com';
            $mail->Port = 587;
        }

        $mail->SMTPAuth = true;
        $mail->Username = $senderEmail;
        $mail->Password = $senderPass;
        $mail->SMTPSecure = 'tls';

        $mail->setFrom($senderEmail, "");
        if ($sendTo != null)
            foreach ($sendTo as $item) {
                $item = (object) $item;
                $mail->addAddress($item->email, $item->name);
            }
        if ($CCTO != null)
            foreach ($CCTO as $item) {
                $item = (object) $item;
                $mail->addCC($item->email, $item->name);
            }
        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body    = $message;
        $mail->send();
    }

    public function send_text($number, $msg)
    {
        $cname = "SentMessages";
        //2488
        //local no port
        $smsgatewaydata = "http://sms.dctechmicro.com:2488/index.php?" .
            "app=ws&u=marketing&h=7d6190ad6afde3a9f45683d9600d5dc8&op=pv&to=" .
            $number . "&msg=" . $msg;
        $url = $smsgatewaydata;
        //7d6190ad6afde3a9f45683d9600d5dc8
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $ret = curl_exec($ch); //get error
        curl_close($ch);
        // \Logger::instance()->logText(
        //     Carbon::now(),
        //     "",
        //     "",
        //     $cname,
        //     "store",
        //     "message",
        //     "Sent Message: " . $ret
        // );
        if (!$ret) {
            return file_get_contents($smsgatewaydata);
        }
        return $ret;
    }
    public function test()
    {
        $connection = ssh2_connect('hosting-new.dctechmicro.com', 22);
        if (!$connection) {
            return "nect";
        } else {
            ssh2_auth_password($connection, 'root', '8tnhbaa0');
            $stream = ssh2_exec($connection, 'php -m');
            stream_set_blocking($stream, true);
            $data = "";
            while ($buf = fread($stream, 4096)) {
                $data .= $buf;
            }
            fclose($stream);
            return $data;
            //return strval($stream);
        }
    }

    public static function instance()
    {
        return new Logger();
    }

    public function telnet_test()
    {
        TelnetClient::setDebug(true);
        $telnet = new TelnetClient('127.0.0.1', 23);
        $telnet->connect();
        $telnet->setPrompt('$');
        $telnet->login('telnetuser', 'weak');

        $cmdResult = $telnet->exec('ls /');

        $telnet->disconnect();

        print("The contents of / is: \"{$cmdResult}\"\n");
    }
}
