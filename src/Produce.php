<?php
namespace Sfmg\Queue;

class Produce
{
    private $driver;

    private $queuename;

    private $message;

    public function __construct(Driver $driver)
    {
        $this->driver = $driver;
    }

    public function setMessages($queuename, $message)
    {
        $this->queuename = $queuename;
        $this->message = $message;
    }

    public function send()
    {
        $this->driver->enqueue($this->queuename, $this->message);
    }

    public function size()
    {
        return $this->driver->size($this->queuename);
    }
}