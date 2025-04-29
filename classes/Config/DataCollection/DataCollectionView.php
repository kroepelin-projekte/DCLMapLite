<?php

declare(strict_types=1);

namespace KPG\DML\classes\Config\DataCollection;

use ILIAS\UI\Component\Input\Container\Form\Standard;
use ILIAS\UI\Component\Input\Field\Section;
use KPG\DML\classes\Interfaces\Constants\ConstantConfig;
use KPG\DML\classes\Util\DataCollection;
use KPG\DML\classes\Util\Language;
use ILIAS\UI\Implementation\Component\Modal\Modal;

class DataCollectionView implements ConstantConfig
{
    use Language;
    use DataCollection;

    private $factory;
    private $dic;

    public function __construct(int $id = 0)
    {
        global $DIC;
        $this->dic = $DIC;
        $this->factory = $DIC->ui()->factory();
    }

    /**
     * Generates a section for selecting a data collection input.
     *
     * @param array|null $data Optional data to pre-fill the selection field.
     *                         - 'dc_tbl_id': The ID of the data collection table to pre-select.
     *
     * @return Section Returns a section with an input field for selecting a data collection table.
     */

    public function dataCollectionFilterModal(): array
    {
        $repository_tree = new \RepositoryTree();
        $modal = $this->factory->modal()->roundtrip(
            '',
            [
                $this->factory->legacy($repository_tree->getExpandableTreeUI()),
            ]
        );
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $modal = $modal->withRequest($this->dic->http()->request());
        }
        $lang_datacollection = self::getLang('datacollection');
        $primary_btn = $this->factory->button()->primary(self::getLang('datacollection_choose'), '')
                                     ->withOnClick($modal->getCloseSignal())
                                     ->withOnLoadCode(function ($id) {
                                         $lang_datacollection = self::getLang('datacollection');
                                         return <<<JS
        $id.onclick = function() {
            let hiddenInput = document.querySelectorAll('input[type="hidden"]');
            let scopeView = document.getElementById('scope-view');
            const radioButtons = document.querySelectorAll('input[name="object_id"]');
            let ref_id = null;
            let title = null;

            radioButtons.forEach(radio => {
                if (radio.checked) {
                    ref_id = radio.getAttribute('data-ref_id');
                    title = radio.getAttribute('data-title');
                }
            });
            
            if (hiddenInput.length > 0) {
                hiddenInput[0].value = ref_id;
            }
            scopeView.innerText = "$lang_datacollection " + title
        };
        JS;
                                     });

        $modal = $modal->withActionButtons([$primary_btn]);
        $button = $this->factory->button()->standard(self::getLang('datacollection_choose'), '')->withOnClick(
            $modal->getShowSignal()
        );
        $lang_no_datacollection_choose = self::getLang('no_datacollection_choose');
        $reset = $this->factory->button()->standard(self::getLang('datacollection_reset'), '')->withOnLoadCode(
            function ($id) {
                $lang_no_datacollection_choose = self::getLang('no_datacollection_choose');
                return <<<JS
        (function() {
            $id.addEventListener('click', () => {
                let hiddenInput = document.querySelectorAll('input[type="hidden"]');
                let scopeView = document.getElementById('scope-view');
                
                if (hiddenInput.length > 0) {
                    hiddenInput[0].value = 0; 
                }

                scopeView.innerText = "$lang_no_datacollection_choose"; 
            });
        })();
        JS;
            }
        );

        return ['modal' => $modal, 'edit' => $button, 'reset' => $reset];
    }

    /**
     * Generates the address section of the form.
     *
     * @param array|null $data Optional data to pre-fill the address fields.
     *                         - 'dc_institution': The institution field value.
     *                         - 'dc_street': The street field value.
     *                         - 'dc_zip': The ZIP/postal code field value.
     *                         - 'dc_location': The city/location field value.
     *
     * @return Section The generated address form section.
     */
    private function adressSection(?array $data = null): Section
    {
        $text_input_institution = $this->factory->input()->field()->text(
            self::getLang(self::CONFIG_DATA_COLLECTION_LANG_TABLE_FIELDS_INSTITUTION),
            self::getLang(self::CONFIG_DATA_COLLECTION_LANG_TABLE_FIELD_TEXT)
        )->withRequired(true)->withValue(!is_null($data) ? $data['dc_institution'] : '');

        $text_input_street = $this->factory->input()->field()->text(
            self::getLang(self::CONFIG_DATA_COLLECTION_LANG_TABLE_FIELDS_STREET),
            self::getLang(self::CONFIG_DATA_COLLECTION_LANG_TABLE_FIELD_TEXT)
        )->withRequired(true)->withValue(
            !is_null($data) ? $data['dc_street'] :
                'Straße'
        );

        $text_input_zip = $this->factory->input()->field()->text(
            self::getLang(self::CONFIG_DATA_COLLECTION_LANG_TABLE_FIELDS_ZIP),
            self::getLang(self::CONFIG_DATA_COLLECTION_LANG_TABLE_FIELD_TEXT)
        )->withRequired(true)->withValue(
            !is_null($data) ? $data['dc_zip'] :
                'PLZ'
        );

        $text_input_location = $this->factory->input()->field()->text(
            self::getLang(self::CONFIG_DATA_COLLECTION_LANG_TABLE_FIELDS_CITY),
            self::getLang(self::CONFIG_DATA_COLLECTION_LANG_TABLE_FIELD_TEXT)
        )->withRequired(true)->withValue(
            !is_null($data) ? $data['dc_location'] :
                'Ort'
        );
        $text_input_website = $this->factory->input()->field()->text(
            self::getLang(self::CONFIG_DATA_COLLECTION_LANG_TABLE_FIELDS_WEBSITE),
            self::getLang(self::CONFIG_DATA_COLLECTION_LANG_TABLE_FIELD_TEXT_URL)
        )->withValue(
            !is_null($data) ? $data['dc_website'] :
                ''
        );
        $hiddenfield = $this->factory->input()->field()->hidden();

        return $this->factory->input()->field()->section([
            'institution' => $text_input_institution,
            'street' => $text_input_street,
            'zip' => $text_input_zip,
            'location' => $text_input_location,
            'website' => $text_input_website,
            'dc_tbl_id' => $hiddenfield,
        ], self::getLang(self::CONFIG_ADRESS));
    }

    /**
     * Initializes a form for data collection input.
     *
     * @param array|null $data Optional data to pre-fill the form fields.
     * @param bool       $create Indicates if the form is for creating a new data collection (true)
     *                           or updating an existing one (false).
     *
     * @return Standard The generated form object.
     * @throws \ilCtrlException
     */
    public function initForm(?array $data = null, bool $create = false): Standard
    {
        if ($create) {
            $form_action = $this->dic->ctrl()->getLinkTargetByClass(
                \ilDCLMapLitePluginGUI::class, self::CREATE
            );
            $form = $this->factory->input()->container()->form()->standard(
                $form_action, [
                    //'dc_tbl_id' => $this->formDataCollectionInput(),
                    'adress' => $this->adressSection(),
                ]
            );
        } else {
            $form_action = $this->dic->ctrl()->getLinkTargetByClass(
                \ilDCLMapLitePluginGUI::class, self::CMD_UPDATE_DATACOLLECTION
            );
            $form = $this->factory->input()->container()->form()->standard(
                $form_action, [
                    //'dc_tbl_id' => $this->formDataCollectionInput($data),
                    'adress' => $this->adressSection($data),
                ]
            );
        }
        return $form;
    }
}