<?php

namespace KPG\DML\classes\Output;

use ILIAS\DI\Container;
use ilTemplate;
use KPG\DML\classes\Config\DataCollection\DCRecordModel;
use KPG\DML\classes\Util\Language;

class OutputView
{
    use Language;

    private static int $id_counter = 0;
    private Container $dic;

    public function __construct()
    {
        global $DIC;
        $this->dic = $DIC;
        self::$id_counter++;
    }

    /**
     * Loads meta information such as JavaScript and CSS files for the map view.
     *
     * @return void
     */
    public function loadMeta(): void
    {
        $this->dic->globalScreen()->layout()->meta()->addJs(
            'Customizing/global/plugins/Services/COPage/PageComponent/DCLMapLite/node_modules/leaflet/dist/leaflet.js'
        );
        $this->dic->globalScreen()->layout()->meta()->addJs(
            'Customizing/global/plugins/Services/COPage/PageComponent/DCLMapLite/js/main.js'
        );
        $this->dic->globalScreen()->layout()->meta()->addCss(
            'Customizing/global/plugins/Services/COPage/PageComponent/DCLMapLite/css/map_style.css'
        );
        $this->dic->globalScreen()->layout()->meta()->addCss(
            'Customizing/global/plugins/Services/COPage/PageComponent/DCLMapLite/node_modules/leaflet/dist/leaflet.css'
        );
    }

    /**
     * Loads the map template for the view.
     *
     * @return ilTemplate The initialized ILIAS template.
     */
    public function loadTemplate(): ilTemplate
    {
        $tpl = new ilTemplate(
            'Customizing/global/plugins/Services/COPage/PageComponent/DCLMapLite/templates/tpl.dcl_map.html',
            true,
            true
        );
        return $tpl;
    }

    /**
     * Checks if a given string is valid JSON.
     *
     * @param string $string The string to validate.
     * @return bool True if the string is valid JSON, false otherwise.
     */
    public function isJson($string)
    {
        if (empty($string)) {
            return false;
        }
        json_decode($string);
        return (json_last_error() == JSON_ERROR_NONE);
    }

    /**
     * Sets values for the ILIAS template with provided properties.
     * The method populates the template with a record's details for display.
     *
     * @param ilTemplate $tpl The template to populate.
     * @param array $properties An associative array of properties to set in the template.
     *
     * @return string The parsed and populated template as a string.
     * @throws \ilTemplateException
     */
    public function setValues(ilTemplate $tpl, array $properties): string
    {
        $id = self::$id_counter;

        $record = DCRecordModel::where(['tbl_id' => $properties['dc_tbl_id']])
            ->getArray();

        $group_identifier = "grp";

        $tpl->setCurrentBlock('SELECTION_GROUP_BUTTONS');
        $tpl->setVariable('DC_MARKER_GROUP_ID', 1);
        $tpl->setVariable('DC_MARKER_GROUP_TITLE', $properties['title']);
        $tpl->setVariable('DC_MARKER_GROUP_COLOR', $properties['color']);
        $tpl->setVariable('GROUP_IDENTIFIER', $group_identifier);
        $tpl->parseCurrentBlock();
        $tpl->setCurrentBlock('DC_MARKER_GROUPS');
        $tpl->setVariable(
            'DC_MARKER_GROUP',
            json_encode(
                $record,
                JSON_UNESCAPED_UNICODE |
                JSON_UNESCAPED_SLASHES |
                JSON_FORCE_OBJECT |
                JSON_NUMERIC_CHECK
            )
        );
        $tpl->setVariable('DC_MARKER_GROUP_ID', 1);
        $tpl->setVariable('DC_MARKER_GROUP_TITLE', $properties['title']);
        $tpl->setVariable('DC_MARKER_GROUP_COLOR', $properties['color']);
        $tpl->parseCurrentBlock();

        foreach ($record as $item) {
            $website = $this->isJson($item['website']) ? json_decode($item['website'])->link : $item['website'];

            if ($website !== null) {
                if (strpos($website, 'https://') !== 0) {
                    $website = 'https://' . $website;
                }
            }
            $tpl->setCurrentBlock('RECORD');
            $tpl->setVariable('INSTITUTION', $item['institution']);
            $tpl->setVariable('STREET', $item['street']);
            $tpl->setVariable('ZIP', $item['zip']);
            $tpl->setVariable('LOCATION', $item['location']);
            if (!empty($item['name'])) {
                $tpl->setVariable('NAME', !empty($item['name']) ? $item['name'] : '');
            }
            if (!empty($website)) {
                $tpl->setVariable('WEBSITE', $website);
                $tpl->setVariable(
                    'WEBSITE_TITLE',
                    !empty($website)
                        ? "<img class='contact-icon' src = 'Customizing/global/plugins/Services/COPage/PageComponent/DCLMapLite/images/icons/icon_webr.svg'/>"
                        . self::getLang('website_title')
                        : ''
                );
            }
            $tpl->setVariable('MEMBER_LAT', $item['lat']);
            $tpl->setVariable('MEMBER_LON', $item['lon']);
            $tpl->setVariable('ID', $item['id']);
            $tpl->setVariable('GROUP_IDENTIFIER', $group_identifier);
            $tpl->setVariable('GROUP_TITLE', $properties['title']);
            $tpl->setVariable('GROUP_ID', 1);
            $tpl->setVariable('GROUP_COLOR', $properties['color']);
            $tpl->parseCurrentBlock();
        }
        $tpl->setVariable('VIEWPOINT_DATA', json_encode($properties));

        $tpl->setVariable('MARKER_COLOR', $properties['lbl_location_marker']);
        $tpl->setVariable('CIRCLE_COLOR', $properties['lbl_location_circle']);
        $tpl->setVariable('LOCATION_INPUT_HEADLINE', $properties['lbl_marker']);
        $tpl->setVariable('LOCATION_INPUT_STREET', $properties['lbl_street']);
        $tpl->setVariable('LOCATION_INPUT_ZIP', $properties['lbl_zip']);
        $tpl->setVariable('LOCATION_INPUT_CITY', $properties['lbl_location']);
        $tpl->setVariable('LOCATION_SEARCH_SUBMIT_BTN', $properties['lbl_submit_button']);
        $tpl->setVariable('LOCATION_SEARCH_RESET_BTN', $properties['lbl_reset_button']);
        $tpl->setVariable('MAP_ID', $id);
        return $tpl->get();
    }

    /**
     * Renders the given template content on the main UI.
     *
     * @param string $tpl The rendered template content as a string.
     * @return void
     */
    public function render(string $tpl): void
    {
        global $DIC;
        $DIC->ui()->renderer()->render($DIC->ui()->factory()->legacy($tpl));
        $DIC->ui()->mainTemplate()->setContent($tpl);
    }
}
