<?php

namespace Cast;

interface ActorRepository
{
    /**
     * Return all the Actors of the repository 
     *
     * @return iterable|null
     */
    public function findAll(): ?iterable;

    /**
     * Find one Actor within the repository by id 
     *
     * @return Actor|null
     */
    public function findBy($id): ?Actor;

    /**
     * Add an Actor to the repository
     */
    public function add(Actor $actor);

    /**
     * Update an existing Actor to the repository
     */
    public function update(Actor $actor);

    /**
     * Remove an Actor from the repository
     */
    public function remove(Actor $actor);
}
