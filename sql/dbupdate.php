<#1>
<?php

use KPG\DML\classes\Database\Setup\RecordSetup;
use KPG\DML\classes\Database\Setup\LabelSetup;
use KPG\DML\classes\Database\Setup\DCSetup;

DCSetup::createTable();
LabelSetup::createTable();
RecordSetup::createTable();
?>