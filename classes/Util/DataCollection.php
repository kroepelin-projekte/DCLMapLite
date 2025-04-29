<?php

namespace KPG\DML\classes\Util;

use ilObject;
use ilObjDataCollection;
use ilDclTable;
use ilDclCache;
use ilException;
use ilDclDatatype;

trait DataCollection
{
    public function getDCLTableByRefid(int $ref_id, $table_field_name): ?int
    {
        foreach ((new ilObjDataCollection($ref_id, true))->getTables() as $dclTable) {
            $tbl_id = $dclTable->getId();
            if (ilDclTable::_hasFieldByTitle($table_field_name, $tbl_id)) {
                return $tbl_id;
            }
        }
        return null;
    }

    public function getDclTable(int $tbl_id): ?ilDclTable
    {
        if (!empty($tbl_id) && ilDclTable::_tableExists($tbl_id)) {
            $tbl = ilDclCache::getTableCache($tbl_id);
            if ($tbl->hasCustomFields()) {
                return $tbl;
            }
        }
        return null;
    }

    /**
     * Retrieves all data collections
     *
     * @return array Returns an array of data collections
     */
    private function fetchDataCollections(): array
    {
        $dcl_collection_all = ilObject::_getObjectsByType('dcl');
        $dc = [];
        $options = [];
        foreach ($dcl_collection_all as $item) {
            foreach (ilObject::_getAllReferences((int) $item['obj_id']) as $reference) {
                if (!ilObject::_isInTrash($reference)) {
                    $obj = new ilObjDataCollection($reference, true);
                    $dc[$obj->getId()] = $obj;
                    foreach ($dc[$obj->getId()]->getTables() as $table) {
                        $options[$table->getId()] = $table->getTitle() . '  @  ' . $table->getCollectionObject(
                            )->getTitle();
                    }
                }
            }
        }
        return $options;
    }

    public function validateDclTableFields(int $tbl_id, array $sections): bool
    {
        $dcl_field_ids = [];
        foreach ($sections as $fields) {
            foreach ($fields as $key => $field) {
                if (!empty($field)) {
                    if (!ilDclTable::_hasFieldByTitle($field, $tbl_id)) {
                        throw new ilException('Field: "' . $field . '" does not exist in table: ' . $tbl_id);
                    } else {
                        $tbl = $this->getDclTable($tbl_id);
                        $dcl_field = $tbl->getFieldByTitle($field);
                        $dcl_field_ids[$key] = [
                            'id' => (int) $tbl->getFieldByTitle($field)->getId(),
                            'origin_title' => $tbl->getFieldByTitle($field)->getTitle(),
                            'field_name' => $key
                        ];
                        $data_type = $dcl_field->getDatatypeId();
                        switch ($key) {
                            case $key === 'zip':
                            case $key === 'phone':
                                if (!in_array(
                                    $dcl_field->getDatatypeId(),
                                    [ilDclDatatype::INPUTFORMAT_NUMBER, ilDclDatatype::INPUTFORMAT_TEXT]
                                )) {
                                    throw new ilException('Field: "' . $key . '" has wrong Datatype format!');
                                }
                                break;
                            case $key === 'website':
                            case $key === 'email':
                                if (!in_array(
                                    $dcl_field->getDatatypeId(),
                                    [ilDclDatatype::INPUTFORMAT_FILE, ilDclDatatype::INPUTFORMAT_TEXT]
                                )) {
                                    throw new ilException('Field: "' . $key . '" has wrong Datatype format!');
                                }
                                break;
                            default:
                                if ($dcl_field->getDatatypeId() !== ilDclDatatype::INPUTFORMAT_TEXT) {
                                    throw new ilException('Field: "' . $key . '" has wrong Datatype format!');
                                }
                                break;
                        }
                    }
                }
            }
        }
        return true;
    }

    public function getRecordsFromDclTable(int $tbl_id, array $field_ids): ?array
    {
        $tbl = $this->getDclTable($tbl_id);
        if (!is_null($tbl)) {
            $records = $tbl->getRecords();
            $result = [];
            $fieldset = $field_ids;
            foreach ($records as $record) {
                $record_field_values = $record->getRecordFieldValues();
                $rec['id'] = $record->getId();
                $rec['tbl_id'] = $tbl->getId();
                foreach ($fieldset as $key => $value) {
                    if (!empty($value)) {
                        if (is_array($record_field_values[$value])) {
                            $rec[$key] = json_encode($record_field_values[$value]);
                        } else {
                            $rec[$key] = $record_field_values[$value];
                        }
                    } else {
                        $rec[$key] = null;
                    }
                }
                $rec['last_update'] = $record->getLastUpdate()->get(1);
                $result[$record->getId()] = $rec;
            }
            return $result;
        }
        return null;
    }

    public function getRecordFieldIDs(array $fields, int $tbl_id): array
    {
        $result = [];
        foreach ($fields as $key => $field_title) {
            if ($field_title != "") {
                $tbl = $this->getDclTable($tbl_id);
                $result[$key] = $tbl->getFieldByTitle($field_title)->getId();
            } else {
                $result[$key] = null;
            }
        }
        return $result;
    }

    public function getSelectionFieldProperties(
        int $tbl_id,
        ?string $selection_field_title = null
    ): array {
        $selection_array = [];
        $tbl = $this->getDclTable($tbl_id);
        if (!is_null($tbl) && !empty($selection_field_title)) {
            $selection_field = $tbl->getFieldByTitle($selection_field_title);
            if (!is_null($selection_field) && $selection_field->hasProperty('text_selection_type')) {
                $selection_type = $selection_field->getProperty('text_selection_type');
                if ($selection_type === 'selection_type_single') {
                    $selection_options = $selection_field->getProperty('text_selection_options');
                    foreach ($selection_options as $selection_option) {
                        $grp_id = $selection_option->getOptId();
                        $selection_array[$grp_id] = [
                            'grp_' . $grp_id . '_field_id' => $selection_option->getFieldId(),
                            'grp_' . $grp_id . '_title' => $selection_option->getValue(),
                            'grp_' . $grp_id . '_opt_id' => $grp_id,
                            'grp_' . $grp_id . '_color' => '#122480',
                        ];
                    }
                }
            };
        } else {
            $selection_array[] = [
                'field_id' => 0,
                'title' => '',
                'opt_id' => 1,
                'color' => '#122480',
            ];
        }
        return $selection_array;
    }

    public function getDCLByTblID(int $tbl_id): array
    {
        $tbl = $this->getDclTable($tbl_id);
        $dcl = $tbl->getCollectionObject();
        return [
            'ref_id' => $dcl->getId(),
            'title' => $dcl->getTitle()
        ];
    }

}