<?php

namespace Mailer;

class Template {

    /** @var   */
    private $view;

    /**
     * @var array
     */
    private $data;

    /**
     * Template constructor.
     * @param $view
     * @param array $data
     */
    public function __construct($view, array $data) {
        $this->view = $view;
        $this->data = $data;
    }

    /**
     * Render view
     * @return string
     * @internal param $view
     * @internal param array $data
     */
    public function render() {
        list($view, $plain, $raw) = $this->parseView($this->view);

        $content = $plain;
        if(! is_null($view)) {
            $content = $view;
        } elseif ( ! is_null($raw) ) {
            $content = $raw;
        }

        return $content;
    }

    /**
     * Parse view .
     *
     * @param $view
     * @return array
     */
    protected function parseView($view) {
        if( is_string($view) ) {
            return array(
                $view, null, null
            );
        }

        if( is_array($view) ) {
            return array(
                isset($view['html']) ? $view['html'] : null,
                isset($view['text']) ? $view['text'] : null,
                isset($view['raw']) ? $view['raw'] : null
            );
        }
    }

    /**
     * Get type data .
     *
     * @return string
     */
    public function type() {
        list($view, $plain, $raw) = $this->parseView($this->view);

        if( isset($view) ) {
            return 'text/html';
        } elseif ( isset($plain) ) {
            return 'text/plain';
        } elseif( isset($raw) ) {
            return 'text/raw';
        }
    }
}