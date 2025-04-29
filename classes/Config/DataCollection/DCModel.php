<?php

namespace KPG\DML\classes\Config\DataCollection;

use KPG\DML\classes\Util\DataCollection;

class DCModel extends \ActiveRecord
{

    use DataCollection;

    /**
     * @var null|int
     *
     * @con_is_primary true
     * @con_sequence   true
     * @con_has_field  true
     * @con_fieldtype  integer
     * @con_length     8
     */
    protected ?int $id;

    /**
     * @var string
     *
     * @con_has_field   true
     * @con_fieldtype   integer
     * @con_length      8
     * @con_is_notnull  false
     * */
    protected ?int $tbl_id = null;

    /**
     * @var string
     *
     * @con_has_field   true
     * @con_fieldtype   text
     * @con_length      256
     * @con_is_notnull  true
     * */
    protected string $institution = '';

    /**
     * @var string
     *
     * @con_has_field   true
     * @con_fieldtype   text
     * @con_length      256
     * @con_is_notnull  true
     * */
    protected string $street = '';

    /**
     * @var string
     *
     * @con_has_field   true
     * @con_fieldtype   text
     * @con_length      256
     * @con_is_notnull  true
     * */
    protected string $zip = '';

    /**
     * @var string
     *
     * @con_has_field   true
     * @con_fieldtype   text
     * @con_length      256
     * @con_is_notnull  true
     * */
    protected string $location = '';

    /**
     * @var null|string
     *
     * @con_has_field   true
     * @con_fieldtype   text
     * @con_length      256
     * @con_is_notnull  false
     * */
    protected ?string $website = '';


    public const TABLE_NAME = 'kpg_dml_fields';

    /**
     * @return string
     */
    public static function returnDbTableName(): string
    {
        return self::TABLE_NAME;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(?int $id): void
    {
        $this->id = $id;
    }

    public function getTblId(): ?int
    {
        return $this->tbl_id;
    }

    public function setTblId(?int $tbl_id): void
    {
        $this->tbl_id = $tbl_id;
    }

    public function getInstitution(): string
    {
        return $this->institution;
    }

    public function setInstitution(string $institution): void
    {
        $this->institution = $institution;
    }

    public function getStreet(): string
    {
        return $this->street;
    }

    public function setStreet(string $street): void
    {
        $this->street = $street;
    }

    public function getZip(): string
    {
        return $this->zip;
    }

    public function setZip(string $zip): void
    {
        $this->zip = $zip;
    }

    public function getLocation(): string
    {
        return $this->location;
    }

    public function setLocation(?string $location): void
    {
        $this->location = $location;
    }
    public function getWebsite(): ?string
    {
        return $this->website;
    }

    public function setWebsite(?string $website): void
    {
        $this->website = $website;
    }

    public function getLastUpdate(): ?string
    {
        return $this->last_update;
    }

    public function setLastUpdate(?string $last_update): void
    {
        $this->last_update = $last_update;
    }
}