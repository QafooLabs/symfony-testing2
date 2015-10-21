<?php

namespace AppBundle\Entity;

class TaskListView
{
    public $id;
    public $name;
    public $categoryName;
    public $tags = array();

    public function tagCount()
    {
        return count($this->tags);
    }
}
