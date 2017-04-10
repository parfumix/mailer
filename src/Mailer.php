<?php

namespace Mailer;

use Mailer\Exceptions\InvalidDriverException;

class Mailer {

    /** @var   */
    protected $swiftMailer;

    /**
     * @var
     */
    protected $config;

    /**
     * @var
     */
    protected $message;

    /**
     * @var
     */
    protected $transport;

    /** @var   */
    protected $from;

    /** @var   */
    protected $replyTo;

    /** @var   */
    protected $to;

    public function __construct(array $config = array()) {
        $this->config = $config;
    }

    /**
     * Send email to ?
     *
     * @param $to
     * @param bool $setFrom
     * @return Mailer
     * @internal param null $subject
     */
    public static function to($to, $setFrom = true) {
        $mailer = (new self)->createMessage($to);

        if( $setFrom ) {
            $from = is_array($to) ? array_pop($to) : $to;

            $mailer->message->setFrom($from);
        }

        return $mailer;
    }


    /**
     * Set the global from address and name.
     *
     * @param  string $address
     * @param  string|null $name
     * @return $this
     */
    public function alwaysFrom($address, $name = null) {
        $this->from = compact('address', 'name');

        return $this;
    }

    /**
     * Set the global reply-to address and name.
     *
     * @param  string $address
     * @param  string|null $name
     * @return $this
     */
    public function alwaysReplyTo($address, $name = null) {
        $this->replyTo = compact('address', 'name');

        return $this;
    }

    /**
     * Set the global to address and name.
     *
     * @param  string $address
     * @param  string|null $name
     * @return $this
     */
    public function alwaysTo($address, $name = null) {
        $this->to = compact('address', 'name');

        return $this;
    }


    /**
     * Send message .
     *
     * @param $view
     * @param array $data
     * @param \Closure|null $callback
     * @return int
     */
    public function send($view, $data = array(), \Closure $callback = null) {
        if( $view instanceof Mailable) {

            return $view->send($this);
        }

        $message = $this->message->setBody(
            (new Template($view, (array)$data))
        );

        if(! is_null($callback))
            call_user_func($callback, $message);

        if( isset($this->to['address']) ) {
            $message->to($this->to['address'], $this->to['name'], true);
            $message->cc($this->to['address'], $this->to['name'], true);
            $message->bcc($this->to['address'], $this->to['name'], true);
        }

        try {
            return $this->getSwiftMailer()->send( $message->getSwiftMessage() );
        } finally {
            $this->getSwiftMailer()->getTransport()->stop();
        }
    }

    /**
     * Change default driver .
     *
     * @param $name
     * @return $this
     * @throws InvalidDriverException
     */
    public function with($name) {
        if(! $this->getTransport()->driver( $name ))
            throw new InvalidDriverException('Invalid driver');

        $this->getTransport()->setDefaultDriver( $name );

        return $this;
    }


    /**
     * @param $to
     * @param null $subject
     * @return mixed
     */
    protected function createMessage($to, $subject = null) {
        if(! $this->message) {
            $this->message = new Message($to, $subject);

            if (! empty($this->from['address'])) {
                $this->message->from($this->from['address'], $this->from['name']);
            }

            // When a global reply address was specified we will set this on every message
            // instances so the developer does not have to repeat themselves every time
            // they create a new message. We will just go ahead and push the address.
            if (! empty($this->replyTo['address'])) {
                $this->message->replyTo($this->replyTo['address'], $this->replyTo['name']);
            }
        }

        return $this;
    }

    /**
     * Get transport instance
     *
     * @return Transport
     */
    protected function getTransport() {
        if(! $this->transport)
            $this->transport = new Transport(
                $this->getConfig()
            );

        return $this->transport;
    }

    /**
     * Get swift mailer instance .
     * @return \Swift_Mailer
     * @internal param array $config
     */
    protected function getSwiftMailer() {
        if(! $this->swiftMailer)
            $this->swiftMailer = new \Swift_Mailer(
                $this->getTransport()->driver()
            );

        return $this->swiftMailer;
    }


    /**
     * Set config .
     *
     * @param array $config
     * @return $this
     */
    public function setConfig(array $config) {
        $this->config = $config;

        return $this;
    }

    /**
     * Get configuration .
     *
     * @return array
     */
    public function getConfig() {
        return array_merge($this->config, array(
            'driver' => env('MAIL_DRIVER'),
            'host' => env('MAIL_HOST'),
            'port' => env('MAIL_PORT'),
            'username' => env('MAIL_USERNAME'),
            'password' => env('MAIL_PASSWORD'),
            'encryption' => env('MAIL_ENCRYPTION'),
        ));
    }

}