<?php

namespace Tests;

use PHPUnit\Framework\TestCase;
use Cast\Actor;
use Cast\CsvActorRepository;
use Cast\CsvParser;

class CsvActorRepositoryTest extends TestCase
{
    use RequestTrait;

    public function setUp()
    {
        if (file_exists(CsvActorRepository::FILE_PATH)) {
            shell_exec('/bin/rm ' . CsvActorRepository::FILE_PATH);
        }
    }

    public function testActorIsSavedProperlyOnTheRepository()
    {
        $actor = Actor::fromRequest($this->request());
        $csvParser= $this->createMock(CsvParser::class);
        $csvParser->expects($this->once())
            ->method('readCSV');
        $csvParser->expects($this->once())
            ->method('addRow')
            ->with(array_values($actor->export()));
        $csvParser->expects($this->once())
            ->method('save');
        $repository = new CsvActorRepository($csvParser); 
        $repository->add($actor);
    }

    public function testActorsAreRetrievedProperlyFromTheRepository()
    {
        $actor = Actor::fromRequest($this->request());
        $repository = new CsvActorRepository(
            new CsvParser(CsvActorRepository::FILE_PATH)
        );
        $repository->add($actor);
        $repository->add(
            Actor::fromRequest(
                $this->request([
                    'firstname' => 'Bruce',
                    'lastname' => 'Willis',
                    'country' => 'US'
                ])
            )
        );
        $repository->add(
            Actor::fromRequest(
                $this->request([
                    'firstname' => 'Robert',
                    'lastname' => 'De Niro',
                    'country' => 'US'
                ])
            )
        );
        $actors = iterator_to_array($repository->findAll());
        $this->assertArraySubset(
            [
                'firstname' => 'Ewan',
                'lastname' => 'MacGregor',
                'country' => 'GB'
            ],
            $actors[0]->export()
        );
        $this->assertArraySubset(
            [
                'firstname' => 'Bruce',
                'lastname' => 'Willis',
                'country' => 'US'
            ],
            $actors[1]->export()
        );
        $this->assertArraySubset(
            [
                'firstname' => 'Robert',
                'lastname' => 'De Niro',
                'country' => 'US'
            ],
            $actors[2]->export()
        );
    }

    public function testRetrieveslAnActorProperlyFromTheRepository()
    {
        $actor = Actor::fromRequest($this->request());
        $repository = new CsvActorRepository(
            new CsvParser(CsvActorRepository::FILE_PATH)
        );
        $repository->add($actor);
        $repository->add(
            Actor::fromRequest(
                $this->request([
                    'firstname' => 'Robert',
                    'lastname' => 'De Niro',
                    'country' => 'US'
                ])
            )
        );
        $actorFound = $repository->findBy($actor->export()['id']);
        $this->assertArraySubset(
            [
                'firstname' => 'Ewan',
                'lastname' => 'MacGregor',
                'country' => 'GB'
            ],
            $actorFound->export()
        );
    }

    public function testRemovesAnActorProperlyFromTheRepository()
    {
        $firstActor = Actor::fromRequest($this->request());
        $repository = new CsvActorRepository(
            new CsvParser(CsvActorRepository::FILE_PATH)
        );
        $repository->add($firstActor);

        $secondActor = Actor::fromRequest(
            $this->request([
                'firstname' => 'Robert',
                'lastname' => 'De Niro',
                'country' => 'US'
            ])
        );
        $expectedActorId = $secondActor->export()['id'];
        $repository->add($secondActor);
        $repository->remove($firstActor);
        $actors = iterator_to_array($repository->findAll());
        $this->assertArraySubset(
            [
                'firstname' => 'Robert',
                'lastname' => 'De Niro',
                'country' => 'US'
            ],
            $actors[0]->export()
        );
        $this->assertEquals($expectedActorId, $actors[0]->export()['id']);
    }

    public function testUpdatesPartiallyAnActorProperlyOnTheRepository()
    {
        $actor = Actor::fromRequest($this->request());
        $repository = new CsvActorRepository(
            new CsvParser(CsvActorRepository::FILE_PATH)
        );
        $repository->add($actor);
        $actorModified = Actor::fromArray([
            'firstname' => 'James Charles Stuart',
            'lastname' => 'MacGregor',
            'country' => 'GB',
            $actor->export()['id']
        ]);
        $repository->update($actorModified);
        $actorFound = $repository->findBy($actor->export()['id']);
        $this->assertArraySubset(
            [
                'firstname' => 'James Charles Stuart',
                'lastname' => 'MacGregor',
                'country' => 'GB'
            ],
            $actorFound->export()
        );
    }

    public function testFullUpdateOfAnActorProperlyOnTheRepository()
    {
        $actor = Actor::fromRequest($this->request());
        $repository = new CsvActorRepository(
            new CsvParser(CsvActorRepository::FILE_PATH)
        );

        $actorModified = Actor::fromArray([
            'firstname' => 'Bruce',
            'lastname' => 'Lee',
            'country' => 'US',
            $actor->export()['id']
        ]);
        $repository->update($actorModified);
        $actorFound = $repository->findBy($actor->export()['id']);
        $this->assertArraySubset(
            [
                'firstname' => 'Bruce',
                'lastname' => 'Lee',
                'country' => 'US'
            ],
            $actorFound->export()
        );
    }

    public function tearDown()
    {
        if (file_exists(CsvActorRepository::FILE_PATH)) {
            shell_exec('/bin/rm ' . CsvActorRepository::FILE_PATH);
        }
    }
}
