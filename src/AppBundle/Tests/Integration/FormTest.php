<?php

namespace AppBundle\Tests\Integration;

class FormTest extends \PHPUnit_Framework_TestCase
{
    public function testStringModel()
    {
        $this->markTestskipped();

        $builder = new FormBuilder();
        $model = new TestModel();

        $form = $builder->createFormBuilder($model)
            ->add('string', 'text')
            ->getForm();

        $form->handle(['string' => 'test']);

        $classMetadata = new ClassMetadata(TestModel::class);
        $classMetadata->mapField(array('name' => 'string', 'type' => 'string'));

        $validatorMetadata = new ClassMetadata(TestModel::class);
        $validatorMetadata->addConstraint();
    }
}

class TestModel
{
    public $string;
}
