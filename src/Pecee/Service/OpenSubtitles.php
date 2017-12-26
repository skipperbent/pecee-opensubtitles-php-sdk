<?php

namespace Pecee\Service;

use Pecee\Service\OpenSubtitles\Exception;
use Pecee\Service\OpenSubtitles\Response;

class OpenSubtitles
{
    /**
     * Endpoint
     *
     * @var string
     */
    const SERVICE_ENDPOINT = 'http://api.opensubtitles.org/xml-rpc';

    /**
     * Useragent
     *
     * @var string
     * @link https://trac.opensubtitles.org/projects/opensubtitles/wiki/DevReadFirst
     */
    protected $userAgent = 'OSTestUserAgent';

    /**
     * Language (ISO639-1)
     *
     * @var string
     */
    protected $language = 'en';

    /**
     * Username
     *
     * @var string
     */
    protected $username;

    /**
     * Password
     *
     * @var string
     */
    protected $password;

    /**
     * Token
     *
     * @var string
     */
    protected $token;

    /**
     * Create client instance
     *
     * @param  array $options
     * @return static
     * @throws Exception
     */
    public static function create(array $options)
    {
        return new static($options);
    }

    /**
     * Constructor
     *
     * @param array $options
     * @throws Exception
     */
    protected function __construct(array $options)
    {
        foreach ($options as $option => $value) {
            $this->{$option} = $value;
        }

        if ($this->username === null || $this->password === null) {
            throw new Exception('Missing username or password');
        }
    }

    /**
     * Destructor
     */
    public function __destruct()
    {
        if ($this->token !== null) {
            $this->logOut($this->token);
        }
    }

    /**
     * Obtain token
     *
     * @return string
     */
    public function obtainToken()
    {
        if ($this->token !== null) {
            return $this->token;
        }
        $response = $this->logIn(
            $this->username,
            $this->password,
            $this->language,
            $this->userAgent
        )->toArray();

        $this->token = $response['token'];

        return $this->token;
    }

    /**
     * Build XML-RPC request
     *
     * @param  string $method
     * @param  array $params
     * @return string
     */
    public function buildRequest($method, array $params = [])
    {
        $request = xmlrpc_encode_request($method, $params, [
            'encoding' => 'UTF-8',
        ]);

        return $request;
    }

    /**
     * Send XML-RPC request
     *
     * @param  string $request
     * @return array
     * @throws Exception
     */
    public function sendRequest($request)
    {
        $context = stream_context_create([
            'http' => [
                'method'  => 'POST',
                'header'  => 'Content-Type: text/xml',
                'content' => $request,
            ],
        ]);
        $file = file_get_contents(static::SERVICE_ENDPOINT, false, $context);
        $response = xmlrpc_decode($file, 'UTF-8');
        if (is_array($response) && xmlrpc_is_fault($response)) {
            throw new Exception($response['faultString'], $response['faultCode']);
        }
        if (empty($response['status']) || $response['status'] !== '200 OK') {
            throw new Exception('Invalid response status');
        }

        return $response;
    }

    /**
     * Call API method
     *
     * @param  string $method
     * @param  array $params
     * @return Response
     * @throws Exception
     */
    public function __call($method, array $params = [])
    {
        $method = ucfirst($method);
        if (!in_array($method, [
            'ServerInfo',
            'LogIn',
            'LogOut',
        ], true)) {
            $token = $this->obtainToken();
            array_unshift($params, $token);
        }
        $request = $this->buildRequest($method, $params);
        $response = $this->sendRequest($request);

        return new Response($method, $response);
    }
}
