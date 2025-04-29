<?php

namespace KPG\DML\classes\Config\Label;

use KPG\DML\classes\Util\Color;

class LModel extends \ActiveRecord
{

    use Color;

    public const TABLE_NAME = 'kpg_dml_labels';

    /**
     * @return string
     */
    public static function returnDbTableName(): string
    {
        return self::TABLE_NAME;
    }

    /**
     * @var null|int
     *
     * @con_is_primary true
     * @con_has_field  true
     * @con_fieldtype  integer
     * @con_length     4
     */
    protected ?int $rec_id;

    /**
     * @var string
     *
     * @con_has_field   true
     * @con_fieldtype   text
     * @con_length      256
     * @con_is_notnull  true
     * */
    protected ?string $latitude = '51.163361';

    /**
     * @var string
     *
     * @con_has_field   true
     * @con_fieldtype   text
     * @con_length      256
     * @con_is_notnull  true
     * */
    protected ?string $longitude = '10.447683';

    /**
     * @var string
     *
     * @con_has_field   true
     * @con_fieldtype   text
     * @con_length      256
     * @con_is_notnull  true
     * */
    protected ?string $title_search = 'Sucheingabe';

    /**
     * @var string
     *
     * @con_has_field   true
     * @con_fieldtype   text
     * @con_length      256
     * @con_is_notnull  true
     * */
    protected ?string $street = 'Straße';

    /**
     * @var null|string
     *
     * @con_has_field   true
     * @con_fieldtype   text
     * @con_length      256
     * @con_is_notnull  false
     * */
    protected ?string $zip = 'Postleitzahl';

    /**
     * @var null|string
     *
     * @con_has_field   true
     * @con_fieldtype   text
     * @con_length      256
     * @con_is_notnull  false
     * */
    protected ?string $location = 'Ort';

    /**
     * @var null|string
     *
     * @con_has_field   true
     * @con_fieldtype   text
     * @con_length      256
     * @con_is_notnull  false
     * */
    protected ?string $submit_button = 'Suchen';

    /**
     * @var null|string
     *
     * @con_has_field   true
     * @con_fieldtype   text
     * @con_length      256
     * @con_is_notnull  false
     * */
    protected ?string $reset_button = 'Zurücksetzen';

    /**
     * @var null|string
     *
     * @con_has_field   true
     * @con_fieldtype   text
     * @con_length      256
     * @con_is_notnull  false
     * */
    protected ?string $marker = 'Mein Standpunkt';

    /**
     * @var null|string
     *
     * @con_has_field   true
     * @con_fieldtype   datetime
     * @con_length      256
     * @con_is_notnull  false
     * */
    protected ?string $location_marker = '#000';

    /**
     * @var null|string
     *
     * @con_has_field   true
     * @con_fieldtype   text
     * @con_length      256
     * @con_is_notnull  false
     * */
    protected ?string $location_circle = '#95C11F';

    /**
     * @var null|string
     *
     * @con_has_field   true
     * @con_fieldtype   text
     * @con_length      256
     * @con_is_notnull  false
     * */
    protected ?string $email = 'E-Mail';

    /**
     * @var null|string
     *
     * @con_has_field   true
     * @con_fieldtype   text
     * @con_length      256
     * @con_is_notnull  false
     * */
    protected ?string $website = 'Webseite';

    /**
     * @var null|string
     *
     * @con_has_field   true
     * @con_fieldtype   text
     * @con_length      256
     * @con_is_notnull  false
     * */
    protected ?string $result_title = 'Ergebnisliste';

    /**
     * @var null|string
     *
     * @con_has_field   true
     * @con_fieldtype   text
     * @con_length      256
     * @con_is_notnull  false
     * */
    protected ?string $perimeter = 'Umkreis';

    /**
     * Get the latitude value.
     *
     * @return string
     */
    public function getLatitude(): string
    {
        return $this->latitude;
    }

    /**
     * Set the latitude value.
     *
     * @param string $latitude
     * @return void
     */
    public function setLatitude(string $latitude): void
    {
        $this->latitude = $latitude;
    }

    /**
     * Get the longitude value.
     *
     * @return string
     */
    public function getLongitude(): string
    {
        return $this->longitude;
    }

    /**
     * Set the longitude value.
     *
     * @param string $longitude
     * @return void
     */
    public function setLongitude(string $longitude): void
    {
        $this->longitude = $longitude;
    }

    /**
     * Get the street value.
     *
     * @return string
     */
    public function getStreet(): string
    {
        return $this->street;
    }

    /**
     * Set the street value.
     *
     * @param string $street
     * @return void
     */
    public function setStreet(string $street): void
    {
        $this->street = $street;
    }

    /**
     * Get the location value.
     *
     * @return string|null
     */
    public function getLocation(): ?string
    {
        return $this->location;
    }

    /**
     * Set the location value.
     *
     * @param string|null $location
     * @return void
     */
    public function setLocation(?string $location): void
    {
        $this->location = $location;
    }

    /**
     * Get the marker value.
     *
     * @return string|null
     */
    public function getMarker(): ?string
    {
        return $this->marker;
    }

    /**
     * Get the email value.
     *
     * @return string|null
     */
    public function getEmail(): ?string
    {
        return $this->email;
    }

    /**
     * Set the email value.
     *
     * @param string|null $email
     * @return void
     */
    public function setEmail(?string $email): void
    {
        $this->email = $email;
    }
}