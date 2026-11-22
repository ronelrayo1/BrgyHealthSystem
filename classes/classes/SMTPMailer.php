<?php
class SMTPMailer {
    private $host;
    private $port;
    private $username;
    private $password;
    private $encryption;
    private $fromEmail;
    private $fromName;

    public function __construct($host, $port, $username, $password, $encryption, $fromEmail, $fromName) {
        $this->host = $host;
        $this->port = $port;
        $this->username = $username;
        $this->password = $password;
        $this->encryption = $encryption;
        $this->fromEmail = $fromEmail;
        $this->fromName = $fromName;
    }

    public function send($to, $subject, $message) {
        ini_set('sendmail_from', $this->fromEmail);

        $headers = "From: " . $this->fromName . " <" . $this->fromEmail . ">\r\n";
        $headers .= "Reply-To: " . $this->fromEmail . "\r\n";
        $headers .= "MIME-Version: 1.0\r\n";
        $headers .= "Content-Type: text/plain; charset=UTF-8\r\n";
        $headers .= "X-Mailer: PHP/" . phpversion();

        $result = mail($to, $subject, $message, $headers, "-f" . $this->fromEmail);

        if (!$result) {
            error_log("Mail failed to send to: " . $to);
            error_log("Error: Last error message: " . error_get_last()['message'] ?? 'Unknown');
        }

        return $result;
    }
}
?>
