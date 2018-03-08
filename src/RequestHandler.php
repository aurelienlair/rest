<?php

namespace Cast;

use Psr\Http\Message\ServerRequestInterface;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Psr7\UriResolver;
use GuzzleHttp\Psr7\Uri;
use Cast\Actor;
use Cast\CsvActorRepository;

class RequestHandler
{
    public function __call($methodName, $args)
    {
        if (is_callable($method)) {
            return call_user_func_array(
                [$this, $methodName],
                $args
            );
        }
    }

    public function handleGet(
        CsvActorRepository $repository,
        URI $uri
    ): ?Response
    {
        $body = [];
        $id = (string)UriResolver::relativize(
            new Uri('http://localhost:8888/api/actors/'),
            $uri
        );
        if ($id) {
            $actor = $repository->findBy(['id' => $id]);
            if (!empty($actor)) {
                $body = $actor->export();
            }
        } else {
            $body = [];
            foreach ($repository->findAll() as $key => $actor) {
                $body[$key] = $actor->export();
            }
        }
        if (!$body) {
            $response = $this->response(404);
        } else {
            $response = $this->response(200);
        }

        return $response;
    }

    public function handlePost(
        $repository,
        ServerRequestInterface $request,
        URI $uri
    ): ?Response
    {
        $actor = Actor::fromRequest($request);
        $repository->add($actor);

        return $this->response(
            201, 
            [
                'Location' => $uri->__toString() . '/' . $actor->export()['id']
            ]
        );
    }

    public function handlePing()
    {
        return $this->response(
            200, 
            [
                'Content-type' => 'application/html; charset=utf-8'
            ], 
            'pong'
        );
    }

    private function response($responseCode, array $headers=[], $body='')
    {
        $defaultHeaders = [
            'Content-type' => 'application/json',
        ];

        return new Response(
            $responseCode, 
            array_merge($defaultHeaders, $headers),
            $body
        );
    }
}
