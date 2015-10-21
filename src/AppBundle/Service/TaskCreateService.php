<?php

namespace AppBundle\Service;

class TaskCreateService
{
    private $repository;

    public function __construct(TaskRepository $repository)
    {
        $this->repository = $repository;
    }

    public function saveTask(SaveTaskCommand $command)
    {
        $task = new Task($command->name, $command->dueDate);
        $events = array(new TaskSavedEvent($task));

        $currentTask = $this->repository->getUsersTaskCount($command->userId);

        if ($currentTask === 0) {
            $events[] = new FirstTaskSavedEvent($task);
        }

        $this->repository->save($task);

        return $events;
    }
}
