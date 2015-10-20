<?php

namespace AppBundle\Service;

use Doctrine\ORM\EntityManager;
use DateTime;

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
        $tasks = $this->getNextWeeksTasks($start, $end);
        $nextTasks = $this->getNextTasks($tasks, 3);
        $chart = $this->generateChartFromTasks($tasks, $includeHours);
        $this->sendTaskChart($chart, $tasks, $start, $end);
    }

    public function getNextWeeksTasks(DateTime $start, Datetime $end)
    {
        $dql = 'SELECT t FROM AppBundle\Entity\Task t
                 WHERE t.dueDate >= ?1 AND t.dueDate <= ?2
              ORDER BY t.dueDate ASC';

        return $this->entityManager->createQuery($dql)
            ->setParameter(1, $start)
            ->setParameter(2, $end)
            ->getResult();
    }

    public function getNextTasks(array $tasks, $amount = 3)
    {
        return array_slice($tasks, 0, $amount);
    }

    public function generateChartFromTasks(array $tasks, $includeHours)
    {
        $chart = [];
        $format = $includeHours ? 'Y-m-d H' : 'Y-m-d';

        foreach ($tasks as $task) {
            if (!isset($chart[$task->dueDate->format($format)])) {
                $chart[$task->dueDate->format($format)] = 0;
            }
            $chart[$task->dueDate->format($format)]++;
        }

        return $chart;
    }

    public function sendTaskChart(array $chart, array $nextTasks, $start, $end)
    {
        $body = $this->templating->render(
            'Emails/dueDateChart.html.twig',
            array('chart' => $chart, 'nextThreeTasks' => $nextTasks)
        );

        $message = \Swift_Message::newInstance()
            ->setSubject(sprintf('Next Weeks tasks: %s - %s', $start->format('Y-m-d'), $end->format('Y-m-d')))
            ->setFrom('info@taskmonster.local')
            ->setTo('me@example.com')
            ->setBody($body, 'text/html');

        $this->mailer->send($message);
    }
}
