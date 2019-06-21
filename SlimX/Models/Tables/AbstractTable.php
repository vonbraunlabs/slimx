<?php

namespace SlimX\Models\Tables;

use Psr\Log\LoggerInterface;
use RedBeanPHP\OODBBean;
use RedBeanPHP\R;

/**
 * Table abstraction
 */
abstract class AbstractTable
{
    protected $tableName;
    protected $logger;
    protected $cache;

    /**
     * Default controller.
     *
     * @param LoggerInterface $logger Logger to be used by the class.
     */
    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
        $this->cache = [];
    }

    /**
     * Find one row, by ID.
     *
     * @param int $id Row ID.
     *
     * @return ?OODBBean Row, if found, NULL otherwise.
     */
    public function findOneById(int $id): ?OODBBean
    {
        if (!isset($this->cache[$id])) {
            $this->logger->debug(
                get_class($this) . '::findOneById cache miss ' . $id
            );
            $bean = R::findOne($this->tableName, 'id = ? AND active = ?', [$id, 1]);
            if (count($this->cache) < 1024) {
                $this->cache[$id] = $bean;
            }
        } else {
            $this->logger->debug(
                get_class($this) . '::findOneById cache hit ' . $id
            );
            $bean = $this->cache[$id];
        }

        return $bean;
    }

    /**
     * Store (insert or update) and load bean.
     *
     * @param OODBBean $bean Bean to be stored.
     *
     * @return OODBBean Bean freshly loaded from database.
     */
    public function storeAndLoad(OODBBean $bean): OODBBean
    {
        return R::load($this->tableName, (int) R::store($bean));
    }
}
