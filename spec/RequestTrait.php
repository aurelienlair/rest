<?php

namespace Tests;

trait RequestTrait
{
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
    /*
    private function request()
    {
        $request = $this->createMock('Psr\Http\Message\ServerRequestInterface');
        $request->expects($this->at(0))
            ->method('getParsedBody')
            ->will($this->returnValue([
                'firstname' => 'Ewan',
                'lastname' => 'MacGregor',
                'country' => 'GB'
            ]));
        $request->expects($this->at(1))
            ->method('getParsedBody')
            ->will($this->returnValue([
                'lastname' => 'MacGregor',
            ]));
        $request->expects($this->at(2))
            ->method('getParsedBody')
            ->will($this->returnValue([
                'country' => 'GB'
            ]));

        return $request;
    }
     */
}
