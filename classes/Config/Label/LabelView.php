<?php

namespace KPG\DML\classes\Config\Label;

use ILIAS\UI\Component\Input\Container\Form\Standard;
use ILIAS\UI\Component\Input\Field\Section;
use KPG\DML\classes\Interfaces\Constants\ConstantConfig;
use KPG\DML\classes\Util\DataCollection;
use KPG\DML\classes\Util\Language;

class LabelView implements ConstantConfig
{
    use Language;
    use DataCollection;

    private $factory;
    private $dic;
    private $model;

    public function __construct()
    {
        global $DIC;
        $this->dic = $DIC;
        $this->factory = $DIC->ui()->factory();
    }

    /**
     * Creates a section containing multiple input fields for configuring labels.
     *
     * @param array $data The data array containing values to populate the form fields.
     *
     * @return Section The generated input section with the configured fields.
     */
    private function formLabel(array $data): Section
    {
        $text_input_latitude = $this->factory->input()->field()->text(
            self::getLang(self::CONFIG_LABEL_LANG_LATITUDE_TITLE),
            self::getLang(self::CONFIG_LABEL_LANG_LATITUDE_HELP_TEXT)
        )->withValue($data['lbl_latitude'])->withRequired(true);

        $text_input_longitude = $this->factory->input()->field()->text(
            self::getLang(self::CONFIG_LABEL_LANG_LONGITUDE_TITLE),
            self::getLang(self::CONFIG_LABEL_LANG_LONGITUDE_HELP_TEXT)
        )->withValue($data['lbl_longitude'])->withRequired(true);

        $text_input_title_search = $this->factory->input()->field()->text(
            self::getLang(self::CONFIG_LABEL_LANG_TITLE_SEARCH_TITLE),
            self::getLang(self::CONFIG_LABEL_LANG_TITLE_SEARCH_HELP_TEXT)
        )->withValue($data['lbl_title_search'])->withRequired(true);

        $text_input_street = $this->factory->input()->field()->text(
            self::getLang(self::CONFIG_LABEL_LANG_STREET_TITLE),
            self::getLang(self::CONFIG_LABEL_LANG_STREET_HELP_TEXT)
        )->withValue($data['lbl_street'])->withRequired(true);

        $text_input_zip = $this->factory->input()->field()->text(
            self::getLang(self::CONFIG_LABEL_LANG_ZIP_TITLE),
            self::getLang(self::CONFIG_LABEL_LANG_ZIP_HELP_TEXT)
        )->withValue($data['lbl_zip'])->withRequired(true);

        $text_input_location = $this->factory->input()->field()->text(
            self::getLang(self::CONFIG_LABEL_LANG_CITY_TITLE),
            self::getLang(self::CONFIG_LABEL_LANG_CITY_HELP_TEXT)
        )->withValue($data['lbl_location'])->withRequired(true);

        $text_input_submit_button = $this->factory->input()->field()->text(
            self::getLang(self::CONFIG_LABEL_LANG_SUBMIT_BUTTON_TITLE),
            self::getLang(self::CONFIG_LABEL_LANG_SUBMIT_BUTTON_HELP_TEXT)
        )->withValue($data['lbl_submit_button'])->withRequired(true);

        $text_input_reset_button = $this->factory->input()->field()->text(
            self::getLang(self::CONFIG_LABEL_LANG_RESET_BUTTON_TITLE),
            self::getLang(self::CONFIG_LABEL_LANG_RESET_BUTTON_HELP_TEXT)
        )->withValue($data['lbl_reset_button'])->withRequired(true);

        $text_input_marker = $this->factory->input()->field()->text(
            self::getLang(self::CONFIG_LABEL_LANG_MARKER_TITLE),
            self::getLang(self::CONFIG_LABEL_LANG_MARKER_HELP_TEXT)
        )->withValue($data['lbl_marker'])->withRequired(true);

        $color_input_location_marker = $this->factory->input()->field()->colorPicker(
            self::getLang(self::CONFIG_LABEL_LANG_COLOR_LOCATION_MARKER_TITLE),
            self::getLang(self::CONFIG_LABEL_LANG_COLOR_LOCATION_MARKER_HELP_TEXT)
        )->withValue($data['lbl_location_marker'])->withRequired(true);

        $color_input_location_circle = $this->factory->input()->field()->colorPicker(
            self::getLang(self::CONFIG_LABEL_LANG_COLOR_LOCATION_CIRCLE_TITLE),
            self::getLang(self::CONFIG_LABEL_LANG_COLOR_LOCATION_CIRCLE_HELP_TEXT)
        )->withValue($data['lbl_location_circle'])->withRequired(true);

        $text_input_perimeter = $this->factory->input()->field()->text(
            self::getLang(self::CONFIG_LABEL_LANG_PERIMETER_TITLE),
            self::getLang(self::CONFIG_LABEL_LANG_PERIMETER_HELP_TEXT)
        )->withValue($data['lbl_perimeter'])->withRequired(true);

        $text_input_website = $this->factory->input()->field()->text(
            self::getLang(self::CONFIG_LABEL_LANG_WEBSITE_TITLE),
            self::getLang(self::CONFIG_LABEL_LANG_WEBSITE_HELP_TEXT)
        )->withValue($data['lbl_website'])->withRequired(true);

        return $this->factory->input()->field()->section(
            [
                "latitude" => $text_input_latitude,
                'longitude' => $text_input_longitude,
                'title_search' => $text_input_title_search,
                'street' => $text_input_street,
                'zip' => $text_input_zip,
                'location' => $text_input_location,
                'website' => $text_input_website,
                'submit_button' => $text_input_submit_button,
                'reset_button' => $text_input_reset_button,
                'marker' => $text_input_marker,
                'perimeter' => $text_input_perimeter,
                'location_marker' => $color_input_location_marker,
                'location_circle' => $color_input_location_circle,
            ],
            'label'
        );
    }

    /**
     * Initializes a new form for label configuration.
     *
     * @param array $data The data array containing values to populate the form input fields.
     *
     * @return Standard The created form object.
     * @throws \ilCtrlException
     */
    public function initForm(array $data): Standard
    {
        $form_action = $this->dic->ctrl()->getLinkTargetByClass(
            \ilDCLMapLitePluginGUI::class, self::CONFIG_LABELS_CMD_UPDATE
        );
        return $this->factory->input()->container()->form()->standard(
            $form_action, [
                'label' => $this->formLabel($data)
            ]
        );
    }

}