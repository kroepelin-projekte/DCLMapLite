<?php

namespace KPG\DML\classes\Database\Setup;

use KPG\DML\classes\Config\DataCollection\DCModel;

class DCSetup
{
    /**
     * Creates the database table for the DCModel if it does not already exist.
     * Fields are defined according to the DCModel's structure.
     * Also creates a sequence and sets a primary key for the table.
     *
     * @return void
     * @global \ilDBInterface $ilDB Global database object
     */
    public static function createTable(): void
    {
        global $ilDB;
        if (!$ilDB->tableExists(DCModel::returnDbTableName())) {
            $fields = array(
                'id' => array(
                    'type' => 'integer',
                    'length' => 8,
                    'notnull' => true
                ),
                'tbl_id' => array(
                    'type' => 'integer',
                    'length' => 8,
                    'notnull' => false
                ),
                'institution' => array(
                    'type' => 'text',
                    'length' => 256,
                    'notnull' => true
                ),
                'street' => array(
                    'type' => 'text',
                    'length' => 256,
                    'notnull' => true
                ),
                'zip' => array(
                    'type' => 'text',
                    'length' => 256,
                    'notnull' => true
                ),
                'location' => array(
                    'type' => 'text',
                    'length' => 256,
                    'notnull' => true
                ),
                'website' => array(
                    'type' => 'text',
                    'length' => 256,
                    'notnull' => false
                ),
            );

            $ilDB->createTable(DCModel::returnDbTableName(), $fields);
            $ilDB->createSequence(DCModel::returnDbTableName());
            $ilDB->addPrimaryKey(DCModel::returnDbTableName(), ['id']);
        }
    }

    /**
     * Drops the database table and sequence related to the DCModel if they exist.
     *
     * @return void
     * @global \ilDBInterface $ilDB Global database object
     */
    public static function dropTable(): void
    {
        global $ilDB;
        if ($ilDB->tableExists(DCModel::returnDbTableName())) {
            $ilDB->dropTable(DCModel::returnDbTableName());
        }
        if ($ilDB->sequenceExists(DCModel::returnDbTableName() . '_seq')) {
            $ilDB->dropSequence(DCModel::returnDbTableName() . '_seq');
        }
    }

}