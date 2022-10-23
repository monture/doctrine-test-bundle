<?php

declare(strict_types=1);

namespace DAMA\DoctrineTestBundle\Doctrine\DBAL;

use Doctrine\DBAL\Connections\PrimaryReadReplicaConnection as DBALPrimaryReadReplicaConnection;

final class PrimaryReadReplicaConnection extends DBALPrimaryReadReplicaConnection
{
    /**
     * @param string|null $connectionName
     */
    public function connect($connectionName = null): bool
    {
        if (!parent::connect($connectionName)) {
            return false;
        }

        // The underlying connection already has a transaction started.
        // We start a transaction on the connection as well
        // so the internal state ($_transactionNestingLevel) is in sync with the underlying connection.
        $this->beginTransaction();

        return true;
    }
}
