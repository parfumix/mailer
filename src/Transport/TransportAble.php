<?php

namespace Mailer\Transport;

use Swift_Mime_Message;

interface TransportAble {

    /**
     * Send message .
     *
     * @param Swift_Mime_Message $message
     * @param null $failedRecipients
     * @return mixed
     */
    public function send(Swift_Mime_Message $message, &$failedRecipients = null);
}
