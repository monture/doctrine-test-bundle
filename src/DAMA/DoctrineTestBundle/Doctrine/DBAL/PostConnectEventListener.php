<?php

namespace DAMA\DoctrineTestBundle\Doctrine\DBAL;

use Doctrine\DBAL\Event\ConnectionEventArgs;

/**
 * @deprecated since dama/doctrine-test-bundle v7.2.0
 */
class PostConnectEventListener
{
    public function postConnect(ConnectionEventArgs $args): void
    {
        // can be disabled at runtime
        if (!StaticDriver::isKeepStaticConnections()
            // new approach without the need for this listener
            || $args->getConnection() instanceof Connection
            || $args->getConnection() instanceof PrimaryReadReplicaConnection
        ) {
            return;
        }

        // The underlying connection already has a transaction started.
        // We start a transaction on the connection as well
        // so the internal state ($_transactionNestingLevel) is in sync with the underlying connection.
        $args->getConnection()->beginTransaction();
    }
}
