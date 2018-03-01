<?php
namespace Sfmg\Queue;

use Phalcon\Di;

class Consumer
{
    private $driver;

    private $options;

    private $logger;

    private $max_live_time;

    private $_exec_times      = 0;

    private $queueName;

    private $timeout = 30;

    public function __construct(Driver $driver, $options = null)
    {
        $this->driver = $driver;

        if (is_null($options)) {
            $this->options = [
                'loop_sleep_time' => 1000,
                'exit_sleep_time' => 3,
                'job_max_exec'    => 100000,
                'exec_exit_code'  => 254,
                'job_max_live_time' => 3600
            ];
        }

        $this->logger = DI::getDefault()->get('logger');
    }

    public function fetch()
    {
        return $this->driver->dequeue($this->queueName, $this->timeout);
    }

    public function setQueueName($queueName)
    {
        $this->queueName = $queueName;
    }

    public function setTimeout($seconds)
    {
        $this->timeout = $seconds;
    }

    //
    // 以下方法暂时没有用到
    //
    public function run($callback)
    {
        $this->max_live_time = time() + $this->options['job_max_live_time'];

        while (true) {
            $this->once($callback);
            $this->_exec_times++;

            $this->checkExitCriteria();
        }
    }

    public function once($callback)
    {
        try {
            $message = $this->driver->dequeue($this->queueName, $this->timeout);
            $this->logger->info($message);

        } catch (\Exception $exception) {
            $this->logger->error($exception->getMessage() . PHP_EOL . $exception->getTraceAsString(), $exception);
            exit(0);
        }
        if (!is_null($message)) {
            try {
                $ret = call_user_func($callback, $message);

                if ($ret) {
                    $this->driver->ack($this->queueName, $message);
                }
            } catch (\Exception $exception) {
                $this->logger->error($exception->getMessage() . PHP_EOL . $exception->getTraceAsString(), $exception);
            }
        }

        sleep($this->options['exit_sleep_time']);

        return ;
    }

    public function checkExitCriteria()
    {
        if (time() > $this->max_live_time || $this->_exec_times > $this->options['job_max_exec']) {
            //执行一段时间退出
            sleep($this->options['exit_sleep_time']);
            exit($this->options['exec_exit_code']);
        }
    }
}
