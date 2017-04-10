# Mailer 

Mailer is a simple package based on SwiftMailer which allow sending messages with different drivers.

### Instalation
You can use the `composer` package manager to install. Either run:

    $ php composer.phar require parfumix/mailer "dev-master"

or add:

    "parfumix/mailer": "dev-master"

to your composer.json file

# Configuration

If you use DotEnv package all you have to do is to populat you ***.env*** file

```env
MAIL_DRIVER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=465
MAIL_USERNAME=your_gmail@gmail.com
MAIL_PASSWORD=your_password
MAIL_ENCRYPTION=ssl
```

If you use other storage repository you can create new Mailer instance and populate with config data

```php
    $mailer = 
```

# Usage

You can use directly ***Mailer*** and send messages by using ***::to('to_email@gmail.com')*** method

```php
Mailer::to('to_email@gmail.com')->send('This is a test message', null, function ($message) {
    return $message->setFrom('from_email@gmail.com');
});
```

or you can create ***Mailable*** classes which require ***build*** method

```php
class NewPayment extends \Mailer\Mail
    implements \Mailer\Mailable {

    public function build() {
        $this->text('Body message');
    }
}

Mailer::to(array(
    'to_email@gmail.com'
))->send( new NewPayment('Subject message') );
```


