<?php

use ILIAS\BackgroundTasks\Implementation\Bucket\BasicBucket;
use ILIAS\BackgroundTasks\Implementation\Tasks\DownloadInteger;
use ILIAS\BackgroundTasks\Implementation\Tasks\PlusJob;
use ILIAS\DI\Container;
use ILIAS\UI\Factory;
use ILIAS\UI\Renderer;
use KPG\DML\classes\Config\DataCollection\DataCollectionView;
use KPG\DML\classes\Config\DataCollection\DCModel;
use KPG\DML\classes\Config\DataCollection\DCRecordModel;
use KPG\DML\classes\Config\DataCollection\DataPreparationTask;
use KPG\DML\classes\Config\Label\LabelView;
use KPG\DML\classes\Database\Setup\DCSetup;
use KPG\DML\classes\Interfaces\Constants\ConstantConfig;
use KPG\DML\classes\Output\OutputController;
use KPG\DML\classes\Util\Color;
use KPG\DML\classes\Util\DataCollection;
use KPG\DML\classes\Util\Language;

/**
 * @ilCtrl_isCalledBy ilDCLMapLitePluginGUI: ilPCPluggedGUI
 */
class ilDCLMapLitePluginGUI extends ilPageComponentPluginGUI implements ConstantConfig
{
    use DataCollection;
    use Color;
    use Language;

    private ilCtrl $ilCtrl;
    private OutputController $controller;
    private Container $dic;
    private ilGlobalTemplateInterface $tpl;
    private Renderer $renderer;
    private Factory $factory;

    public function __construct()
    {
        global $DIC;
        parent::__construct();
        $this->dic = $DIC;
        $this->tpl = $DIC->ui()->mainTemplate();
        $this->ilCtrl = $DIC->ctrl();
        $this->renderer = $DIC->ui()->renderer();
        $this->factory = $this->dic->ui()->factory();

        $this->controller = new OutputController();
    }

    /**
     * Executes the command based on the next class or command obtained from $ilCtrl.
     *
     * @return void
     */
    public function executeCommand(): void
    {
        $next_class = $this->ilCtrl->getNextClass();
        switch ($next_class) {
            default:
                $cmd = $this->ilCtrl->getCmd();
                if (in_array($cmd, array(
                    'create',
                    'cancel',
                    'edit',
                    'update',
                    self::CONFIG_DATA_COLLECTION_CMD_INIT,
                    self::CMD_UPDATE_DATACOLLECTION,
                    self::CONFIG_LABEL_CMD_INIT,
                    self::CONFIG_LABELS_CMD_SAVE,
                    self::CONFIG_LABELS_CMD_UPDATE,
                    self::CMD_UPDATE_DATACOLLECTION_RECORDS,
                ))) {
                    $this->$cmd();
                }
                break;
        }
    }

    /**
     * Sets the tabs in the UI for the plugin interface.
     *
     * @param string $tab_id ID of the tab to be activated.
     * @param bool   $create Indicates whether creation mode is active (default: false).
     * @return void
     * @throws ilCtrlException
     */
    public static function setTabs(string $tab_id, $create = false): void
    {
        global $DIC;
        $DIC->tabs()->addTab(
            self::CONFIG_DATA_COLLECTION_CMD_INIT,
            self::getLang(self::CONFIG_DATA_COLLECTION_TAB_TITLE),
            $DIC->ctrl()->getLinkTargetByClass(\ilDCLMapLitePluginGUI::class, self::CONFIG_DATA_COLLECTION_CMD_INIT)
        );
        if (!$create) {
            $DIC->tabs()->addTab(
                self::CONFIG_LABEL_CMD_INIT,
                self::getLang(self::CONFIG_LABEL_TAB_TITLE),
                $DIC->ctrl()->getLinkTargetByClass(\ilDCLMapLitePluginGUI::class, self::CONFIG_LABEL_CMD_INIT)
            );
        }
        $DIC->tabs()->activateTab($tab_id);
    }

    /**
     * Initializes the data collection editing in create mode.
     *
     * @return void
     * @throws ilCtrlException
     */
    public function insert(): void
    {
        $this->editDataCollection(true);
    }

    /**
     * Initializes the data collection editing in edit mode.
     *
     * @return void
     * @throws ilCtrlException
     */
    public function edit(): void
    {
        $this->editDataCollection();
    }

    /**
     * Handles editing of the data collection and sets up the UI components accordingly.
     *
     * @param bool $create Indicates whether this is a create or edit operation (default: false).
     * @return void
     * @throws ilCtrlException
     */
    public function editDataCollection(bool $create = false): void
    {
        global $DIC;
        self::setTabs(self::CONFIG_DATA_COLLECTION_CMD_INIT, $create);
        $view = new DataCollectionView();
        $update_button_action = $DIC->ctrl()->getLinkTargetByClass(
            \ilDCLMapLitePluginGUI::class, self::CMD_UPDATE_DATACOLLECTION_RECORDS
        );
        $properties = $this->getProperties();
        if (!$create) {
            if (isset($properties['dc_tbl_id'])) {
                $update_button_action = $update_button_action . '&dc_tbl_id=' . $properties['dc_tbl_id'];
            } else {
                $update_button_action = $update_button_action;
            }
            $update_button = $this->factory->button()->standard(
                self::getLang(self::UPDATE_RECORDS), $update_button_action
            );
        }

        $scope = $this->factory->legacy(
            "<br /><br /><br /><div id='repository-picker' style='border: 2px solid grey; padding: 20px; display: inline-block;
'><div id='scope-view'>".self::getLang('no_datacollection_choose')."</div><br />" . $this->renderer->render(
                [$view->dataCollectionFilterModal()]
            ) . "</div>"
        );
        if ($create) {
            $panel = $this->factory->panel()->standard(
                self::getLang(self::CONFIG_DATA_COLLECTION_TAB_TITLE), [$scope, $view->initForm($properties, $create)]
            );
        } else {
            $panel = $this->factory->panel()->standard(
                self::getLang(self::CONFIG_DATA_COLLECTION_TAB_TITLE),
                [$update_button, $scope, $view->initForm($properties, $create)]
            );
        }


        $this->tpl->setContent(
            $this->renderer->render([$panel])
        );
        if(array_key_exists('dc_ref_id', $properties)) {
            $obj_datacollection = new ilObjDataCollection($properties['dc_ref_id'], true);
            $title = $obj_datacollection->getTitle();
            $ref_id = $properties['dc_ref_id'];
        } else {
            $title = self::getLang('no_datacollection_choose');
            $ref_id = 0;
        }

        $this->tpl->addOnLoadCode(<<<JS
            let hiddenInput = document.querySelectorAll('input[type="hidden"]');
            let scopeView = document.getElementById('scope-view');
           
            let ref_id = $ref_id
            let title = "$title"
            console.log("ref_ID: " + ref_id + "title: " + title + "")      
            if (hiddenInput.length > 0) {
                hiddenInput[0].value = ref_id;
            }
            scopeView.innerText = "Datensammlung: " + title
        JS);
    }

    /**
     * Handles the editing of labels and sets up the UI components accordingly.
     *
     * @return void
     * @throws ilCtrlException
     */
    public function editLabel(): void
    {
        self::setTabs(self::CONFIG_LABEL_CMD_INIT);
        $view = new LabelView();
        $this->tpl->setContent(
            $this->renderer->render($view->initForm($this->getProperties()))
        );
    }

    /**
     * Creates a new data collection and validates the input data.
     *
     * @return void
     * @throws Exception
     */
    public function create(): void
    {
        $view = new DataCollectionView();
        $form = $view->initForm(null, true);
        $form = $form->withRequest($this->dic->http()->request());
        $result = $form->getData();

        if ($form->getError()) {
            return;
        }

        try {
            $data_collection_ref_id = (int) $result['adress']['dc_tbl_id'];
            if($data_collection_ref_id == 0) {
                $this->tpl->setOnScreenMessage(
                    \ilGlobalTemplateInterface::MESSAGE_TYPE_FAILURE,
                    self::getLang('msg_failed_no_datacollection_choose'),
                    true
                );
                $this->returnToParent();
                return;
            }
            unset($result['adress']['dc_tbl_id']);

            $tbl_id = self::getDCLTableByRefid($data_collection_ref_id, $result['adress']['institution']);
            if ($tbl_id === null) {
                throw new ilException(self::getLang('msg_failed_tbl_not_found'));
            }
            $this->validateDclTableFields(
                $tbl_id,
                [$result['adress']]
            );
        } catch (ilException $e) {
            $this->tpl->setOnScreenMessage(
                \ilGlobalTemplateInterface::MESSAGE_TYPE_FAILURE,
                $e->getMessage(),
                true
            );
            $this->returnToParent();
            return;
        }

        $properties = [
            'dc_tbl_id' => $tbl_id,
            'dc_ref_id' => $data_collection_ref_id,
            'dc_institution' => $result['adress']['institution'],
            'dc_street' => $result['adress']['street'],
            'dc_zip' => $result['adress']['zip'],
            'dc_location' => $result['adress']['location'],
            'dc_website' => $result['adress']['website'],
            'lbl_latitude' => '51.163361',
            'lbl_longitude' => '10.447683',
            'lbl_title_search' => 'Sucheingabe',
            'lbl_street' => 'Straße',
            'lbl_zip' => 'Postleitzahl',
            'lbl_location' => 'Ort',
            'lbl_submit_button' => 'Suchen',
            'lbl_reset_button' => 'Zurücksetzen',
            'lbl_marker' => 'Mein Standpunkt',
            'lbl_location_marker' => '#000',
            'lbl_location_circle' => '#95C11F',
            'lbl_website' => 'Webseite',
            'lbl_perimeter' => 'Umkreis'
        ];

        $selectionProperties = $this->getSelectionFieldProperties($tbl_id);
        foreach ($selectionProperties as $selectionProperty) {
            foreach ($selectionProperty as $key => $value) {
                $properties[$key] = $value;
            }
        }
        /**
         * DCLMap Records
         */
        $merge = array_merge($result['adress']);
        $field_ids = $this->getRecordFieldIDs($merge, $tbl_id);
        $records = $this->getRecordsFromDclTable($tbl_id, $field_ids);

        DataPreparationTask::updateDataCollection([$tbl_id, serialize($records)]);

        if ($this->createElement($properties)) {
            $this->tpl->setOnScreenMessage('success', self::getLang('msg_create_success'), true);
            $this->returnToParent();
        }
    }

    /**
     * Updates an existing data collection and validates the input data.
     *
     * @return void
     * @throws ilCtrlException
     * @throws Exception
     */
    public function updateDataCollection(): void
    {
        $view = new DataCollectionView();
        $form = $view->initForm(null, true);
        $form = $form->withRequest($this->dic->http()->request());
        $result = $form->getData();
        if ($form->getError()) {
            return;
        }

        try {
            $data_collection_ref_id = (int) $result['adress']['dc_tbl_id'];
            if($data_collection_ref_id == 0) {
                $this->tpl->setOnScreenMessage(
                    \ilGlobalTemplateInterface::MESSAGE_TYPE_FAILURE,
                    self::getLang('msg_failed_no_datacollection_choose'),
                    true
                );
                $this->returnToParent();
                return;
            }
            unset($result['adress']['dc_tbl_id']);

            $tbl_id = self::getDCLTableByRefid($data_collection_ref_id, $result['adress']['institution']);
            if ($tbl_id === null) {
                throw new ilException(self::getLang('msg_failed_tbl_not_found'));
            }
            $this->validateDclTableFields(
                $tbl_id,
                [$result['adress']]
            );
        } catch (ilException $e) {
            $this->tpl->setOnScreenMessage(
                \ilGlobalTemplateInterface::MESSAGE_TYPE_FAILURE,
                $e->getMessage(),
                true
            );
            $this->returnToParent();
            return;
        }

        $properties = [
            'dc_tbl_id' => $tbl_id,
            'dc_ref_id' => $data_collection_ref_id,
            'dc_institution' => $result['adress']['institution'],
            'dc_street' => $result['adress']['street'],
            'dc_zip' => $result['adress']['zip'],
            'dc_location' => $result['adress']['location'],
            'dc_website' => $result['adress']['website'],
            'lbl_latitude' => '51.163361',
            'lbl_longitude' => '10.447683',
            'lbl_title_search' => 'Sucheingabe',
            'lbl_street' => 'Straße',
            'lbl_zip' => 'Postleitzahl',
            'lbl_location' => 'Ort',
            'lbl_submit_button' => 'Suchen',
            'lbl_reset_button' => 'Zurücksetzen',
            'lbl_marker' => 'Mein Standpunkt',
            'lbl_location_marker' => '#000',
            'lbl_location_circle' => '#95C11F',
            'lbl_website' => 'Webseite',
            'lbl_perimeter' => 'Umkreis'
        ];

        $selectionProperties = $this->getSelectionFieldProperties($tbl_id);
        foreach ($selectionProperties as $selectionProperty) {
            foreach ($selectionProperty as $key => $value) {
                $properties[$key] = $value;
            }
        }

        $merge = array_merge($result['adress']);
        $field_ids = $this->getRecordFieldIDs($merge, $tbl_id);
        $records = $this->getRecordsFromDclTable($tbl_id, $field_ids);

        DataPreparationTask::updateDataCollection([$tbl_id, serialize($records)]);

        if ($this->updateElement($properties)) {
            $this->tpl->setOnScreenMessage('success', self::getLang('msg_create_success'), true);
            $this->returnToParent();
        }
    }

    /**
     * Updates the records of an existing data collection and saves them to the database.
     *
     * @return void
     * @throws Exception
     */
    public function updateDataCollectionRecords(): void
    {
        $result['institution'] = $this->getProperties()['dc_institution'];
        $result['street'] = $this->getProperties()['dc_street'];
        $result['zip'] = $this->getProperties()['dc_zip'];
        $result['location'] = $this->getProperties()['dc_location'];
        $result['website'] = $this->getProperties()['dc_website'];

        $dc_tbl_id = $this->getProperties()['dc_tbl_id'];
        $field_ids = $this->getRecordFieldIDs($result, $dc_tbl_id);
        $records = $this->getRecordsFromDclTable($dc_tbl_id, $field_ids);

        DataPreparationTask::updateDataCollection([$dc_tbl_id, serialize($records)]);

        $this->tpl->setOnScreenMessage('success', self::getLang(self::MSG_SUCCESS_DATACOLLECTION_UPDATE), true);
        $this->returnToParent();
    }

    /**
     * Updates the label properties and triggers the label editing interface.
     *
     * @return void
     * @throws ilCtrlException
     */
    public function updateLabel(): void
    {
        $properties = $this->getProperties();

        $view = new LabelView();
        $form = $view->initForm($properties);
        $form = $form->withRequest($this->dic->http()->request());
        $result = $form->getData();
        if ($form->getError()) {
            return;
        }
        $properties['lbl_latitude'] = $result['label']['latitude'];
        $properties['lbl_longitude'] = $result['label']['longitude'];
        $properties['lbl_title_search'] = $result['label']['title_search'];
        $properties['lbl_street'] = $result['label']['street'];
        $properties['lbl_zip'] = $result['label']['zip'];
        $properties['lbl_location'] = $result['label']['location'];
        $properties['lbl_submit_button'] = $result['label']['submit_button'];
        $properties['lbl_reset_button'] = $result['label']['reset_button'];
        $properties['lbl_marker'] = $result['label']['marker'];
        $properties['lbl_location_marker'] = $this->rgbToHex(
            $result['label']['location_marker']->r(),
            $result['label']['location_marker']->g(),
            $result['label']['location_marker']->b()
        );
        $properties['lbl_location_circle'] = $this->rgbToHex(
            $result['label']['location_circle']->r(),
            $result['label']['location_circle']->g(),
            $result['label']['location_circle']->b()
        );
        $properties['lbl_website'] = $result['label']['website'];
        $properties['lbl_perimeter'] = $result['label']['perimeter'];

        $this->updateElement($properties);
        $this->tpl->setOnScreenMessage('success', self::getLang('msg_update_label'));
        $this->editLabel();
    }

    /**
     * Generates the HTML for the plugin element based on its mode and properties.
     *
     * @param string $a_mode         The display mode for the element.
     * @param array  $a_properties   The properties of the plugin element.
     * @param string $plugin_version The current version of the plugin.
     * @return string The HTML content for the element.
     */
    public function getElementHTML(string $a_mode, array $a_properties, string $plugin_version): string
    {
        if ($this->plugin->isActive()) {
            $properties = $a_properties;
            return $this->controller->showMap($properties);
        }
        return '';
    }

    /**
     * Cancels the operation and returns the user to the parent interface.
     *
     * @return void
     */
    public function cancel(): void
    {
        $this->returnToParent();
    }
}
