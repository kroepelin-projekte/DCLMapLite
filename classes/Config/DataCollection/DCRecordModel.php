<?php
declare(strict_types=1);

namespace KPG\DML\classes\Config\DataCollection;

use ILIAS\BackgroundTasks\BackgroundTaskServices;
use ILIAS\BackgroundTasks\Implementation\Bucket\BasicBucket;
use ILIAS\BackgroundTasks\Implementation\TaskManager\MockObserver;

class DCRecordModel extends \ActiveRecord
{
    public const TABLE_NAME = 'kpg_dml_record';

    /**
     * @var null|int
     *
     * @con_is_primary true
     * @con_has_field  true
     * @con_fieldtype  integer
     * @con_length     4
     */
    protected ?int $id;

    /**
     * @var int
     *
     * @con_has_field  true
     * @con_fieldtype  integer
     * @con_length     4
     * @con_is_notnull  true
     * */
    protected int $tbl_id;

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
     * @con_length     10
     * @con_is_notnull
     */
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
    protected ?string $website = null;

    /**
     * @var null|string
     *
     * @con_has_field   true
     * @con_fieldtype   text
     * @con_length      8
     * @con_is_notnull  false
     * */
    protected ?string $color = null;
    /**
     * @var null|float
     *
     * @con_has_field   true
     * @con_fieldtype   float
     * @con_length      8
     * @con_is_notnull  false
     * */
    protected ?float $lat = null;

    /**
     * @var null|float
     *
     * @con_has_field   true
     * @con_fieldtype   float
     * @con_length      8
     * @con_is_notnull  false
     * */
    protected ?float $lon = null;

    /**
     * @var null|string
     *
     * @con_has_field   true
     * @con_fieldtype   datetime
     * @con_length      256
     * @con_is_notnull  true
     * */
    protected ?string $last_update;

    /**
     * @return string
     */
    /**
     * Returns the database table name.
     *
     * @return string The name of the database table.
     */
    public static function returnDbTableName(): string
    {
        return self::TABLE_NAME;
    }

    /**
     * Retrieves the ID of the record.
     *
     * @return int|null The ID of the record, or null if not set.
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * Sets the ID of the record.
     *
     * @param int|null $id The ID of the record.
     */
    public function setId(?int $id): void
    {
        $this->id = $id;
    }

    /**
     * Sets the table ID associated with the record.
     *
     * @param int $tbl_id The table ID.
     */
    public function setTblId(int $tbl_id): void
    {
        $this->tbl_id = $tbl_id;
    }

    /**
     * Retrieves the name of the institution.
     *
     * @return string The name of the institution.
     */
    public function getInstitution(): string
    {
        return $this->institution;
    }

    /**
     * Sets the name of the institution.
     *
     * @param string $institution The name of the institution.
     */
    public function setInstitution(string $institution): void
    {
        $this->institution = $institution;
    }

    /**
     * Retrieves the street name.
     *
     * @return string The street name.
     */
    public function getStreet(): string
    {
        return $this->street;
    }

    /**
     * Sets the street name.
     *
     * @param string $street The street name.
     */
    public function setStreet(string $street): void
    {
        $this->street = $street;
    }

    /**
     * Retrieves the ZIP/postal code.
     *
     * @return string The ZIP/postal code.
     */
    public function getZip(): string
    {
        return $this->zip;
    }

    /**
     * Sets the ZIP/postal code.
     *
     * @param string $zip The ZIP/postal code.
     */
    public function setZip(string $zip): void
    {
        $this->zip = $zip;
    }

    /**
     * Retrieves the location or city name.
     *
     * @return string The location or city name.
     */
    public function getLocation(): string
    {
        return $this->location;
    }

    /**
     * Sets the location or city name.
     *
     * @param string|null $location The location or city name.
     */
    public function setLocation(?string $location): void
    {
        $this->location = $location;
    }

    /**
     * Retrieves the name associated with the record.
     *
     * @return string|null The name, or null if not set.
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * Sets the name associated with the record.
     *
     * @param string|null $name The name of the record.
     */
    public function setName(?string $name): void
    {
        $this->name = $name;
    }

    /**
     * Sets the website URL associated with the record.
     *
     * @param string|null $website The website URL.
     */
    public function setWebsite(?string $website): void
    {
        $this->website = $website;
    }

    /**
     * Retrieves the email address associated with the record.
     *
     * @return string|null The email address, or null if not set.
     */
    public function getEmail(): ?string
    {
        return $this->email;
    }

    /**
     * Sets the email address associated with the record.
     *
     * @param string|null $email The email address.
     */
    public function setEmail(?string $email): void
    {
        $this->email = $email;
    }

    /**
     * Retrieves the phone number associated with the record.
     *
     * @return string|null The phone number, or null if not set.
     */
    public function getPhone(): ?string
    {
        return $this->phone;
    }

    /**
     * Sets the phone number associated with the record.
     *
     * @param string|null $phone The phone number.
     */
    public function setPhone(?string $phone): void
    {
        $this->phone = $phone;
    }

    /**
     * Retrieves the color associated with the record.
     *
     * @return string|null The color, or null if not set.
     */
    public function getColor(): ?string
    {
        return $this->color;
    }

    /**
     * Sets the latitude for geolocation.
     *
     * @param float|null $lat The latitude to set.
     */
    public function setLat(?float $lat): void
    {
        $this->lat = $lat;
    }

    /**
     * Sets the longitude for geolocation.
     *
     * @param float|null $lon The longitude to set.
     */
    public function setLon(?float $lon): void
    {
        $this->lon = $lon;
    }

    /**
     * Retrieves the last update timestamp.
     *
     * @return string|null The last update timestamp, or null if not set.
     */
    public function getLastUpdate(): ?string
    {
        return $this->last_update;
    }

    /**
     * Sets the last update timestamp.
     *
     * @param string|null $last_update The last update timestamp.
     */
    public function setLastUpdate(?string $last_update): void
    {
        $this->last_update = $last_update;
    }

    /**
     * Adds a record to the database.
     *
     * This method creates a new instance of the current class, populates it
     * with the provided record details, and saves it to the database.
     *
     * @param array $record The record data to be added. It must contain the following keys:
     *                      - 'id' (int|null): The ID of the record.
     *                      - 'tbl_id' (int): The table ID the record belongs to.
     *                      - 'institution' (string): The institution name.
     *                      - 'street' (string): The street information.
     *                      - 'zip' (string): The ZIP/postal code.
     *                      - 'location' (string): The location or city name.
     *                      - 'name' (string|null): The name associated with the record.
     *                      - 'website' (string|null): The website URL.
     *                      - 'email' (string|null): The email address.
     *                      - 'phone' (string|null): The phone number.
     *                      - 'lat' (float|null): The latitude for geolocation.
     *                      - 'lon' (float|null): The longitude for geolocation.
     *                      - 'last_update' (string|null): The last update timestamp.
     *
     * @return void
     */
    private function addRecord($record)
    {
        $m = new self();
        $m->setId($record['id']);
        $m->setTblId($record['tbl_id']);
        $m->setInstitution($record['institution']);
        $m->setStreet($record['street']);
        $m->setZip((string)$record['zip']);
        $m->setLocation($record['location']);
        $m->setWebsite($record['website']);
        $m->setLat((float)$record['lat']);
        $m->setLon((float)$record['lon']);
        $m->setLastUpdate($record['last_update']);
        $m->store();
    }

    /**
     * Deletes a record from the database.
     *
     * This method finds a record by its ID and deletes it.
     *
     * @param array $record The record array containing at least the 'id' key.
     *
     * @return void
     */
    private function deleteRecord($record)
    {
        self::find($record['id'])->delete();
    }

    /**
     * Saves an array of records to the database, adding, updating, or deleting as required.
     *
     * This method compares the provided records with the existing records in the database for the specified table ID.
     * Depending on the comparison, it determines whether to add, update, or delete records.
     *
     * @param int $tbl_id The table ID to which the records belong.
     * @param array $records An array of records to be saved, with the following structure:
     *                       - 'id' (int|null): The ID of the record.
     *                       - 'tbl_id' (int): The table ID the record belongs to.
     *                       - 'institution' (string): The institution name.
     *                       - 'street' (string): The street information.
     *                       - 'zip' (string): The ZIP/postal code.
     *                       - 'location' (string): The location or city name.
     *                       - 'name' (string|null): The name associated with the record.
     *                       - 'website' (string|null): The website URL.
     *                       - 'email' (string|null): The email address.
     *                       - 'phone' (string|null): The phone number.
     *                       - 'lat' (float|null): The latitude for geolocation.
     *                       - 'lon' (float|null): The longitude for geolocation.
     *                       - 'last_update' (string): The last update timestamp.
     *
     * @return void
     * @throws \Exception
     */
    public function saveFromArray(int $tbl_id, array $records): void
    {
        $target = self::where(['tbl_id' => $tbl_id])->getArray();
        $record_cache = $this->compareRecords($records, $target);
        foreach ($record_cache as $key => $items) {
            switch ($key) {
                case 'add':
                case 'update':
                    foreach ($items as $record) {
                        $coords = $this->getCoordsViaCurl($tbl_id, $record);
                        if (!$coords) {
                            continue;
                        }
                        $record['lat'] = $coords['lat'];
                        $record['lon'] = $coords['lon'];
                        $this->addRecord($record);
                    }
                    break;
                case 'delete':
                    foreach ($items as $record) {
                        $this->deleteRecord($record);
                    }
                    break;
            }
        }
    }

    /**
     * Compares two arrays of records and determines the differences.
     *
     * This method compares a source array of records with a target array
     * and categorizes the differences into:
     * - 'add': Records present in the source but not in the target.
     * - 'update': Records where the last update timestamp in the source
     *   is newer than in the target.
     * - 'delete': Records present in the target but not in the source.
     *
     * @param array $source_array The source array of records to compare.
     *                             Keys represent record IDs, and values are record data.
     * @param array $target_array The target array of records to compare against.
     *                             Keys represent record IDs, and values are record data.
     *
     * @return array An associative array containing three keys:
     *               - 'add': Records to add.
     *               - 'update': Records to update.
     *               - 'delete': Records to delete.
     */
    public function compareRecords(array $source_array, array $target_array): array
    {
        $result = [];
        foreach ($source_array as $id => $record) {
            if (!array_key_exists($id, $target_array)) {
                $result['add'][$id] = $record;
            } elseif ($record['last_update'] > $target_array[$id]['last_update']) {
                $result['update'][$id] = $record;
            }
        }

        foreach ($target_array as $id => $record) {
            if (!array_key_exists($id, $source_array)) {
                $result['delete'][$id] = $record;
            }
        }

        return $result;
    }

    /**
     * Retrieves geographical coordinates (latitude and longitude) using the Nominatim OpenStreetMap API.
     *
     * This method sends a CURL GET request to the Nominatim API, using street, postal code, and city data
     * to retrieve geocoordinates. If coordinates already exist for the given table ID, the method slightly adjusts
     * the values to ensure uniqueness.
     *
     * @param int $tbl_id The table ID to check for existing coordinates.
     * @param array $data An associative array containing the address information:
     *                    - 'street' (string): The street name.
     *                    - 'zip' (string): The postal code.
     *                    - 'location' (string): The city name.
     *
     * @return array|null An array containing:
     *                    - 'lat' (float): Latitude.
     *                    - 'lon' (float): Longitude.
     *                    Returns null if no results are found or an error occurs.
     * @throws \Exception
     */
    private function getCoordsViaCurl(int $tbl_id, array $data): ?array
    {
        $search_url =
            'https://nominatim.openstreetmap.org/search?format=json&countrycodes=de&limit=1&addressdetails=1'
            . '&street=' . urlencode($data['street'])
            . '&postalcode=' . urlencode((string)$data['zip'])
            . '&city=' . urlencode($data['location']);

        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => $search_url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
            CURLOPT_USERAGENT => 'DCLMapLite for ILIAS',
        ));
        $result = json_decode(curl_exec($curl), true);
        if (!$result) {
            return null;
        }
        $coords = [
            'lat' => $result[0]['lat'],
            'lon' => $result[0]['lon']
        ];
        $check = !empty(self::where(['tbl_id' => $tbl_id])
            ->where(['lat' => $coords['lat']])
            ->where(['lon' => $coords['lon']])
            ->get());
        if ($check) {
            $coords['lat'] += (rand(0, 10000) * 0.000001);
            $coords['lon'] += (rand(0, 10000) * 0.000001);
        }
        return $coords;
    }
}
