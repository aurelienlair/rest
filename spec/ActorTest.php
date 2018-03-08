<?php

namespace Tests;

use PHPUnit\Framework\TestCase;
use Cast\Actor;

class ActorTest extends TestCase
{
    use RequestTrait;

    public function testCanBeCreatedFromValidSpecification()
    {
        $actor = Actor::fromRequest($this->request());
        $this->assertNotNull($actor);
    }

    public function testActorExport()
    {
        $actor = Actor::fromRequest($this->request());

        $this->assertEquals('Ewan', $actor->export()['firstname']);
        $this->assertEquals('MacGregor', $actor->export()['lastname']);
        $this->assertEquals('GB', $actor->export()['country']);
        $this->assertRegExp('/^[a-f0-9]{32}$/', $actor->export()['id']);
    }

    public function testActorImportFromArray()
    {
        $actor = Actor::fromArray([
            'firstname' => 'Ewan',
            'lastname' => 'MacGregor',
            'country' => 'GB',
            'id' => md5(uniqid(rand(), true))
        ]);

        $this->assertEquals('Ewan', $actor->export()['firstname']);
        $this->assertEquals('MacGregor', $actor->export()['lastname']);
        $this->assertEquals('GB', $actor->export()['country']);
        $this->assertRegExp('/^[a-f0-9]{32}$/', $actor->export()['id']);
    }

    private function request(array $data=[])
    {
        $data = array_merge([
            'firstname' => 'Ewan',
            'lastname' => 'MacGregor',
            'country' => 'GB'
        ], $data);
        $request = $this->createMock('Psr\Http\Message\ServerRequestInterface');
        $request->expects($this->at(0))
            ->method('getParsedBody')
            ->will($this->returnValue($data));
        $request->expects($this->at(1))
            ->method('getParsedBody')
            ->will($this->returnValue([
                'lastname' => $data['lastname'],
            ]));
        $request->expects($this->at(2))
            ->method('getParsedBody')
            ->will($this->returnValue([
                'country' => $data['country']
            ]));

        return $request;
    }
}
