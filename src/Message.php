<?php

namespace Mailer;

use Swift_Attachment;

class Message {

    /**
     * @var
     */
    protected $swiftMessage;

    /**
     * Message constructor.
     * @param $to
     * @param null $subject
     */
    public function __construct($to, $subject = null) {

        $this->getSwiftMessage($subject)->setTo($to);

    }

    /**
     * Attach file .
     *
     * @param $path
     * @param $options
     * @return $this
     */
    public function attach($path, $options) {
        $this->swiftMessage->attach( Swift_Attachment::fromPath($path, $options) );

        return $this;
    }

    /**
     * Attach data.
     *
     */
    public function attachData() {}

    /**
     * Add a "from" address to the message.
     *
     * @param  string|array $address
     * @param  string|null $name
     * @return $this
     */
    public function from($address, $name = null) {
        $this->swiftMessage->setFrom($address, $name);

        return $this;
    }

    /**
     * Add a reply to address to the message.
     *
     * @param  string|array $address
     * @param  string|null $name
     * @return $this
     */
    public function replyTo($address, $name = null) {
        return $this->addAddresses($address, $name, 'ReplyTo');
    }

    /**
     * Add a recipient to the message.
     *
     * @param  string|array $address
     * @param  string|null $name
     * @param  bool $override
     * @return $this
     */
    public function to($address, $name = null, $override = false) {
        if ($override) {
            $this->swiftMessage->setTo($address, $name);

            return $this;
        }

        return $this->addAddresses($address, $name, 'To');
    }

    /**
     * Add a carbon copy to the message.
     *
     * @param  string|array $address
     * @param  string|null $name
     * @param  bool $override
     * @return $this
     */
    public function cc($address, $name = null, $override = false) {
        if ($override) {
            $this->swiftMessage->setCc($address, $name);

            return $this;
        }

        return $this->addAddresses($address, $name, 'Cc');
    }

    /**
     * Add a blind carbon copy to the message.
     *
     * @param  string|array $address
     * @param  string|null $name
     * @param  bool $override
     * @return $this
     */
    public function bcc($address, $name = null, $override = false) {
        if ($override) {
            $this->swiftMessage->setBcc($address, $name);

            return $this;
        }

        return $this->addAddresses($address, $name, 'Bcc');
    }


    /**
     * Add a recipient to the message.
     *
     * @param  string|array $address
     * @param  string $name
     * @param  string $type
     * @return $this
     */
    protected function addAddresses($address, $name, $type) {
        if (is_array($address)) {
            $this->swiftMessage->{"set{$type}"}($address, $name);
        } else {
            $this->swiftMessage->{"add{$type}"}($address, $name);
        }

        return $this;
    }

    /**
     * Set body .
     *
     * @param Template $template
     * @return $this
     */
    public function setBody(Template $template) {
        $this->swiftMessage->setBody( $template->render(), $template->type() );

        return $this;
    }

    /**
     * Get swift message instance
     *
     * @param null $subject
     * @param null $body
     * @return \Swift_Message
     */
    public function getSwiftMessage($subject = null, $body = null) {
        if(! $this->swiftMessage)
            $this->swiftMessage = new \Swift_Message($subject, $body);

        return $this->swiftMessage;
    }

    /**
     * @param $name
     * @param $arguments
     * @return mixed
     */
    public function __call($name, $arguments) {
        return call_user_func_array(array($this->swiftMessage, $name), $arguments);
    }

    /**
     * @return mixed
     */
    public function __toString() {
        return call_user_func_array(array($this->swiftMessage, '__toString'), func_get_args());

    }

}