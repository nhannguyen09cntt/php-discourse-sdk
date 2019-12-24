<?php
namespace Discourse;

class DiscourseClient
{

    /**
     * @const string Production Graph API URL.
     */
    const BASE_GRAPH_URL = 'https://4rum.vn';

    /**
     * @const int The timeout in seconds for a normal request.
     */
    const DEFAULT_REQUEST_TIMEOUT = 60;

    /**
     * @var DiscourseHttpClientInterface HTTP client handler.
     */

    protected $httpClientHandler;

    /**
     * Create a new httpclient instance.
     *
     * @return void
     */
    public function __construct($httpClient)
    {
        $this->httpClientHandler = $httpClient;
    }

    /**
     * Returns the base Graph URL.
     *
     * @return string
     */
    public function getBaseGraphUrl()
    {

        return static::BASE_GRAPH_URL;
    }

    public function sendRequest(DiscourseRequest $request)
    {
        list($url, $method, $headers, $body) = $this->prepareRequestMessage($request);
        $timeOut = static::DEFAULT_REQUEST_TIMEOUT;

        $rawResponse = $this->httpClientHandler->send($url, $method, $body, $headers, $timeOut);

        $returnResponse = new DiscourseResponse(
            $request,
            $rawResponse->getBody(),
            $rawResponse->getHttpResponseCode(),
            $rawResponse->getHeaders()
        );
        if ($returnResponse->isError()) {
            throw $returnResponse->getThrownException();
        }
        return $returnResponse;
    }

    public function prepareRequestMessage(DiscourseRequest $request)
    {
        $url = $this->getBaseGraphUrl() . $request->getUrl();
        $requestBody = $request->getUrlEncodedBody();
        $request->setHeaders([
            'Content-Type' => 'application/json',
        ]);

        return [
            $url,
            $request->getMethod(),
            $request->getHeaders(),
            $requestBody->getBody(),
        ];
    }
}
