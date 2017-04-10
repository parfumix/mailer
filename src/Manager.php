<?php

namespace Mailer;

abstract class Manager {

    /**
     * The array of created "drivers".
     *
     * @var array
     */
    protected $drivers = [];

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
        $method = 'create' . ucfirst($driver) . 'Driver';

        if (method_exists($this, $method))
            return $this->$method();

        throw new \Exception("Driver [$driver] not supported.");
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
