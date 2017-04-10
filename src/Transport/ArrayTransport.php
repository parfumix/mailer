<?php

namespace Mailer\Transport;

use Swift_Mime_Message;

class ArrayTransport extends Transport
    implements TransportAble {

    /**
     * The collection of Swift Messages.
     *
     */
    protected $messages;

    /**
     * Create a new array transport instance.
     *
     */
    public function __construct() {
        $this->messages = [];
    }

    /**
     * {@inheritdoc}
     */
    public function send(Swift_Mime_Message $message, &$failedRecipients = null) {
        $this->beforeSendPerformed($message);

        $this->messages[] = $message;

        return $this->numberOfRecipients($message);
    }

    /**
     * Retrieve the collection of messages.
     *
     */
    public function messages() {
        return $this->messages;
    }

    /**
     * Clear all of the messages from the local collection.
     *
     */
    public function flush() {
        return $this->messages = [];
    }
}