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