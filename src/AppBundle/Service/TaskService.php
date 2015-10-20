<?php

namespace AppBundle\Service;

use Doctrine\ORM\EntityManager;

class TaskService
{
    private $entityManager;
    private $mailer;
    private $templating;

    public function __construct(EntityManager $entityManager, $mailer, $templating)
    {
        $this->entityManager = $entityManager;
        $this->mailer = $mailer;
        $this->templating = $templating;
    }

    public function nextWeekReport(DateTime $start, Datetime $end, $includeHours = false)
    {
        $dql = 'SELECT t FROM AppBundle\Entity\Task t
                 WHERE t.dueDate >= ?1 AND t.dueDate <= ?2
              ORDER BY t.dueDate ASC';
        $tasks = $this->entityManager->createQuery($dql)
            ->setParameter(1, $start)
            ->setParameter(2, $end)
            ->getResult();

        $chart = [];
        $nextThreeTasks = array_slice($tasks, 0, 3);
        $format = $includeHours ? 'Y-m-d H' : 'Y-m-d';

        foreach ($tasks as $task) {
            if (!isset($chart[$task->dueDate->format($format)])) {
                $chart[$task->dueDate->format($format)] = 0;
            }
            $chart[$task->dueDate->format($format)]++;
        }

        $body = $this->templating->render(
            'Emails/dueDateChart.html.twig',
            array('chart' => $chart, 'nextThreeTasks' => $nextThreeTasks)
        );

        $message = \Swift_Message::newInstance()
            ->setSubject(sprintf('Next Weeks tasks: %s - %s', $start->format('Y-m-d'), $end->format('Y-m-d')))
            ->setFrom('info@taskmonster.local')
            ->setTo('me@example.com')
            ->setBody($body, 'text/html');

        $this->mailer->send($message);
    }
}
