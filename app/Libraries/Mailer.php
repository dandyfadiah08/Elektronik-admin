<?php

namespace App\Libraries;

use PHPMailer\PHPMailer\PHPMailer;

class Mailer
{
    protected $isDebugSend,$mailHost,$mailUsername,$mailPassword,$mailPort;
    public $senderEmail,$senderName,$isDebug,$isHTML = true;

    public function __construct()
    {
        $this->senderEmail = env('email.sender_email');
        $this->senderName = env('email.sender_name');
        $this->isDebug = env('email.debug');
        $this->isDebugSend = env('email.debug_send');
        $this->mailHost = env('email.host');
        $this->mailUsername = env('email.username');
        $this->mailPassword = env('email.password');
        $this->mailPort = env('email.port');
    }

    /*
    @param $data object
    @return $response object
    */
    public function send($data)
    {
        $response = (object)[
            'success' => false,
            'message' => "Receiver Email is required. "
        ];
        $receiverEmail = $data->receiverEmail ?? false;
        $receiverName = $data->receiverName ?? '';
        $subject = $data->subject ?? '';
        $content = $data->content ?? '';
        if($receiverEmail !== FALSE) {
            if(!$this->isDebugSend) {
                $mail = new PHPMailer;
                $mail->isSMTP();
                $mail->Host = $this->mailHost;
                $mail->Port = $this->mailPort;
                $mail->Username = $this->mailUsername;
                $mail->Password = $this->mailPassword;
                $mail->SMTPAuth = true;
                $mail->SMTPSecure = 'tls';
                if($this->isDebug) $mail->SMTPDebug = 2; // Aktifkan untuk melakukan debugging
                $mail->isHTML($this->isHTML);
                $mail->setFrom($this->senderEmail, $this->senderName);
                $mail->addAddress($receiverEmail, $receiverName);
                $mail->Subject = $subject;
                $mail->Body = $content;
                if ($mail->send()) {
                    $response->success = true;
                    $response->message = "Email successfully sent to $receiverEmail";
                } else {
                    $response->message = "Failed sending email to $receiverEmail";
                }
            } else {
                // debug, not really sends email
                $response->success = true;
                $response->message = "Email successfully sent to $receiverEmail";
            }
            // echo '<pre>';
            // var_dump($response);
            // var_dump($mail);
            // die;
        }
        return $response;
    }

    public function send_pdf($data, $pdf_path, $pdf_name)
    {
        $this->config = $data['config'];
        // $this->password = $this->bcDecrypt($this->password);
        $mail = new PHPMailer;
        $mail->isSMTP();
        $mail->Host = $this->mailHost;
        $mail->Username = $this->mailUsername;
        $mail->Password = $this->mailPassword;
        $mail->Port = 587;
        $mail->SMTPAuth = true;
        $mail->SMTPSecure = 'tls';
        if($this->isDebug) $mail->SMTPDebug = 2; // Aktifkan untuk melakukan debugging
        $mail->setFrom($this->senderEmail, $this->senderName);
        $mail->addAddress($data['receiverEmail'], '');
        $mail->isHTML(true); // Aktifkan jika isi emailnya berupa html
        $mail->Subject = $data['subjek'];
        $mail->Body = $data['content'];
        $mail->AddAttachment($pdf_path, $pdf_name,  'base64', 'application/pdf');
        $send = $mail->send();
        if ($send) { // Jika Email berhasil dikirim
            $response = array('status' => true, 'message' => 'Email berhasil dikirim ke '.$data['receiverEmail']);
        } else { // Jika Email Gagal dikirim
            $response = array('status' => false, 'message' => 'Email gagal dikirim ke '.$data['receiverEmail']);
        }
        // echo '<pre>';
        // var_dump($response);
        // var_dump($mail);
        // die;
        return $response;
    }

}
