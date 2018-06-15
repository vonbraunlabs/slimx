<?php

namespace SlimX\Models\Tables;

use Psr\Log\LoggerInterface;
use RedBeanPHP\OODBBean;
use RedBeanPHP\R;

abstract class AbstractTable
{
    protected $tableName;
    protected $logger;
    protected $cache;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
        $this->cache = [];
    }

    public function findOneById(int $id): ?OODBBean
    {
        if (!isset($this->cache[$id])) {
            $this->logger->debug(get_class($this) . '::findOneById cache miss ' . $id);
            $bean = R::findOne($this->tableName, 'id = ? AND active = ?', [$id, 1]);
            if (count($this->cache) < 1024) {
                $this->cache[$id] = $bean;
            }
        } else {
            $this->logger->debug(get_class($this) . '::findOneById cache hit ' . $id);
            $bean = $this->cache[$id];
        }

        return $bean;
    }
}
