<?php

namespace AppBundle\Tests\Controller;

use AppBundle\Entity\Task;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class TaskControllerTest extends WebTestCase
{
    public function testNewTasks()
    {
        $client = self::createClient();

        $crawler = $client->request('GET', '/tasks');

        $form = $crawler->filter('.new')->form();

        $name = 'Innogames Workshop' . time();
        $crawler = $client->submit($form, ['form[task]' => $name]);

        $em = $client->getContainer()->get('doctrine.orm.default_entity_manager');
        $repository = $em->getRepository(Task::class);

        $task = $repository->findOneBy(array('task' => $name));
        $this->assertNotNull($task);
    }

    public function testNewBlankTaskNameNotInDatabase()
    {
        $client = self::createClient();

        $crawler = $client->request('GET', '/tasks');

        $form = $crawler->filter('.new')->form();

        $crawler = $client->submit($form, ['form[task]' => '']);

        $this->assertContains('This value should not be blank.', $client->getResponse()->getContent());

        $em = $client->getContainer()->get('doctrine.orm.default_entity_manager');
        $repository = $em->getRepository(Task::class);

        $task = $repository->findOneBy(array('task' => ''));
        $this->assertNull($task);
    }
}
