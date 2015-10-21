<?php

namespace AppBundle\Entity;

class TaskOrmRepository
{
    public function save(Task $task)
    {
        $this->entityManager->persist($task);
        $this->entityManager->flush($task);

        $events = $task->popEvents();
        foreach ($events as $event) {
            $this->eventDispatcher->dispatch(get_class($event), $event);
        }
    }
}

class EventSourcingRepository
{
    public function save(Task $task)
    {
        $events = $task->popEvents();

        $this->eventStore->save($task->getId(), $events);

        foreach ($events as $event) {
            $this->eventDispatcher->dispatch(get_class($event), $event);
        }
    }

    public function find($id)
    {
        $events = $this->eventStore->find($id);

        $task = new Task();
        $task->replay($events);

        return $task;
    }
}
