<?php

namespace AppBundle\Entity;

interface TaskRepository
{
    public function save(Task $task);

    public function find($id): Task;

    public function findTaskItems($userId): TaskListView[]
}
