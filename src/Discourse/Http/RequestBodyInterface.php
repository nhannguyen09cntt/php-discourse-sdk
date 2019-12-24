<?php
namespace Discourse\Http;

/**
 * Interface
 *
 * @package Discourse
 */
interface RequestBodyInterface
{
    /**
     * Get the body of the request to send to Graph.
     *
     * @return string
     */
    public function getBody();
}
