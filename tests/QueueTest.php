<?php
namespace Test;

class QueueTest extends \UnitTestCase
{
    public function testProducer()
    {
        $qp = $this->getDI()->get('queue_produce');
        $qp->setMessages('sync', json_encode(['test']));
        $origin_size = $qp->size();
        $qp->send();
        $this->assertEquals($origin_size + 1, $qp->size() );
    }

    public function testConsumer()
    {
        $qc = $this->getDI()->get('queue_consumer');
        $qc->setQueueName('sync');
        $qc->setTimeout(30);
        $message = $qc->fetch();
        $this->assertEquals(count($message), 2);
    }
}

