<?php

namespace Mailer;

class Transport extends Manager {

    /**
     * @var array
     */
    private $config;

    public function __construct(array $config) {
        $this->config = $config;
    }

    /**
     * Create an instance of the SMTP Swift Transport driver.
     *
     * @return \Swift_SmtpTransport
     */
    protected function createSmtpDriver() {
        $transport = \Swift_SmtpTransport::newInstance(
            $this->config['host'], $this->config['port']
        );

        if (isset($this->config['encryption'])) {
            $transport->setEncryption($this->config['encryption']);
        }

        // Once we have the transport we will check for the presence of a username
        // and password. If we have it we will set the credentials on the Swift
        // transporter instance so that we'll properly authenticate delivery.
        if (isset($this->config['username'])) {
            $transport->setUsername($this->config['username']);

            $transport->setPassword($this->config['password']);
        }

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
            isset($this->config['sendmail']) ?: null
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
     * Create an instance of the Log Swift Transport driver.
     *
     */
    protected function createLogDriver() {
        #@todo add log driver
    }

    /**
     * Get the default mail driver name.
     *
     * @return string
     */
    public function getDefaultDriver() {
        return $this->config['driver'];
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
