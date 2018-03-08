<?php

namespace Cast;

use GuzzleHttp\Psr7\Response;

class ResponseSender 
{
    public static function send(Response $response, $headersOnly=false)
    {
        header(
            'HTTP/' 
            . $response->getProtocolVersion() 
            . ' ' 
            . $response->getStatusCode()
            . ' '
            . $response->getReasonPhrase()
        );

        foreach ($response->getHeaders() as $name => $values) {
            foreach ($values as $value) {
                header(sprintf('%s: %s', $name, $value), false);
            }
        }

        if (!$headersOnly) {
            echo $response->getBody();
        }
    }
}
