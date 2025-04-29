<?php

use ILIAS\UI\Component\Tree\TreeRecursion;
use KPG\DML\classes\Util\Language;
use ILIAS\UI\Component\Tree\Node\Node;

class RepositoryTree
{
    use Language;
    public array $allowed_types = [
        'cat',
        'catr',
        'crs',
        'crsr',
        'grp',
        'fold',
        'blog',
        'prg',
        'dcl'
    ];

    private \ILIAS\DI\Container $dic;
    private \ILIAS\UI\Factory $factory;
    private \ILIAS\UI\Renderer $renderer;
    private \ilObjUser $user;
    private \ilTree $tree;

    public function __construct()
    {
        global $DIC;

        $this->dic = $DIC;
        $this->factory = $DIC->ui()->factory();
        $this->renderer = $DIC->ui()->renderer();
        $this->user = $DIC->user();
        $this->tree = $DIC->repositoryTree();
    }

    /**
     * @param int|null $ref_id
     * @return int
     */
    public function getRefId(?int $ref_id = null): int
    {
        $time_limit_owner = $this->user->getTimeLimitOwner();
        if ($time_limit_owner === USER_FOLDER_ID) {
            $time_limit_owner = ROOT_FOLDER_ID;
        }

        if (!$ref_id) {
            return $time_limit_owner;
        } else {
            return $ref_id;
        }
    }

    /**
     * @param int|null $ref_id
     * @return string
     * @throws \ilDatabaseException
     * @throws \ilObjectNotFoundException
     */
    public function getExpandableTreeUI(?int $ref_id = null): string
    {
        $ref_id = $this->getRefId();
        $data = $this->tree->getChildsByTypeFilter($ref_id, $this->allowed_types);

        $recursion = new class ($this->allowed_types) implements \ILIAS\UI\Component\Tree\TreeRecursion {
            use Language;
            private \ilTree $tree;
            private array $allowed_object_types;
            private array $allowed_types_for_tree;

            public function __construct(array $allowed_object_types)
            {
                global $DIC;
                $this->tree = $DIC->repositoryTree();
                //$this->allowed_object_types = 'dcl';
                $this->allowed_types_for_tree = $allowed_object_types;
            }

            /**
             * @param $record
             * @param $environment
             * @return array
             */
            public function getChildren($record, $environment = null): array
            {
                return $this->tree->getChildsByTypeFilter((int) $record['ref_id'], ['dcl']);

            }

            /**
             * @param \ILIAS\UI\Implementation\Component\Tree\Node\Factory $factory
             * @param $record
             * @param $environment
             * @return \ILIAS\UI\Component\Tree\Node\Node
             */
            public function build($factory, $record, $environment = null): \ILIAS\UI\Component\Tree\Node\Node
            {
                $ref_id = $record['ref_id'];
                $obj_id = $record['obj_id'];
                $title = $record['title'];
                $type = $record['type'];

                $icon = $environment['icon_factory']->standard($record["type"], '');

                $label = '';
                if ($type === 'dcl') {
                    $label .= '<input type="radio" name="object_id" data-ref_id="' . $ref_id . '" data-title="' . $title . '" style="margin-right: 5px;">';
                    $label .= $record['title'] . ' ('. self::getLang('datacollection') .', ' . $ref_id . ')';
                } else {
                    $label .= $record['title'] . ' (' . $environment['lng']->txt($type) . ', ' . $ref_id . ')';
                }


                $node = $factory->simple($label, $icon);

                return $node;
            }
        };

        $environment = [
            'url' => $this->dic->http()->request()->getRequestTarget(),
            'icon_factory' => $this->factory->symbol()->icon(),
            'lng' => $this->dic->language(),
        ];

        $tree = $this->factory->tree()->expandable("Label", $recursion)
                              ->withEnvironment($environment)
                              ->withData($data);

        return '<div style="padding: 0px">' . $this->renderer->render([
                $this->factory->legacy('<h2>'. self::getLang('datacollection') .'</h2>'),
                $tree
            ]) . '</div>';
    }
}
