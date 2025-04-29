<?php

use KPG\DML\classes\Database\Setup\DCSetup;
use KPG\DML\classes\Database\Setup\LabelSetup;
use KPG\DML\classes\Database\Setup\RecordSetup;

class ilDCLMapLitePlugin extends ilPageComponentPlugin
{

    public const PLUGIN_ID = 'kpg_dclmap_lite';
    public const PLUGIN_NAME = 'DCLMapLite';

    /**
     * Checks if the provided type is a valid parent type for the plugin.
     *
     * @param string $a_type The type to validate.
     * @return bool Always returns true for this implementation.
     */
    public function isValidParentType(string $a_type): bool
    {
        return true;
    }

    /**
     * Executes necessary actions after the plugin is uninstalled.
     * This includes dropping database tables related to this plugin.
     *
     * @return void
     */
    protected function afterUninstall(): void
    {
        parent::afterUninstall();
        RecordSetup::dropTable();
        LabelSetup::dropTable();
        DCSetup::dropTable();
    }

    /**
     * Retrieves the plugin name.
     *
     * @return string The name of the plugin.
     */
    public function getPluginName(): string
    {
        return self::PLUGIN_NAME;
    }
}