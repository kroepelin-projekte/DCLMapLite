<?php

namespace KPG\DML\classes\Output;

class OutputController
{
    public $dic;

    public function __construct()
    {
        global $DIC;
        $this->dic = $DIC;
    }

    /**
     * Displays a map with custom properties.
     *
     * Loads necessary metadata, retrieves the HTML template, and sets values
     * into the template based on the given properties to render the map.
     *
     * @param array $properties The properties used to configure the map. This includes metadata such as titles, colors, location data, etc.
     *
     * @return string The rendered HTML content of the map.
     * @throws \ilTemplateException
     */
    public function showMap(array $properties): string
    {
        $view = new OutputView();
        $view->loadMeta();
        $tpl = $view->loadTemplate();
        return $view->setValues($tpl, $properties);
    }
}
