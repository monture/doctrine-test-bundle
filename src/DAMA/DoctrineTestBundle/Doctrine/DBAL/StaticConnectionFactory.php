<?php

namespace DAMA\DoctrineTestBundle\Doctrine\DBAL;

use Doctrine\Bundle\DoctrineBundle\ConnectionFactory;
use Doctrine\Common\EventManager;
use Doctrine\DBAL\Configuration;
use Doctrine\DBAL\Connection as DBALConnection;
use Doctrine\DBAL\Connections\PrimaryReadReplicaConnection as DBALPrimaryReadReplicaConnection;

class StaticConnectionFactory extends ConnectionFactory
{
    /**
     * @var ConnectionFactory
     */
    private $decoratedFactory;

    public function __construct(ConnectionFactory $decoratedFactory)
    {
        parent::__construct([]);
        $this->decoratedFactory = $decoratedFactory;
    }

    public function createConnection(array $params, Configuration $config = null, EventManager $eventManager = null, array $mappingTypes = []): DBALConnection
    {
        if (!StaticDriver::isKeepStaticConnections() || !isset($params['dama.keep_static']) || !$params['dama.keep_static']) {
            return $this->decoratedFactory->createConnection($params, $config, $eventManager, $mappingTypes);
        }

        $params['wrapperClass'] = $this->getWrapperClass($params);

        $connection = $this->decoratedFactory->createConnection($params, $config, $eventManager, $mappingTypes);

        // Make sure we use savepoints to be able to easily roll back nested transactions
        if ($connection->getDriver()->getDatabasePlatform()->supportsSavepoints()) {
            $connection->setNestTransactionsWithSavepoints(true);
        }

        return $connection;
    }

    /**
     * @return class-string<DBALConnection>
     */
    private function getWrapperClass(array $params): string
    {
        if (!isset($params['wrapperClass'])
            || $params['wrapperClass'] === DBALConnection::class
        ) {
            return Connection::class;
        }

        if ($params['wrapperClass'] === DBALPrimaryReadReplicaConnection::class) {
            return PrimaryReadReplicaConnection::class;
        }

        @trigger_deprecation(
            'dama/doctrine-test-bundle',
            'v7.2.0',
            'Customizing the DBAL Connection wrapperClass is deprecated and will not be supported anymore on v8.0 when using this bundle.'
        );

        return $params['wrapperClass'];
    }
}
