<?php
/**
 * Scandiweb_SocialLogin
 *
 * @category    Scandiweb
 * @package     Scandiweb_SocialLogin
 * @author      Viktors Vipolzovs <info@scandiweb.com>
 * @copyright   Copyright (c) 2016 Scandiweb, Ltd (http://scandiweb.com)
 */

namespace Scandiweb\SocialLogin\Setup;

use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

class InstallSchema implements InstallSchemaInterface
{

    /**
     * Installs DB schema for a module
     *
     * @param SchemaSetupInterface   $setup
     * @param ModuleContextInterface $context
     *
     * @return void
     */
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context) {
        $installer = $setup;
        $installer->startSetup();

        $table = $installer->getConnection()
            ->newTable(
                $installer->getTable('customer_provider')
            )
            ->addColumn(
                'id',
                Table::TYPE_INTEGER,
                null,
                ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
                'Autoincrement Id'
            )
            ->addColumn(
                'entity_id',
                Table::TYPE_INTEGER,
                null,
                ['unsigned' => true, 'nullable' => false, 'default' => '0'],
                'Entity Id'
            )
            ->addColumn(
                'user_id',
                Table::TYPE_TEXT,
                255,
                ['nullable' => false],
                'User id from social network'
            )
            ->addColumn(
                'provider',
                Table::TYPE_TEXT,
                255,
                ['nullable' => false],
                'Provider'
            )
            ->addIndex(
                $installer->getIdxName('customer_provider', ['entity_id']),
                ['entity_id']
            )
            ->addForeignKey(
                $installer->getFkName('customer_provider', 'entity_id', 'customer_entity', 'entity_id'),
                'entity_id',
                $installer->getTable('customer_entity'),
                'entity_id',
                Table::ACTION_CASCADE,
                Table::ACTION_CASCADE
            );

        $installer->getConnection()->createTable($table);
        $installer->endSetup();
    }
}