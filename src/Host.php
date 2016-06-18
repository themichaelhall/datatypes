<?php

namespace DataTypes;

use DataTypes\Interfaces\HostInterface;

/**
 * Class representing a host.
 */
class Host implements HostInterface
{
    /**
     * @return string The host as a string.
     */
    public function __toString()
    {
        return $this->myHost;
    }

    /**
     * Parses a host.
     *
     * @param string $host The host.
     *
     * @return HostInterface The Host instance.
     */
    public static function parse($host)
    {
        return new self($host);
    }

    /**
     * Constructs a host from host.
     *
     * @param string $host The host.
     */
    private function __construct($host)
    {
        $this->myHost = $host;
    }

    /**
     * @var string My host.
     */
    private $myHost;
}
