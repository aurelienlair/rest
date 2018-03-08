<?php
require_once __DIR__ . '/../vendor/autoload.php';

use Psr\Http\Message\ServerRequestInterface;
use GuzzleHttp\Psr7\ServerRequest;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Psr7\UriResolver;
use GuzzleHttp\Psr7\Uri;
use Cast\Actor;
use Cast\CsvActorRepository;
use Cast\CsvParser;
use Cast\RequestHandler;
use Cast\ResponseSender;

$uri = new Uri(ServerRequest::getUriFromGlobals());
$requestHandler = new RequestHandler();
$headersOnly = false;

// Routing
switch ($uri->getPath()) {
    case '/api/ping':
        $response = $requestHandler->handlePing();
        break;
    // Main: /api/actors
    default:
        $repository = new CsvActorRepository(
            new CsvParser(CsvActorRepository::FILE_PATH)
        );

        $request = ServerRequest::fromGlobals();

        switch ($request->getMethod()) {
            case 'HEAD':
                $headersOnly = true;
            case 'GET':
                $response = $requestHandler->handleGet($repository, $uri);
                break;
            case 'POST':
                $response = $requestHandler->handlePost($repository, $request, $uri);
                break;
            case 'PUT':
                break;
        }
        break;
}

ResponseSender::send($response, $headersOnly);
