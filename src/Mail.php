<?php

namespace Mailer;

use ReflectionClass;
use ReflectionProperty;

abstract class Mail {

    /**
     * @var
     */
    protected $view;

    /**
     * @var
     */
    protected $viewData = array();

    /** @var */
    protected $from;

    /** @var */
    protected $to;

    /** @var */
    protected $subject;

    /** @var  */
    protected $attachments;

    /** @var   */
    protected $rawAttachments;

    public function __construct($subject = null, $to = null) {
        $this->to($to)->subject($subject);
    }

    /**
     * Send mail
     *
     * @param Mailer $mailer
     * @return int
     */
    public function send(Mailer $mailer) {
        call_user_func_array(array($this, 'build'), array($mailer));

        return $mailer->send($this->buildView(), $this->buildViewData(), function ($message) {
            $this->buildFrom($message)
                ->buildTo($message)
                ->buildSubject($message)
                ->buildAttachments($message);
        });
    }


    /**
     * Build view
     *
     * @return array
     */
    public function buildView() {
        if (isset($this->view)) {
            return ['html' => $this->view];
        } elseif (isset($this->textView)) {
            return ['text' => $this->textView];
        }

        return $this->view;
    }

    /**
     * Build view data .
     *
     * @return mixed
     */
    public function buildViewData() {
        $data = $this->viewData;

        foreach ((new ReflectionClass($this))->getProperties(ReflectionProperty::IS_PUBLIC) as $property) {
            if ($property->getDeclaringClass()->getName() != self::class) {
                $data[$property->getName()] = $property->getValue($this);
            }
        }

        return (array)$data;
    }

    /**
     * Build message :from
     *
     * @param Message $message
     * @return $this
     */
    public function buildFrom(Message $message) {
        if(! is_null($this->from)) {
            $message->setFrom($this->from);
        }

        return $this;
    }

    /**
     * Build to :to
     *
     * @param Message $message
     * @return $this
     */
    public function buildTo(Message $message) {
        isset($this->to)
            ? $message->setTo($this->to)
            : null;

        return $this;
    }

    /**
     * Build subject
     *
     * @param Message $message
     * @return $this
     */
    public function buildSubject(Message $message) {
        if(! is_null($this->subject))
            $message->setSubject($this->subject);

        return $this;
    }

    /**
     * Build attachments
     *
     * @param Message $message
     * @return $this
     */
    public function buildAttachments(Message $message) {
        foreach ($this->attachments as $attachment) {
            $message->attach($attachment['file'], $attachment['options']);
        }

        foreach ($this->rawAttachments as $attachment) {
            $message->attachData(
                $attachment['data'], $attachment['name'], $attachment['options']
            );
        }

        return $this;
    }


    /**
     * Set from
     *
     * @param $from
     * @return $this
     */
    public function from($from) {
        $this->from = $from;

        return $this;
    }

    /**
     * Set to .
     *
     * @param $to
     * @return $this
     */
    public function to($to) {
        $this->to = $to;

        return $this;
    }

    /**
     * Set subject .
     *
     * @param $subject
     * @return $this
     */
    public function subject($subject) {
        $this->subject = $subject;

        return $this;
    }

    /**
     * Set the view data for the message.
     *
     * @param  string|array $key
     * @param  mixed $value
     * @return $this
     */
    public function with($key, $value = null) {
        if (is_array($key)) {
            $this->viewData = array_merge($this->viewData, $key);
        } else {
            $this->viewData[$key] = $value;
        }

        return $this;
    }

    /**
     * Attach a file to the message.
     *
     * @param  string $file
     * @param  array $options
     * @return $this
     */
    public function attach($file, array $options = []) {
        $this->attachments[] = compact('file', 'options');

        return $this;
    }

    /**
     * Attach in-memory data as an attachment.
     *
     * @param  string $data
     * @param  string $name
     * @param  array $options
     * @return $this
     */
    public function attachData($data, $name, array $options = []) {
        $this->rawAttachments[] = compact('data', 'name', 'options');

        return $this;
    }

    /**
     * @param $view
     * @param array $viewData
     * @return $this
     */
    public function view($view, array $viewData = []) {
        $this->view = $view;

        $this->viewData = $viewData;

        return $this;
    }

    /**
     * Set the plain text view for the message.
     *
     * @param  string $textView
     * @param  array $data
     * @return $this
     */
    public function text($textView, array $data = []) {
        $this->textView = $textView;

        $this->viewData = array_merge($this->viewData, $data);

        return $this;
    }

}