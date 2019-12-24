<?php
namespace Discourse;

use Discourse\Exceptions\DiscourseSDKException;
use Discourse\Http\RequestBodyUrlEncoded;
use Discourse\Url\DiscourseUrlManipulator;

class DiscourseRequest
{
    /**
     * @var string The HTTP method for this request.
     */
    protected $method;
    /**
     * @var string The Graph endpoint for this request.
     */
    protected $endpoint;
    /**
     * @var array The headers to send with this request.
     */
    protected $headers = [];
    /**
     * @var array The parameters to send with this request.
     */
    protected $params = [];

    public function __construct($accessToken, $method, $endpoint, $params)
    {
        $this->setAccessToken($accessToken);
        $this->setMethod($method);
        $this->setEndpoint($endpoint);
        $this->setParams($params);
    }

    /**
     * Set the access token for this request.
     *
     * @param AccessToken|string|null
     *
     * @return FacebookRequest
     */
    public function setAccessToken($accessToken)
    {
        $this->accessToken = $accessToken;

        return $this;
    }

    /**
     * Return the access token for this request.
     *
     * @return string|null
     */
    public function getAccessToken()
    {
        return $this->accessToken;
    }

    /**
     * Set the endpoint for this request.
     *
     * @param string
     *
     * @return DiscourseRequest
     */
    public function setEndpoint($endpoint)
    {
        $this->endpoint = $endpoint;
        return $this;
    }

    /**
     * Return the endpoint for this request.
     *
     * @return string
     */
    public function getEndpoint()
    {
        // For batch requests, this will be empty
        return $this->endpoint;
    }

    /**
     * Return the HTTP method for this request.
     *
     * @return string
     */
    public function getMethod()
    {
        return $this->method;
    }

    /**
     * Set the HTTP method for this request.
     *
     * @param string
     */
    public function setMethod($method)
    {
        $this->method = strtoupper($method);
    }

    /**
     * Generate and return the headers for this request.
     *
     * @return array
     */
    public function getHeaders()
    {
        $headers = static::getDefaultHeaders();
        $accessToken = $this->getAccessToken();

        if ($accessToken) {
            $headers['Authorization'] = 'Bearer ' . $accessToken;
        }

        return array_merge($this->headers, $headers);
    }

    /**
     * Set the headers for this request.
     *
     * @param array $headers
     */
    public function setHeaders(array $headers)
    {
        $this->headers = array_merge($this->headers, $headers);
    }

    /**
     * Set the params for this request.
     *
     * @param array $params
     *
     * @return DiscourseRequest
     */
    public function setParams(array $params = [])
    {
        $this->dangerouslySetParams($params);
        return $this;
    }

    /**
     * Generate and return the params for this request.
     *
     * @return array
     */
    public function getParams()
    {
        $params = $this->params;

        return $params;
    }

    /**
     * Returns the body of the request as URL-encoded.
     *
     * @return RequestBodyUrlEncoded
     */
    public function getUrlEncodedBody()
    {
        $params = $this->getPostParams();
        return new RequestBodyUrlEncoded($params);
    }

    /**
     * Only return params on POST requests.
     *
     * @return array
     */
    public function getPostParams()
    {
        if ($this->getMethod() === 'POST') {
            return $this->getParams();
        }
        return [];
    }

    /**
     * Set the params for this request without filtering them first.
     *
     * @param array $params
     *
     * @return DiscourseRequest
     */
    public function dangerouslySetParams(array $params = [])
    {
        $this->params = array_merge($this->params, $params);
        return $this;
    }

    /**
     * Validate that the HTTP method is set.
     *
     * @throws DiscourseSDKException
     */
    public function validateMethod()
    {
        if (!$this->method) {
            throw new DiscourseSDKException('HTTP method not specified.');
        }
        if (!in_array($this->method, ['GET', 'POST', 'DELETE', 'PUT'])) {
            throw new DiscourseSDKException('Invalid HTTP method specified.');
        }
    }

    /**
     * Generate and return the URL for this request.
     *
     * @return string
     */
    public function getUrl()
    {
        $this->validateMethod();

        $endpoint = DiscourseUrlManipulator::forceSlashPrefix($this->getEndpoint());
        $url = $endpoint;
        if ($this->getMethod() !== 'POST') {
            $params = $this->getParams();
            $url = DiscourseUrlManipulator::appendParamsToUrl($url, $params);
        }
        return $url;
    }

    /**
     * Return the default headers that every request should use.
     *
     * @return array
     */
    public static function getDefaultHeaders()
    {
        return [
            'User-Agent' => '4rum-php-' . Discourse::VERSION,
            'Accept-Encoding' => '*',
        ];
    }
}
