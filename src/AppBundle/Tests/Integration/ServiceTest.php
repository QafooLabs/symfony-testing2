<?php

namespace AppBundle\Tests\Integration;

use AppBundle\Service\TaskService;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\AbstractQuery;
use Symfony\Component\Templating\EngineInterface;
use DateTime;
use AppBundle\Entity\Task;

class ServiceTest extends \PHPUnit_Framework_TestCase
{
    public function testTaskServicePhake()
    {
        $entityManager = \Phake::mock(EntityManager::class);
        $query = \Phake::mock(AbstractQuery::class);
        $templating = \Phake::mock(EngineInterface::class);
        $mailer = \Phake::mock(\Swift_Transport::class);

        $service = new TaskService($entityManager, $mailer, $templating);

        \Phake::when($entityManager)->createQuery(\Phake::anyParameters())->thenReturn($query);
        \Phake::when($query)->setParameter(\Phake::anyParameters())->thenReturn($query);
        \Phake::when($query)->getResult()->thenReturn(array());
        \Phake::when($templating)->render(\Phake::anyParameters())->thenReturn('foo');

        $service->nextWeekReport(new DateTime, new DateTime);

        \Phake::verify($mailer)->send($this->isInstanceOf(\Swift_Message::class));
    }

    public function testTaskService()
    {
        $entityMock = $this->getMockBuilder('Doctrine\ORM\EntityManager')->setMethods(
            ['createQuery', 'setParameter', 'getResult']
        )->disableOriginalConstructor()->getMock();

        $entityMock->method('createQuery')->willReturnSelf();
        $entityMock->method('setParameter')->willReturnSelf();
        $entityMock->method('getResult')->willReturn([$this->getTask(), $this->getTask(), $this->getTask()]);

        $mailerMock = $this->getMockBuilder('SwiftMailer')->setMethods(['send'])->getMock();
        $mailerMock->expects($this->once())->method('send')->with($this->attributeEqualTo('_body', ''));

        $templatingMock = $this->getMockBuilder('Templating')->setMethods(['render'])->getMock();
        $templatingMock->method('render')->willReturn('');

        $service = new TaskService($entityMock, $mailerMock, $templatingMock);

        $service->nextWeekReport(new DateTime(), new DateTime());
    }

    protected function getTask()
    {
        $task = new Task();
        $task->id = rand(1, 1000);
        $task->dueDate = new DateTime('2015-01-'.rand(1, 31));

        return $task;
    }

    public function testNextWeekReport()
    {
        $em = $this->getMockBuilder('Doctrine\ORM\EntityManager')
            ->disableOriginalConstructor()
            ->getMock();

        $mailer = $this->getMockBuilder('Swift_Mailer')
            ->disableOriginalConstructor()
            ->getMock();

        $templating = $this->getMockBuilder('Symfony\Component\Templating\EngineInterface')
            ->getMockForAbstractClass();

        $start = new DateTime('now');
        $end = new DateTime('2015-10-30 12:00:00');

        $includeHours = false;


        $dql = 'dql ...';

        $result = array();
        $task1 = new \AppBundle\Entity\Task();
        $task1->dueDate = new \DateTime();
        $task2 = new \AppBundle\Entity\Task();
        $task2->dueDate = new \DateTime();
        $result[] = $task1;
        $result[] = $task2;


        $em->expects($this->at(0))->method('createQuery')
            //->with($this->equalTo($dql))
            ->will($this->returnSelf());
        $em->expects($this->at(1))
            ->method('setParameter')
            ->with($this->equalTo(1), $this->equalTo($start))
            ->will($this->returnSelf());
        $em->expects($this->at(2))
            ->method('setParameter')
            ->with($this->equalTo(2), $this->equalTo($end))
            ->will($this->returnSelf());
        $em->expects($this->at(3))
            ->method('getResult')
            ->will($this->returnValue($result));

        //$options = array('chart' => $chart, 'nextThreeTasks' => $nextThreeTasks)
        $templating->expects($this->once())
            ->method('render')
            //->with($this->equalTo('Emails/dueDateChart.html.twig'), $options);
            ->willReturn('template Content');
        $templating->expects($this->once())->method('send');

        $o = new \AppBundle\Service\TaskService($em, $mailer, $templating);
        $o->nextWeekReport($start, $end, $includeHours);



    }
    }
}
