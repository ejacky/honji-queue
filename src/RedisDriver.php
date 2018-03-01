<?php
namespace Sfmg\Queue;

class RedisDriver implements Driver
{
    private $redis;

    public function __construct($redis)
    {
        if (is_null($this->redis)) {
            $this->redis = $redis;
        }
    }

    public function size($queue)
    {
        return $this->redis->llen('queue:' . $queue);
    }

    public function enqueue($queueName, $message)
    {
        $this->redis->rpush('queue:' . $queueName, $message);
    }

    public function dequeue($queueName, $timeout)
    {
        return $this->redis->blpop('queue:' . $queueName, $timeout);
    }

    public function ack($queueName, $message)
    {

    }
}