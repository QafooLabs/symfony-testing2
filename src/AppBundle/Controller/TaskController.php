<?php

namespace AppBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use AppBundle\Entity\Task;
use AppBundle\Form\TaskType;

use QafooLabs\MVC\FormRequest;
use QafooLabs\MVC\RedirectRoute;

class TaskController
{
    private $entityManager;

    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;

    }
    public function newAction(FormRequest $request)
    {
        $task = new Task();

        if ($request->handle(new TaskType, $task)) {
            $task = $request->getData();

            $this->entityManager->persist($task);
            $this->entityManager->flush();

            return new RedirectRoute('task_new', ['id' => $task->id]);
        }

        return new TemplateView(
            'default/new.html.twig', array(
            'form' => $request->createFormView(),
        ));
    }
}







