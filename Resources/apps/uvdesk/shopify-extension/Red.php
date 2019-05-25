<?php

namespace HelpdeskExtension\UVDesk\ShopifyEcommerce;

use Doctrine\ORM\EntityManager;
use Predis\Client as RedisClient;
use Symfony\Component\DependencyInjection\ContainerInterface;

class Scheduler
{
    private $redis;
    private $container;
    private $entityManager;

    public function __construct(ContainerInterface $container, EntityManager $entityManager)
    {
        $this->container = $container;
        $this->entityManager = $entityManager;

        $this->redis = new RedisClient([
            'scheme' => $this->container->getParameter('scheme'),
            'host' => $this->container->getParameter('host'),
            'port' => $this->container->getParameter('port'),
        ], [
            'profile' => '2.8',
            'prefix' => 'red:',
        ]);
    }

    public function getRedisClient()
    {
        return $this->redis;
    }

    public function isRedisClientConnected()
    {
        try {
            $this->redis->connect();

            if ((bool) $this->redis->isConnected() == false) {
                return false;
            }
        } catch (\Exception $e) {
            return false;
        }

        return true;
    }

    public function pubsub()
    {
        // Subscribe to key-space event notification
		$pubsub = $this->getPubSub();
		$pubsub->subscribe('__keyevent@0__:expired');

        foreach ($pubsub as $message) {
			if ('subscribe' == $message->kind) {
				$output->write("\nSubscribed to channel <fg=yellow>" . $message->channel . "</>\n\n");
			} else if ('message' == $message->kind) {
				switch ($message->channel) {
					case '__keyevent@0__:expired':
                        $expired_key = $message->payload;
                        
						if ((bool) preg_match('/pattern:(.*)/', $expired_key, $matches) !== false) {
							$output->write("Key expired <fg=yellow>" . $expired_key . "</>\n");
						}
						break;
					default:
						break;
				}
			}
        }
    }

    private function getPubSub()
	{
		$pubsub_client = new RedisClient([
            'scheme' => $this->container->getParameter('redis.client.scheme'),
            'host' => $this->container->getParameter('redis.client.host'),
			'port' => $this->container->getParameter('redis.client.port'),
			'read_write_timeout' => '-1'
        ], [
			'profile' => '2.8',
		]);

		$pubsub_client->config('set', 'notify-keyspace-events', 'KExe');
		return $pubsub_client->pubSubLoop();
	}
}

?>