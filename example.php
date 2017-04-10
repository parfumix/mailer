<?php

use Mailer\Mailer;
use Mailer\Transport;

$config = array(
    'driver' => 'smtp',
    'host' => 'smtp.gmail.com',
    'port' => 465,
    'username' => 'you_email@gmail.com',
    'password' => 'your_passwd',
    'encryption' => 'ssl'
);

$log_config = array(
    'driver' => 'log',
    'log_path' => dirname(__FILE__) . '/storage/mail.log'
);

$mailer = (new Mailer(
    new Transport( $log_config )
))->alwaysFrom('from_email@gmail.com', 'Name')
    ->alwaysReplyTo('to_email@mail.ru', 'Name');

class NewPayment extends \Mailer\Mail
    implements \Mailer\Mailable {

    /** Build mailable class */
    public function build() {
        $this->from('from_email@gmail.com')
            ->text('My message');
    }
}

$mailer->to('to_email@gmail.com')->send(
    new NewPayment('Hello')
);

$mailer->to('to_email@gmail.com', 'Subject')->send('Message body', null, function (\Mailer\Message $message) {
    return $message->setFrom('other_from_email@gmail.com');
});
