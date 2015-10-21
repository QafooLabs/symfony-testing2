<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity
 */
class Task
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue
     */
    public $id;

    /**
     * @ORM\Column(type="string")
     * @Assert\NotBlank
     */
    public $task;

    /**
     * @ORM\Column(type="datetime")
     */
    public $dueDate;

    private $events = [];

    public function create($task, DateTime $dueDate)
    {
        if (empty($task) || !is_string($task)) {
            throw new \InvalidArgumentException();
        }

        if ($dueDate < new \DateTime('now')) {
            throw new \InvalidArgumentException();
        }

        $this->emit(new TaskSavedEvent($task, $dueDate));
    }

    protected function handleTaskSaved(TaskSavedEvent $event)
    {
        $this->task = $event->task;
        $this->dueDate = $event->dueDate;
    }

    public function postpone()
    {
        $newDate = clone ($this->dueDate);
        $newDate->modify('+1 day');

        $this->emit(new TaskPostponedEvent($newDate));
    }

    protected function handleTaskPostponed(TaskPostponedEvent $event)
    {
        $this->dueDate = $event->newDueDate;
    }

    public function popEvents()
    {
        $events = $this->events;
        $this->events = [];

        return $events;
    }

    public function replay(array $events)
    {
        foreach ($events as $event) {
            $method = "handle" . (new ReflectionObject($event))->getShortName();
            $this->$method($event);
        }
    }

    protected function emit($event)
    {
        $this->events[] = $event;
        $method = "handle" . (new ReflectionObject($event))->getShortName();
        $this->$method($event);
    }
}
