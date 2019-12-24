<?php 
namespace Discourse\HttpClients;

/**
 * Interface DiscourseHttpClientInterface
 *
 * @package Discourse
 */
interface DiscourseHttpClientInterface
{
    /**
     * Sends a request to the server and returns the raw response.
     *
     * @param string $url     The endpoint to send the request to.
     * @param string $method  The request method.
     * @param string $body    The body of the request.
     * @param array  $headers The request headers.
     * @param int    $timeOut The timeout in seconds for the request.
     *
     * @return \Discourse\Http\GraphRawResponse Raw response from the server.
     *
     * @throws \Discourse\Exceptions\DiscourseSDKException
     */
    public function send($url, $method, $body, array $headers, $timeOut);
}