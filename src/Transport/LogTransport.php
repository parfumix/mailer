<?php

namespace Mailer\Transport;

use Swift_Mime_Message;

class LogTransport extends Transport
    implements TransportAble {

    /**
     * Create a new array transport instance.
     *
     */
    public function __construct() {

    }

    /**
     * {@inheritdoc}
     */
    public function send(Swift_Mime_Message $message, &$failedRecipients = null) {
        $this->beforeSendPerformed($message);

        return $this->numberOfRecipients($message);
    }

}