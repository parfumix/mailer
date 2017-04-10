<?php

namespace Mailer\Transport;

use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use Swift_Mime_Message;
use Swift_Mime_MimeEntity;

class LogTransport extends Transport
    implements TransportAble {

    /** @var Logger  */
    protected $logger;

    /**
     * Create a new array transport instance.
     * @param $path
     */
    public function __construct($path) {
        $this->logger = (new Logger('mail_logger'))
            ->pushHandler(new StreamHandler($path, Logger::DEBUG) );
    }

    /**
     * {@inheritdoc}
     */
    public function send(Swift_Mime_Message $message, &$failedRecipients = null) {
        $this->beforeSendPerformed($message);

        $this->logger->debug( $this->getMimeEntityString($message) );

        return $this->numberOfRecipients($message);
    }

    /**
     * Format message .
     *
     * @param Swift_Mime_MimeEntity $entity
     * @return string
     */
    protected function getMimeEntityString(Swift_Mime_MimeEntity $entity) {
        $string = (string)$entity->getHeaders() . PHP_EOL . $entity->getBody();

        foreach ($entity->getChildren() as $children) {
            $string .= PHP_EOL . PHP_EOL . $this->getMimeEntityString($children);
        }

        return $string;
    }
}