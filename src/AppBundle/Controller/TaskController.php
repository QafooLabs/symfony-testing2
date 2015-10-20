<?php

namespace AppBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use AppBundle\Entity\Task;

class TaskController extends Controller
{
    public function newAction(Request $request)
    {
        $task = new Task();

        $form = $this->createFormBuilder($task)
            ->add('task', 'text')
            ->add('dueDate', 'date')
            ->add('save', 'submit', array('label' => 'Create Task'))
            ->getForm();

        $form->handleRequest($request);

        if ($form->isValid()) {
            $entityManager = $this->get('doctrine.orm.default_entity_manager');
            $entityManager->persist($task);
            $entityManager->flush();

            return $this->redirectToRoute('task_new', ['id' => $task->id]);
        }

        return $this->render('default/new.html.twig', array(
            'form' => $form->createView(),
        ));
    }
}
