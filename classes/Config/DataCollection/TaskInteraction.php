<?php
declare(strict_types=1);

namespace KPG\DML\classes\Config\DataCollection;

use ILIAS\BackgroundTasks\Bucket;
use ILIAS\BackgroundTasks\Implementation\Tasks\AbstractUserInteraction;
use ILIAS\BackgroundTasks\Implementation\Tasks\UserInteraction\UserInteractionOption;
use ILIAS\BackgroundTasks\Implementation\Values\ScalarValues\IntegerValue;
use ILIAS\BackgroundTasks\Implementation\Values\ScalarValues\StringValue;
use ILIAS\BackgroundTasks\Task\UserInteraction\Option;
use ILIAS\BackgroundTasks\Types\SingleType;
use ILIAS\BackgroundTasks\Types\Type;
use ILIAS\BackgroundTasks\Value;
use KPG\DML\classes\Util\Language;

class TaskInteraction extends AbstractUserInteraction
{
    use Language;
    public function getMessage(array $input): string
    {
        return self::getLang('backgroundtask_success');
    }

    /**
     * @inheritDoc
     */
    public function getInputTypes(): array
    {
        return [];
    }

    /**
     * @inheritDoc
     */
    public function getOutputType(): Type
    {
        return new SingleType(StringValue::class);
    }

    /**
     * @inheritDoc
     */
    public function interaction(array $input, Option $user_selected_option, Bucket $bucket): Value
    {
        global $DIC;
        $input = $input[0];
        if ($user_selected_option->getValue() == "download") {
            $outputter = new \ilPHPOutputDelivery();
            $outputter->start("DML Data Import");
            $data = unserialize($input->getValue());
            if ($data === false) {
                echo "The string could not be deserialized.";
                exit;
            }
            echo "<table border='1' cellspacing='0' cellpadding='5' style='border-collapse: collapse; width: 100%;'>";
            echo "<thead>";
            echo "<tr>";

            $headers = array_keys(reset($data));
            foreach ($headers as $header) {
                echo "<th>" . htmlspecialchars($header) . "</th>";
            }
            echo "</tr>";
            echo "</thead>";
            echo "<tbody>";

            foreach ($data as $row) {
                echo "<tr>";
                foreach ($row as $column) {
                    $column = $column !== null ? (string) $column : "-";
                    echo "<td>" . htmlspecialchars($column !== null ? $column : "-") . "</td>";
                }
                echo "</tr>";
            }

            echo "</tbody>";
            echo "</table>";
            $outputter->stop();
        }
        return $input;
    }

    /**
     * @inheritDoc
     */
    public function getOptions(array $input): array
    {
        return [new UserInteractionOption("download", "download"),];
    }

    /**
     * @inheritDoc
     */
    public function isFinal(): bool
    {
        return true;
    }
}
