<?php

namespace Pecee\Service\OpenSubtitles;

class Response implements \JsonSerializable
{
    /**
     * Response array
     *
     * @var array
     */
    protected $response;

    /**
     * Method
     *
     * @var string
     */
    protected $method;

    /**
     * Constructor
     *
     * @param string $method
     * @param array $response
     */
    public function __construct($method, $response)
    {
        $this->method = $method;
        $this->response = $response;
    }

    /**
     * Return response as array
     *
     * @return array
     */
    public function toArray()
    {
        return $this->response;
    }

    /**
     * Specify data which should be serialized to JSON
     * @link http://php.net/manual/en/jsonserializable.jsonserialize.php
     * @return mixed data which can be serialized by <b>json_encode</b>,
     * which is a value of any type other than a resource.
     * @since 5.4.0
     */
    public function jsonSerialize()
    {
        return $this->toArray();
    }
}
