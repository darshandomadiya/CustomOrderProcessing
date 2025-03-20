<?php
namespace DarshanDomadiya\CustomOrderProcessing\Setup;

use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

class InstallSchema implements InstallSchemaInterface
{
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;
        $installer->startSetup();

        $table = $installer->getConnection()->newTable($installer->getTable('order_status_log'))
            ->addColumn('id', Table::TYPE_INTEGER, null, ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true])
            ->addColumn('order_id', Table::TYPE_INTEGER, null, ['nullable' => false])
            ->addColumn('old_status', Table::TYPE_TEXT, 255, ['nullable' => false])
            ->addColumn('new_status', Table::TYPE_TEXT, 255, ['nullable' => false])
            ->addColumn('timestamp', Table::TYPE_TIMESTAMP, null, ['nullable' => false, 'default' => Table::TIMESTAMP_INIT]);

        $installer->getConnection()->createTable($table);
        $installer->endSetup();
    }
}
