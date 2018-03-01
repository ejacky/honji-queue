<?php
namespace Sfmg\Queue;

interface Driver
{
    public function enqueue($queueName, $message);

    public function dequeue($queueName, $timeout);

    public function ack($queueName, $message);

    public function size($queueName);
}