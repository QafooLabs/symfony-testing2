<?php

namespace AppBundle\Tests\Integration;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class ServiceTest extends KernelTestCase
{
    public function testGet()
    {
        $kernel = self::createKernel();
        $kernel->boot();
        $container = $kernel->getcontainer();

        $service = $container->get('task_service');
    }
}
