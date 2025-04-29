<?php

namespace KPG\DML\classes\Database\Setup;

use KPG\DML\classes\Config\Label\LModel;

class LabelSetup
{
    /**
     * Creates a new database table if it does not already exist.
     *
     * The table includes the following fields:
     * - rec_id: Integer, primary key, not nullable.
     * - latitude: Text, max length 256, not nullable.
     * - longitude: Text, max length 256, not nullable.
     * - title_search: Text, max length 256, not nullable.
     * - street: Text, max length 256, not nullable.
     * - zip: Text, max length 256, nullable.
     * - location: Text, max length 256, nullable.
     * - submit_button: Text, max length 256, nullable.
     * - reset_button: Text, max length 256, nullable.
     * - marker: Text, max length 256, nullable.
     * - location_marker: Text, max length 256, nullable.
     * - location_circle: Text, max length 256, nullable.
     * - email: Text, max length 256, nullable.
     * - website: Text, max length 256, nullable.
     * - result_title: Text, max length 256, nullable.
     * - perimeter: Text, max length 256, nullable.
     *
     * Adds "rec_id" as the primary key to the table.
     *
     * @return void
     */
    public static function createTable(): void
    {
        global $ilDB;
        if (!$ilDB->tableExists(LModel::returnDbTableName())) {
            $fields = array(
                'rec_id' => array(
                    'type' => 'integer',
                    'length' => 4,
                    'notnull' => true
                ),
                'latitude' => array(
                    'type' => 'text',
                    'length' => 256,
                    'notnull' => true
                ),
                'longitude' => array(
                    'type' => 'text',
                    'length' => 256,
                    'notnull' => true
                ),
                'title_search' => array(
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
                    'notnull' => false
                ),
                'location' => array(
                    'type' => 'text',
                    'length' => 256,
                    'notnull' => false
                ),
                'submit_button' => array(
                    'type' => 'text',
                    'length' => 256,
                    'notnull' => false
                ),
                'reset_button' => array(
                    'type' => 'text',
                    'length' => 256,
                    'notnull' => false
                ),
                'marker' => array(
                    'type' => 'text',
                    'length' => 256,
                    'notnull' => false
                ),
                'location_marker' => array(
                    'type' => 'text',
                    'length' => 256,
                    'notnull' => false
                ),
                'location_circle' => array(
                    'type' => 'text',
                    'length' => 256,
                    'notnull' => false
                ),
                'website' => array(
                    'type' => 'text',
                    'length' => 256,
                    'notnull' => false
                ),
                'perimeter' => array(
                    'type' => 'text',
                    'length' => 256,
                    'notnull' => false
                )
            );

            $ilDB->createTable(LModel::returnDbTableName(), $fields, true, true);
            $ilDB->addPrimaryKey(LModel::returnDbTableName(), ['rec_id']);
        }
    }

    /**
     * Drops the database table if it exists.
     *
     * @return void
     */
    public static function dropTable(): void
    {
        global $ilDB;
        if ($ilDB->tableExists(LModel::returnDbTableName())) {
            $ilDB->dropTable(LModel::returnDbTableName());
        }
    }

}