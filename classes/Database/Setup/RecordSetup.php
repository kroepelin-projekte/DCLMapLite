<?php

namespace KPG\DML\classes\Database\Setup;

use KPG\DML\classes\Config\DataCollection\DCRecordModel;

class RecordSetup
{
    /**
     * Creates the database table for DCRecordModel if it does not already exist.
     * The table includes fields such as id, tbl_id, institution, street, zip, location, and others.
     * Sets 'id' as the primary key.
     *
     * @return void
     * @global $ilDB The global database connection object.
     *
     */
    public static function createTable(): void
    {
        global $ilDB;
        if (!$ilDB->tableExists(DCRecordModel::returnDbTableName())) {
            $fields = array(
                'id' => array(
                    'type' => 'integer',
                    'length' => 8,
                    'notnull' => true,
                    'default' => 0
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
                'color' => array(
                    'type' => 'text',
                    'length' => 8,
                    'notnull' => false
                ),
                'lat' => array(
                    'type' => 'float',
                    'length' => 8,
                    'notnull' => false
                ),
                'lon' => array(
                    'type' => 'float',
                    'length' => 8,
                    'notnull' => false
                ),
                'last_update' => array(
                    'type' => 'timestamp',
                    'length' => 8,
                    'notnull' => false
                ),
            );

            $ilDB->createTable(DCRecordModel::returnDbTableName(), $fields, true, true);
            $ilDB->addPrimaryKey(DCRecordModel::returnDbTableName(), ['id']);
        }
    }

    /**
     * Drops the database table for DCRecordModel if it exists.
     *
     * @return void
     * @global $ilDB the global database connection object.
     *
     */
    public static function dropTable(): void
    {
        global $ilDB;
        if ($ilDB->tableExists(DCRecordModel::returnDbTableName())) {
            $ilDB->dropTable(DCRecordModel::returnDbTableName());
        }
    }

}