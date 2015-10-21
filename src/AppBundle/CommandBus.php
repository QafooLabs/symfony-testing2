<?php

namespace AppBundle;

use Symfony\Component\DependencyInjection\ContainterInterface;

class CommandBus
{
    private $commands = [
        SaveTaskCommand::class => ['task_create_service', 'saveTask'],
    ];

    private $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;

    }

    public function handle($command)
    {
        $service = $this->container->get($this->commands[get_class($command)][0]);
        $method = $this->commands[get_class($command)][1];

        $happendEvents = $service->$method($command);

        foreach ($happendEvents as $event) {
            $this->eventDispatcher->dispatch(get_class($event), $happendEvents);
        }
    }
}
