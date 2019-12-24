<?php
namespace Discourse\HttpClients;

use Discourse\Exceptions\DiscourseSDKException;
use Discourse\Http\GraphRawResponse;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Psr7\Request;

class DiscourseGuzzleHttpClient implements DiscourseHttpClientInterface
{
    /**
     * @var \GuzzleHttp\Client The Guzzle client.
     */
    protected $guzzleClient;
    /**
     * @param \GuzzleHttp\Client|null The Guzzle client.
     */
    public function __construct(Client $guzzleClient = null)
    {
        $this->guzzleClient = $guzzleClient ?: new Client();
    }
    /**
     * @inheritdoc
     */
    public function send($url, $method, $body, array $headers, $timeOut)
    {
        $options = [
            'timeout' => $timeOut,
            'connect_timeout' => 10,
            // 'verify' => __DIR__ . '/certs/DigiCertHighAssuranceEVRootCA.pem',
        ];
        $request = new Request($method, $url, $headers, $body);
        try {
            $rawResponse = $this->guzzleClient->send($request, $options);
        } catch (RequestException $e) {
            $rawResponse = $e->getResponse();
            if (!$rawResponse instanceof Response) {
                throw new DiscourseSDKException($e->getMessage(), $e->getCode());
            }
        }
        $rawHeaders = $this->getHeadersAsString($rawResponse);
        $rawBody = $rawResponse->getBody();
        $httpStatusCode = $rawResponse->getStatusCode();
        return new GraphRawResponse($rawHeaders, $rawBody, $httpStatusCode);
    }

    /**
     * Returns the Guzzle array of headers as a string.
     *
     * @param Response $response The Guzzle response.
     *
     * @return string
     */
    public function getHeadersAsString(Response $response)
    {
        $headers = $response->getHeaders();
        $rawHeaders = [];
        foreach ($headers as $name => $values) {
            $rawHeaders[] = $name . ": " . implode(", ", $values);
        }
        return implode("\r\n", $rawHeaders);
    }
}
