<?php

namespace Mailer;

use Mailer\Transport\ArrayTransport;
use Mailer\Transport\LogTransport;

class Transport extends Manager {

    /**
     * @var array
     */
    private $config;

    public function __construct(array $config = array()) {
        $this->config = $config;
    }

    /**
     * Create an instance of the SMTP Swift Transport driver.
     *
     * @return \Swift_SmtpTransport
     */
    protected function createSmtpDriver() {
        $host = isset($this->config['host'])
            ? $this->config['host']
            : ( isset($_ENV['MAIL_HOST']) ? $_ENV['MAIL_HOST'] : null );

        $port = isset($this->config['port'])
            ? $this->config['port']
            : ( isset($_ENV['MAIL_PORT']) ? $_ENV['MAIL_PORT'] : null );

        $transport = \Swift_SmtpTransport::newInstance($host, $port);

        $encryption = isset($this->config['encryption'])
            ? $this->config['encryption']
            : ( isset($_ENV['MAIL_ENCRYPTION']) ? $_ENV['MAIL_ENCRYPTION'] : null );

        if ($encryption)
            $transport->setEncryption($encryption);

        // Once we have the transport we will check for the presence of a username
        // and password. If we have it we will set the credentials on the Swift
        // transporter instance so that we'll properly authenticate delivery.
        $username = isset($this->config['username'])
            ? $this->config['username']
            : (isset($_ENV['MAIL_USERNAME']) ? $_ENV['MAIL_USERNAME'] : null);

        $password = isset($this->config['password'])
            ? $this->config['password']
            : (isset($_ENV['MAIL_PASSWORD']) ? $_ENV['MAIL_PASSWORD'] : null);

        if( $username )
            $transport->setUsername($username);

        if( $password )
            $transport->setPassword($password);

        // Next we will set any stream context options specified for the transport
        // and then return it. The option is not required any may not be inside
        // the configuration array at all so we'll verify that before adding.
        if (isset($this->config['stream'])) {
            $transport->setStreamOptions($this->config['stream']);
        }

        return $transport;
    }

    /**
     * Create an instance of the Sendmail Swift Transport driver.
     *
     * @return \Swift_SendmailTransport
     */
    protected function createSendmailDriver() {
        return \Swift_SendmailTransport::newInstance(
            isset($this->config['command']) ?: isset($_ENV['MAIL_COMMAND']) ? $_ENV['MAIL_COMMAND'] : null
        );
    }

    /**
     * Create an instance of the Mail Swift Transport driver.
     *
     * @return \Swift_MailTransport
     */
    protected function createMailDriver() {
        return \Swift_MailTransport::newInstance();
    }

    /**
     * Create array driver .
     *
     * @return ArrayTransport
     */
    protected function createArrayDriver() {
        return new ArrayTransport;
    }

    /**
     * Create an instance of the Log Swift Transport driver.
     *
     */
    protected function createLogDriver() {
        return new LogTransport(
            isset($this->config['log_path'])
                ? $this->config['log_path']
                : (isset($_ENV['MAIL_LOG_PATH']) ? $_ENV['MAIL_LOG_PATH'] : null)
        );
    }

    /**
     * Get the default mail driver name.
     *
     * @return string
     */
    public function getDefaultDriver() {
        return isset($this->config['driver'])
            ? $this->config['driver']
            : (isset($_ENV['MAIL_DRIVER']) ? $_ENV['MAIL_DRIVER'] : null);
    }

    /**
     * Set the default mail driver name.
     *
     * @param  string $name
     * @return void
     */
    public function setDefaultDriver($name) {
        $this->config['driver'] = $name;
    }
}
