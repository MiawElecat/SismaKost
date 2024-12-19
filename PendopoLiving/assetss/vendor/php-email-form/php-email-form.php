<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Load PHPMailer library
require __DIR__ . '/../../../PHPMailer-master/src/Exception.php';
require __DIR__ . '/../../../PHPMailer-master/src/PHPMailer.php';
require __DIR__ . '/../../../PHPMailer-master/src/SMTP.php';

class PHP_Email_Form {
    public $to;
    public $from_name;
    public $from_email;
    public $subject;
    public $smtp = []; // SMTP Configuration
    public $messages = []; // Message Content
    public $ajax = false; // Use AJAX if necessary

    public function add_message($content, $key = '', $priority = 0) {
        $this->messages[] = [
            'content' => $content,
            'key' => $key,
            'priority' => $priority
        ];
    }

    public function send() {
        $email_body = $this->compose_email();
        return $this->send_via_smtp($email_body); // Force SMTP usage
    }

    private function compose_email() {
        $body = "You have received a new message:\n\n";
        foreach ($this->messages as $message) {
            if (!empty($message['key'])) {
                $body .= $message['key'] . ": ";
            }
            $body .= $message['content'] . "\n";
        }
        return $body;
    }

    private function send_via_smtp($email_body) {
      $mail = new PHPMailer(true);
    
      try {
          // Configure SMTP server
          $mail->isSMTP();
          $mail->Host = $this->smtp['host']; 
          $mail->SMTPAuth = true;
          $mail->Username = $this->smtp['username'];
          $mail->Password = $this->smtp['password'];
          $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
          $mail->Port = $this->smtp['port'];
    
          // Configure email
          $mail->setFrom($this->from_email, $this->from_name);
          $mail->addAddress($this->to);
          $mail->Subject = $this->subject;
          $mail->Body = $email_body;
    
          // Send email
          $mail->send();
    // Cek skrip yang digunakan dan kembalikan pesan yang sesuai
    if (basename($_SERVER['PHP_SELF']) == 'contact.php') {
        return 'Your message has been sent. Thank you!';
    } elseif (basename($_SERVER['PHP_SELF']) == 'newsletter.php') {
        return 'Your subscription request has been sent. Thank you!';
    }
} catch (Exception $e) {
    return "SMTP Error: {$mail->ErrorInfo}";
}
    }    
  }

?>
