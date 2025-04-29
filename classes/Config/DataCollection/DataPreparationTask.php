<?php
declare(strict_types=1);

namespace KPG\DML\classes\Config\DataCollection;

use ILIAS\BackgroundTasks\Implementation\Bucket\BasicBucket;
use ILIAS\BackgroundTasks\Implementation\TaskManager\BasicTaskManager;
use ILIAS\BackgroundTasks\Implementation\TaskManager\MockObserver;
use ILIAS\BackgroundTasks\Implementation\Tasks\DownloadInteger;
use ILIAS\BackgroundTasks\Implementation\Values\ScalarValues\IntegerValue;
use ILIAS\MediaCast\BackgroundTasks\DownloadAllBackgroundTask;
use ILIAS\UI\Implementation\Component\Input\Field\DateTime;
use KPG\DML\classes\Interfaces\Constants\ConstantConfig;
use KPG\DML\classes\Config\DataCollection\AsyncTaskManager;
use KPG\DML\classes\Util\Language;

/**
 * Handles the updating of data collection by managing and executing background tasks
 * for importing and preparing data using an asynchronous task system.
 *
 * Implements the ConstantConfig interface for configuration constants.
 */
class DataPreparationTask implements ConstantConfig
{
    use Language;
    /**
     * Updates the data collection by preparing and executing background tasks
     * for data import and preparation using asynchronous task management.
     *
     * @param mixed $input The input data to be processed and prepared.
     *
     * @return void
     */
    public static function updateDataCollection($input): void
    {
        global $DIC;


        $bucket = new BasicBucket();
        $bucket->setUserId($DIC->user()->getId());

        $taskFactory = $DIC->backgroundTasks()->taskFactory();
        $task = $taskFactory->createTask(DataPreparationJob::class, $input);
        $interaction = $taskFactory->createTask(TaskInteraction::class, [$task]);
        $bucket->setTask($interaction);
        $bucket->setTitle(self::getLang('backgroundtask_title'));
        $bucket->setDescription('Data Import and Preparation for DCLMapLite');
        $bucket->heartbeat();

        $async_task_manager = new AsyncTaskManager($DIC->backgroundTasks()->persistence());
        $async_task_manager->runAsync();
        $async_task_manager->run($bucket);
    }
}
