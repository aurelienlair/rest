<?php

namespace Cast;

use ArrayIterator;

class CsvActorRepository implements ActorRepository
{
    const FILE_PATH = 'storage/actors.csv';

    private $csvParser;

    public function __construct(CsvParser $csvParser)
    {
        $this->csvParser = $csvParser;
    }

    public function findAll(): ?iterable
    {
        $this->csvParser->readCSV();

		return new ArrayIterator(
            array_map(
                function($row)
                {
                    $data = [
                        'firstname' => $row[1],
                        'lastname' => $row[2],
                        'country' => $row[3],
                        'id' => $row[0]
                    ];
                    return Actor::fromArray($data);
                },
                $this->csvParser->getRows()
            )
        );
    }

    public function findBy($id): ?Actor
    {
        $result = array_filter(
            iterator_to_array($this->findAll()),
            function ($actor) use ($id) {
                return $actor->export()['id'] === $id;
            }
        );

        if (count($result)) {
            return array_pop($result);
        }
        
        return null;
    }

    public function add(Actor $actor)
    {
        $this->csvParser->readCSV();
        $this->csvParser->addRow(array_values($actor->export()));
        $this->csvParser->save();
    }

    public function update(Actor $actor)
    {
        $data = $this->findAll();
        $rowNumber = array_search($actor->export()['id'], array_column(iterator_to_array($data), 'id'));
        $this->csvParser->updateRow($rowNumber, array_values($actor->export()));
        $this->csvParser->save();
    }

    public function remove(Actor $actor)
    {
        $data = $this->findAll();
        $rowNumber = array_search($actor->export()['id'], array_column(iterator_to_array($data), 'id'));
        $this->csvParser->removeRow($rowNumber);
        $this->csvParser->save();
    }
}
