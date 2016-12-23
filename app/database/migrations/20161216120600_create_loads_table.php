<?php

use Phinx\Migration\AbstractMigration;
use Phinx\Db\Adapter\MysqlAdapter;

class CreateLoadsTable extends AbstractMigration
{
    /**
     * Change Method.
     */
    public function change()
    {
        $table = $this->table('loads', ['collation' => 'utf8mb4_unicode_ci']);
        $table->addColumn('down', 'integer', ['limit' => MysqlAdapter::INT_MEDIUM, 'signed' => false])
            ->addColumn('ip', 'string', ['limit' => 15])
            ->addColumn('time', 'integer')
            ->create();
    }
}