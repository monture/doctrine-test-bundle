<?php

declare(strict_types=1);

namespace DAMA\DoctrineTestBundle\Doctrine\DBAL;

use Doctrine\DBAL\Connection as DBALConnection;

final class Connection extends DBALConnection
{
    public function connect(): bool
    {
        if (!parent::connect()) {
            return false;
        }

        // The underlying connection already has a transaction started.
        // We start a transaction on the connection as well
        // so the internal state ($_transactionNestingLevel) is in sync with the underlying connection.
        $this->beginTransaction();

        return true;
    }
}
