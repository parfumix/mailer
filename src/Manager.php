<?php

namespace Mailer;

use Mailer\Exceptions\InvalidDriverException;

abstract class Manager {

    /**
     * The array of created "drivers".
     *
     * @var array
     */
    protected $drivers = [];

    /**
     * Custom drivers
     *
     * @var array
     */
    protected $customCreators = [];

    /**
     * Get the default driver name.
     *
     * @return string
     */
    abstract public function getDefaultDriver();

    /**
     * Get a driver instance.
     *
     * @param  string $driver
     * @return mixed
     */
    public function driver($driver = null) {
        $driver = $driver ?: $this->getDefaultDriver();

        if (! isset($this->drivers[$driver]))
            $this->drivers[$driver] = $this->createDriver($driver);

        return $this->drivers[$driver];
    }

    /**
     * Create a new driver instance.
     *
     * @param  string $driver
     * @return mixed
     *
     * @throws \Exception
     */
    protected function createDriver($driver) {
        if (isset($this->customCreators[$driver])) {

            return $this->callCustomCreator($driver);
        } else {
            $method = 'create' . ucfirst($driver) . 'Driver';

            if (method_exists($this, $method))
                return $this->$method();
        }

        throw new InvalidDriverException("Driver [$driver] not supported.");
    }

    /**
     * Call a custom driver creator.
     *
     * @param  string $driver
     * @return mixed
     */
    protected function callCustomCreator($driver) {
        return $this->customCreators[$driver]($this);
    }

    /**
     * Register a custom driver creator Closure.
     *
     * @param  string $driver
     * @param  \Closure $callback
     * @return $this
     */
    public function extend($driver, \Closure $callback) {
        $this->customCreators[$driver] = $callback;

        return $this;
    }

    /**
     * Get all of the created "drivers".
     *
     * @return array
     */
    public function getDrivers() {
        return $this->drivers;
    }

    /**
     * Dynamically call the default driver instance.
     *
     * @param  string $method
     * @param  array $parameters
     * @return mixed
     */
    public function __call($method, $parameters) {
        return $this->driver()->$method(...$parameters);
    }
}
