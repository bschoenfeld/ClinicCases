<?php
/**
 * Account
 *
 * PHP version 5
 *
 * @category Class
 * @package  Swagger\Client
 * @author   Swagger Codegen team
 * @link     https://github.com/swagger-api/swagger-codegen
 */

/**
 * PracticePanther KISS Api
 *
 * No description provided (generated by Swagger Codegen https://github.com/swagger-api/swagger-codegen)
 *
 * OpenAPI spec version: v2
 * 
 * Generated by: https://github.com/swagger-api/swagger-codegen.git
 */

/**
 * NOTE: This class is auto generated by the swagger code generator program.
 * https://github.com/swagger-api/swagger-codegen
 * Do not edit the class manually.
 */

namespace Swagger\Client\Model;

use \ArrayAccess;
use \Swagger\Client\ObjectSerializer;

/**
 * Account Class Doc Comment
 *
 * @category Class
 * @description An account is an entity that can represent either one contact, or multiple contacts. An account must contain at least one primary contact
 * @package     Swagger\Client
 * @author      Swagger Codegen team
 * @link        https://github.com/swagger-api/swagger-codegen
 */
class Account implements ModelInterface, ArrayAccess
{
    const DISCRIMINATOR = null;

    /**
      * The original name of the model.
      *
      * @var string
      */
    protected static $swaggerModelName = 'Account';

    /**
      * Array of property to type mappings. Used for (de)serialization
      *
      * @var string[]
      */
    protected static $swaggerTypes = [
        'id' => 'string',
        'display_name' => 'string',
        'number' => 'int',
        'company_name' => 'string',
        'address_street_1' => 'string',
        'address_street_2' => 'string',
        'address_city' => 'string',
        'address_state' => 'string',
        'address_country' => 'string',
        'address_zip_code' => 'string',
        'tags' => 'string[]',
        'company_custom_field_values' => '\Swagger\Client\Model\CustomFieldValue[]',
        'assigned_to_users' => '\Swagger\Client\Model\UserReference[]',
        'created_at' => '\DateTime',
        'updated_at' => '\DateTime',
        'notes' => 'string',
        'primary_contact' => '\Swagger\Client\Model\Contact',
        'other_contacts' => '\Swagger\Client\Model\Contact[]'
    ];

    /**
      * Array of property to format mappings. Used for (de)serialization
      *
      * @var string[]
      */
    protected static $swaggerFormats = [
        'id' => 'uuid',
        'display_name' => null,
        'number' => 'int32',
        'company_name' => null,
        'address_street_1' => null,
        'address_street_2' => null,
        'address_city' => null,
        'address_state' => null,
        'address_country' => null,
        'address_zip_code' => null,
        'tags' => null,
        'company_custom_field_values' => null,
        'assigned_to_users' => null,
        'created_at' => 'date-time',
        'updated_at' => 'date-time',
        'notes' => null,
        'primary_contact' => null,
        'other_contacts' => null
    ];

    /**
     * Array of property to type mappings. Used for (de)serialization
     *
     * @return array
     */
    public static function swaggerTypes()
    {
        return self::$swaggerTypes;
    }

    /**
     * Array of property to format mappings. Used for (de)serialization
     *
     * @return array
     */
    public static function swaggerFormats()
    {
        return self::$swaggerFormats;
    }

    /**
     * Array of attributes where the key is the local name,
     * and the value is the original name
     *
     * @var string[]
     */
    protected static $attributeMap = [
        'id' => 'id',
        'display_name' => 'display_name',
        'number' => 'number',
        'company_name' => 'company_name',
        'address_street_1' => 'address_street_1',
        'address_street_2' => 'address_street_2',
        'address_city' => 'address_city',
        'address_state' => 'address_state',
        'address_country' => 'address_country',
        'address_zip_code' => 'address_zip_code',
        'tags' => 'tags',
        'company_custom_field_values' => 'company_custom_field_values',
        'assigned_to_users' => 'assigned_to_users',
        'created_at' => 'created_at',
        'updated_at' => 'updated_at',
        'notes' => 'notes',
        'primary_contact' => 'primary_contact',
        'other_contacts' => 'other_contacts'
    ];

    /**
     * Array of attributes to setter functions (for deserialization of responses)
     *
     * @var string[]
     */
    protected static $setters = [
        'id' => 'setId',
        'display_name' => 'setDisplayName',
        'number' => 'setNumber',
        'company_name' => 'setCompanyName',
        'address_street_1' => 'setAddressStreet1',
        'address_street_2' => 'setAddressStreet2',
        'address_city' => 'setAddressCity',
        'address_state' => 'setAddressState',
        'address_country' => 'setAddressCountry',
        'address_zip_code' => 'setAddressZipCode',
        'tags' => 'setTags',
        'company_custom_field_values' => 'setCompanyCustomFieldValues',
        'assigned_to_users' => 'setAssignedToUsers',
        'created_at' => 'setCreatedAt',
        'updated_at' => 'setUpdatedAt',
        'notes' => 'setNotes',
        'primary_contact' => 'setPrimaryContact',
        'other_contacts' => 'setOtherContacts'
    ];

    /**
     * Array of attributes to getter functions (for serialization of requests)
     *
     * @var string[]
     */
    protected static $getters = [
        'id' => 'getId',
        'display_name' => 'getDisplayName',
        'number' => 'getNumber',
        'company_name' => 'getCompanyName',
        'address_street_1' => 'getAddressStreet1',
        'address_street_2' => 'getAddressStreet2',
        'address_city' => 'getAddressCity',
        'address_state' => 'getAddressState',
        'address_country' => 'getAddressCountry',
        'address_zip_code' => 'getAddressZipCode',
        'tags' => 'getTags',
        'company_custom_field_values' => 'getCompanyCustomFieldValues',
        'assigned_to_users' => 'getAssignedToUsers',
        'created_at' => 'getCreatedAt',
        'updated_at' => 'getUpdatedAt',
        'notes' => 'getNotes',
        'primary_contact' => 'getPrimaryContact',
        'other_contacts' => 'getOtherContacts'
    ];

    /**
     * Array of attributes where the key is the local name,
     * and the value is the original name
     *
     * @return array
     */
    public static function attributeMap()
    {
        return self::$attributeMap;
    }

    /**
     * Array of attributes to setter functions (for deserialization of responses)
     *
     * @return array
     */
    public static function setters()
    {
        return self::$setters;
    }

    /**
     * Array of attributes to getter functions (for serialization of requests)
     *
     * @return array
     */
    public static function getters()
    {
        return self::$getters;
    }

    /**
     * The original name of the model.
     *
     * @return string
     */
    public function getModelName()
    {
        return self::$swaggerModelName;
    }

    

    

    /**
     * Associative array for storing property values
     *
     * @var mixed[]
     */
    protected $container = [];

    /**
     * Constructor
     *
     * @param mixed[] $data Associated array of property values
     *                      initializing the model
     */
    public function __construct(array $data = null)
    {
        $this->container['id'] = isset($data['id']) ? $data['id'] : null;
        $this->container['display_name'] = isset($data['display_name']) ? $data['display_name'] : null;
        $this->container['number'] = isset($data['number']) ? $data['number'] : null;
        $this->container['company_name'] = isset($data['company_name']) ? $data['company_name'] : null;
        $this->container['address_street_1'] = isset($data['address_street_1']) ? $data['address_street_1'] : null;
        $this->container['address_street_2'] = isset($data['address_street_2']) ? $data['address_street_2'] : null;
        $this->container['address_city'] = isset($data['address_city']) ? $data['address_city'] : null;
        $this->container['address_state'] = isset($data['address_state']) ? $data['address_state'] : null;
        $this->container['address_country'] = isset($data['address_country']) ? $data['address_country'] : null;
        $this->container['address_zip_code'] = isset($data['address_zip_code']) ? $data['address_zip_code'] : null;
        $this->container['tags'] = isset($data['tags']) ? $data['tags'] : null;
        $this->container['company_custom_field_values'] = isset($data['company_custom_field_values']) ? $data['company_custom_field_values'] : null;
        $this->container['assigned_to_users'] = isset($data['assigned_to_users']) ? $data['assigned_to_users'] : null;
        $this->container['created_at'] = isset($data['created_at']) ? $data['created_at'] : null;
        $this->container['updated_at'] = isset($data['updated_at']) ? $data['updated_at'] : null;
        $this->container['notes'] = isset($data['notes']) ? $data['notes'] : null;
        $this->container['primary_contact'] = isset($data['primary_contact']) ? $data['primary_contact'] : null;
        $this->container['other_contacts'] = isset($data['other_contacts']) ? $data['other_contacts'] : null;
    }

    /**
     * Show all the invalid properties with reasons.
     *
     * @return array invalid properties with reasons
     */
    public function listInvalidProperties()
    {
        $invalidProperties = [];

        return $invalidProperties;
    }

    /**
     * Validate all the properties in the model
     * return true if all passed
     *
     * @return bool True if all properties are valid
     */
    public function valid()
    {

        return true;
    }


    /**
     * Gets id
     *
     * @return string
     */
    public function getId()
    {
        return $this->container['id'];
    }

    /**
     * Sets id
     *
     * @param string $id id
     *
     * @return $this
     */
    public function setId($id)
    {
        $this->container['id'] = $id;

        return $this;
    }

    /**
     * Gets display_name
     *
     * @return string
     */
    public function getDisplayName()
    {
        return $this->container['display_name'];
    }

    /**
     * Sets display_name
     *
     * @param string $display_name This is the display name for the contact. It is set automatically based on the user settings in the UI This is the display name for the contact. It is set automatically based on the user settings in the UI
     *
     * @return $this
     */
    public function setDisplayName($display_name)
    {
        $this->container['display_name'] = $display_name;

        return $this;
    }

    /**
     * Gets number
     *
     * @return int
     */
    public function getNumber()
    {
        return $this->container['number'];
    }

    /**
     * Sets number
     *
     * @param int $number This is the account number. If Auto-Numbering is turned on you can leave this field blank and PracticePanther will automatically assign the next available number
     *
     * @return $this
     */
    public function setNumber($number)
    {
        $this->container['number'] = $number;

        return $this;
    }

    /**
     * Gets company_name
     *
     * @return string
     */
    public function getCompanyName()
    {
        return $this->container['company_name'];
    }

    /**
     * Sets company_name
     *
     * @param string $company_name Should be set only if this account represents a company
     *
     * @return $this
     */
    public function setCompanyName($company_name)
    {
        $this->container['company_name'] = $company_name;

        return $this;
    }

    /**
     * Gets address_street_1
     *
     * @return string
     */
    public function getAddressStreet1()
    {
        return $this->container['address_street_1'];
    }

    /**
     * Sets address_street_1
     *
     * @param string $address_street_1 address_street_1
     *
     * @return $this
     */
    public function setAddressStreet1($address_street_1)
    {
        $this->container['address_street_1'] = $address_street_1;

        return $this;
    }

    /**
     * Gets address_street_2
     *
     * @return string
     */
    public function getAddressStreet2()
    {
        return $this->container['address_street_2'];
    }

    /**
     * Sets address_street_2
     *
     * @param string $address_street_2 address_street_2
     *
     * @return $this
     */
    public function setAddressStreet2($address_street_2)
    {
        $this->container['address_street_2'] = $address_street_2;

        return $this;
    }

    /**
     * Gets address_city
     *
     * @return string
     */
    public function getAddressCity()
    {
        return $this->container['address_city'];
    }

    /**
     * Sets address_city
     *
     * @param string $address_city address_city
     *
     * @return $this
     */
    public function setAddressCity($address_city)
    {
        $this->container['address_city'] = $address_city;

        return $this;
    }

    /**
     * Gets address_state
     *
     * @return string
     */
    public function getAddressState()
    {
        return $this->container['address_state'];
    }

    /**
     * Sets address_state
     *
     * @param string $address_state address_state
     *
     * @return $this
     */
    public function setAddressState($address_state)
    {
        $this->container['address_state'] = $address_state;

        return $this;
    }

    /**
     * Gets address_country
     *
     * @return string
     */
    public function getAddressCountry()
    {
        return $this->container['address_country'];
    }

    /**
     * Sets address_country
     *
     * @param string $address_country address_country
     *
     * @return $this
     */
    public function setAddressCountry($address_country)
    {
        $this->container['address_country'] = $address_country;

        return $this;
    }

    /**
     * Gets address_zip_code
     *
     * @return string
     */
    public function getAddressZipCode()
    {
        return $this->container['address_zip_code'];
    }

    /**
     * Sets address_zip_code
     *
     * @param string $address_zip_code address_zip_code
     *
     * @return $this
     */
    public function setAddressZipCode($address_zip_code)
    {
        $this->container['address_zip_code'] = $address_zip_code;

        return $this;
    }

    /**
     * Gets tags
     *
     * @return string[]
     */
    public function getTags()
    {
        return $this->container['tags'];
    }

    /**
     * Sets tags
     *
     * @param string[] $tags tags
     *
     * @return $this
     */
    public function setTags($tags)
    {
        $this->container['tags'] = $tags;

        return $this;
    }

    /**
     * Gets company_custom_field_values
     *
     * @return \Swagger\Client\Model\CustomFieldValue[]
     */
    public function getCompanyCustomFieldValues()
    {
        return $this->container['company_custom_field_values'];
    }

    /**
     * Sets company_custom_field_values
     *
     * @param \Swagger\Client\Model\CustomFieldValue[] $company_custom_field_values This is a list of custom field values related to this company. Can only be used if company_name is set for this account
     *
     * @return $this
     */
    public function setCompanyCustomFieldValues($company_custom_field_values)
    {
        $this->container['company_custom_field_values'] = $company_custom_field_values;

        return $this;
    }

    /**
     * Gets assigned_to_users
     *
     * @return \Swagger\Client\Model\UserReference[]
     */
    public function getAssignedToUsers()
    {
        return $this->container['assigned_to_users'];
    }

    /**
     * Sets assigned_to_users
     *
     * @param \Swagger\Client\Model\UserReference[] $assigned_to_users At least one user must be assigned to this matter. You can get the current user using get at /users/me
     *
     * @return $this
     */
    public function setAssignedToUsers($assigned_to_users)
    {
        $this->container['assigned_to_users'] = $assigned_to_users;

        return $this;
    }

    /**
     * Gets created_at
     *
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->container['created_at'];
    }

    /**
     * Sets created_at
     *
     * @param \DateTime $created_at updated_at can be used to sync contacts with PracticePanther. updated_at can be used to sync contacts with PracticePanther.
     *
     * @return $this
     */
    public function setCreatedAt($created_at)
    {
        $this->container['created_at'] = $created_at;

        return $this;
    }

    /**
     * Gets updated_at
     *
     * @return \DateTime
     */
    public function getUpdatedAt()
    {
        return $this->container['updated_at'];
    }

    /**
     * Sets updated_at
     *
     * @param \DateTime $updated_at updated_at can be used to sync contacts with PracticePanther. updated_at can be used to sync contacts with PracticePanther.
     *
     * @return $this
     */
    public function setUpdatedAt($updated_at)
    {
        $this->container['updated_at'] = $updated_at;

        return $this;
    }

    /**
     * Gets notes
     *
     * @return string
     */
    public function getNotes()
    {
        return $this->container['notes'];
    }

    /**
     * Sets notes
     *
     * @param string $notes These are company notes and can only be used if company_name is set for this account
     *
     * @return $this
     */
    public function setNotes($notes)
    {
        $this->container['notes'] = $notes;

        return $this;
    }

    /**
     * Gets primary_contact
     *
     * @return \Swagger\Client\Model\Contact
     */
    public function getPrimaryContact()
    {
        return $this->container['primary_contact'];
    }

    /**
     * Sets primary_contact
     *
     * @param \Swagger\Client\Model\Contact $primary_contact This is the primary contact for this account
     *
     * @return $this
     */
    public function setPrimaryContact($primary_contact)
    {
        $this->container['primary_contact'] = $primary_contact;

        return $this;
    }

    /**
     * Gets other_contacts
     *
     * @return \Swagger\Client\Model\Contact[]
     */
    public function getOtherContacts()
    {
        return $this->container['other_contacts'];
    }

    /**
     * Sets other_contacts
     *
     * @param \Swagger\Client\Model\Contact[] $other_contacts If this account is a company, this will include any additional contacts other than the primary contact, if any
     *
     * @return $this
     */
    public function setOtherContacts($other_contacts)
    {
        $this->container['other_contacts'] = $other_contacts;

        return $this;
    }
    /**
     * Returns true if offset exists. False otherwise.
     *
     * @param  integer $offset Offset
     *
     * @return boolean
     */
    public function offsetExists($offset)
    {
        return isset($this->container[$offset]);
    }

    /**
     * Gets offset.
     *
     * @param  integer $offset Offset
     *
     * @return mixed
     */
    public function offsetGet($offset)
    {
        return isset($this->container[$offset]) ? $this->container[$offset] : null;
    }

    /**
     * Sets value based on offset.
     *
     * @param  integer $offset Offset
     * @param  mixed   $value  Value to be set
     *
     * @return void
     */
    public function offsetSet($offset, $value)
    {
        if (is_null($offset)) {
            $this->container[] = $value;
        } else {
            $this->container[$offset] = $value;
        }
    }

    /**
     * Unsets offset.
     *
     * @param  integer $offset Offset
     *
     * @return void
     */
    public function offsetUnset($offset)
    {
        unset($this->container[$offset]);
    }

    /**
     * Gets the string presentation of the object
     *
     * @return string
     */
    public function __toString()
    {
        if (defined('JSON_PRETTY_PRINT')) { // use JSON pretty print
            return json_encode(
                ObjectSerializer::sanitizeForSerialization($this),
                JSON_PRETTY_PRINT
            );
        }

        return json_encode(ObjectSerializer::sanitizeForSerialization($this));
    }
}
