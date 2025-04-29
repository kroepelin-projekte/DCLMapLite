<?php
declare(strict_types=1);

namespace KPG\DML\classes\Config\DataCollection;

use ILIAS\BackgroundTasks\Implementation\Bucket\State;
use ILIAS\BackgroundTasks\Implementation\Tasks\AbstractJob;
use ILIAS\BackgroundTasks\Implementation\Tasks\UserInteraction\UserInteractionOption;
use ILIAS\BackgroundTasks\Implementation\Values\ScalarValues\StringValue;
use ILIAS\BackgroundTasks\Task\UserInteraction\Option;
use ILIAS\Data\Result;
use ILIAS\BackgroundTasks\Types\SingleType;
use ILIAS\BackgroundTasks\Types\Type;
use KPG\DML\classes\Config\DataCollection\DCRecordModel;

/**
 * Represents a job responsible for preparing and processing data, saving records,
 * and notifying the observer of state changes during execution.
 */
class DataPreparationJob extends AbstractJob
{
    /**
     * Runs the process to save and manage records, notifying the observer of the state.
     *
     * @param array                           $input    An array containing the input data. The first element should have the table ID,
     *                                                  and the second element should contain serialized records data.
     * @param \ILIAS\BackgroundTasks\Observer $observer The observer used to handle state notifications and heartbeat updates.
     *
     * @return StringValue Returns a StringValue object containing either the saved records data or an error message in case of an exception.
     */
    public function run($input, \ILIAS\BackgroundTasks\Observer $observer): StringValue
    {
        try {
            $tbl_id = (int) $input[0]->getValue();
            $records = unserialize($input[1]->getValue());
            (new DCRecordModel())->saveFromArray($tbl_id, $records);

            $string = new StringValue();
            $observer->heartbeat();
            $observer->notifyState(State::SCHEDULED);
            $string->setValue(serialize($records));

        } catch (\Exception $e) {
            $string = new StringValue();
            $string->setValue($e->getMessage());
        }
        return $string;
    }

    /**
     * @return \ILIAS\BackgroundTasks\Types\Type|null
     */
    public function getInputType(): ?Type
    {
        return null;
    }

    /**
     * @return \ILIAS\BackgroundTasks\Types\Type
     */
    public function getOutputType(): Type
    {
        return new SingleType(StringValue::class);
    }

    /**
     * @return bool
     */
    public function isStateless(): bool
    {
        return true;
    }

    /**
     * @return int
     */
    public function getExpectedTimeOfTaskInSeconds(): int
    {
        return 1000;
    }

    /**
     * @return array|\ILIAS\BackgroundTasks\Types\Type[]
     */
    public function getInputTypes(): array
    {
        return [];
    }
}
