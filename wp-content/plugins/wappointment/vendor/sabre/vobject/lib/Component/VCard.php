<?php

namespace WappoVendor\Sabre\VObject\Component;

use WappoVendor\Sabre\VObject;
use WappoVendor\Sabre\Xml;
/**
 * The VCard component.
 *
 * This component represents the BEGIN:VCARD and END:VCARD found in every
 * vcard.
 *
 * @copyright Copyright (C) fruux GmbH (https://fruux.com/)
 * @author Evert Pot (http://evertpot.com/)
 * @license http://sabre.io/license/ Modified BSD License
 */
class VCard extends \WappoVendor\Sabre\VObject\Document
{
    /**
     * The default name for this component.
     *
     * This should be 'VCALENDAR' or 'VCARD'.
     *
     * @var string
     */
    public static $defaultName = 'VCARD';
    /**
     * Caching the version number.
     *
     * @var int
     */
    private $version = null;
    /**
     * This is a list of components, and which classes they should map to.
     *
     * @var array
     */
    public static $componentMap = ['VCARD' => 'WappoVendor\\Sabre\\VObject\\Component\\VCard'];
    /**
     * List of value-types, and which classes they map to.
     *
     * @var array
     */
    public static $valueMap = [
        'BINARY' => 'WappoVendor\\Sabre\\VObject\\Property\\Binary',
        'BOOLEAN' => 'WappoVendor\\Sabre\\VObject\\Property\\Boolean',
        'CONTENT-ID' => 'WappoVendor\\Sabre\\VObject\\Property\\FlatText',
        // vCard 2.1 only
        'DATE' => 'WappoVendor\\Sabre\\VObject\\Property\\VCard\\Date',
        'DATE-TIME' => 'WappoVendor\\Sabre\\VObject\\Property\\VCard\\DateTime',
        'DATE-AND-OR-TIME' => 'WappoVendor\\Sabre\\VObject\\Property\\VCard\\DateAndOrTime',
        // vCard only
        'FLOAT' => 'WappoVendor\\Sabre\\VObject\\Property\\FloatValue',
        'INTEGER' => 'WappoVendor\\Sabre\\VObject\\Property\\IntegerValue',
        'LANGUAGE-TAG' => 'WappoVendor\\Sabre\\VObject\\Property\\VCard\\LanguageTag',
        'PHONE-NUMBER' => 'WappoVendor\\Sabre\\VObject\\Property\\VCard\\PhoneNumber',
        // vCard 3.0 only
        'TIMESTAMP' => 'WappoVendor\\Sabre\\VObject\\Property\\VCard\\TimeStamp',
        'TEXT' => 'WappoVendor\\Sabre\\VObject\\Property\\Text',
        'TIME' => 'WappoVendor\\Sabre\\VObject\\Property\\Time',
        'UNKNOWN' => 'WappoVendor\\Sabre\\VObject\\Property\\Unknown',
        // jCard / jCal-only.
        'URI' => 'WappoVendor\\Sabre\\VObject\\Property\\Uri',
        'URL' => 'WappoVendor\\Sabre\\VObject\\Property\\Uri',
        // vCard 2.1 only
        'UTC-OFFSET' => 'WappoVendor\\Sabre\\VObject\\Property\\UtcOffset',
    ];
    /**
     * List of properties, and which classes they map to.
     *
     * @var array
     */
    public static $propertyMap = [
        // vCard 2.1 properties and up
        'N' => 'WappoVendor\\Sabre\\VObject\\Property\\Text',
        'FN' => 'WappoVendor\\Sabre\\VObject\\Property\\FlatText',
        'PHOTO' => 'WappoVendor\\Sabre\\VObject\\Property\\Binary',
        'BDAY' => 'WappoVendor\\Sabre\\VObject\\Property\\VCard\\DateAndOrTime',
        'ADR' => 'WappoVendor\\Sabre\\VObject\\Property\\Text',
        'LABEL' => 'WappoVendor\\Sabre\\VObject\\Property\\FlatText',
        // Removed in vCard 4.0
        'TEL' => 'WappoVendor\\Sabre\\VObject\\Property\\FlatText',
        'EMAIL' => 'WappoVendor\\Sabre\\VObject\\Property\\FlatText',
        'MAILER' => 'WappoVendor\\Sabre\\VObject\\Property\\FlatText',
        // Removed in vCard 4.0
        'GEO' => 'WappoVendor\\Sabre\\VObject\\Property\\FlatText',
        'TITLE' => 'WappoVendor\\Sabre\\VObject\\Property\\FlatText',
        'ROLE' => 'WappoVendor\\Sabre\\VObject\\Property\\FlatText',
        'LOGO' => 'WappoVendor\\Sabre\\VObject\\Property\\Binary',
        // 'AGENT'   => 'Sabre\\VObject\\Property\\',      // Todo: is an embedded vCard. Probably rare, so
        // not supported at the moment
        'ORG' => 'WappoVendor\\Sabre\\VObject\\Property\\Text',
        'NOTE' => 'WappoVendor\\Sabre\\VObject\\Property\\FlatText',
        'REV' => 'WappoVendor\\Sabre\\VObject\\Property\\VCard\\TimeStamp',
        'SOUND' => 'WappoVendor\\Sabre\\VObject\\Property\\FlatText',
        'URL' => 'WappoVendor\\Sabre\\VObject\\Property\\Uri',
        'UID' => 'WappoVendor\\Sabre\\VObject\\Property\\FlatText',
        'VERSION' => 'WappoVendor\\Sabre\\VObject\\Property\\FlatText',
        'KEY' => 'WappoVendor\\Sabre\\VObject\\Property\\FlatText',
        'TZ' => 'WappoVendor\\Sabre\\VObject\\Property\\Text',
        // vCard 3.0 properties
        'CATEGORIES' => 'WappoVendor\\Sabre\\VObject\\Property\\Text',
        'SORT-STRING' => 'WappoVendor\\Sabre\\VObject\\Property\\FlatText',
        'PRODID' => 'WappoVendor\\Sabre\\VObject\\Property\\FlatText',
        'NICKNAME' => 'WappoVendor\\Sabre\\VObject\\Property\\Text',
        'CLASS' => 'WappoVendor\\Sabre\\VObject\\Property\\FlatText',
        // Removed in vCard 4.0
        // rfc2739 properties
        'FBURL' => 'WappoVendor\\Sabre\\VObject\\Property\\Uri',
        'CAPURI' => 'WappoVendor\\Sabre\\VObject\\Property\\Uri',
        'CALURI' => 'WappoVendor\\Sabre\\VObject\\Property\\Uri',
        'CALADRURI' => 'WappoVendor\\Sabre\\VObject\\Property\\Uri',
        // rfc4770 properties
        'IMPP' => 'WappoVendor\\Sabre\\VObject\\Property\\Uri',
        // vCard 4.0 properties
        'SOURCE' => 'WappoVendor\\Sabre\\VObject\\Property\\Uri',
        'XML' => 'WappoVendor\\Sabre\\VObject\\Property\\FlatText',
        'ANNIVERSARY' => 'WappoVendor\\Sabre\\VObject\\Property\\VCard\\DateAndOrTime',
        'CLIENTPIDMAP' => 'WappoVendor\\Sabre\\VObject\\Property\\Text',
        'LANG' => 'WappoVendor\\Sabre\\VObject\\Property\\VCard\\LanguageTag',
        'GENDER' => 'WappoVendor\\Sabre\\VObject\\Property\\Text',
        'KIND' => 'WappoVendor\\Sabre\\VObject\\Property\\FlatText',
        'MEMBER' => 'WappoVendor\\Sabre\\VObject\\Property\\Uri',
        'RELATED' => 'WappoVendor\\Sabre\\VObject\\Property\\Uri',
        // rfc6474 properties
        'BIRTHPLACE' => 'WappoVendor\\Sabre\\VObject\\Property\\FlatText',
        'DEATHPLACE' => 'WappoVendor\\Sabre\\VObject\\Property\\FlatText',
        'DEATHDATE' => 'WappoVendor\\Sabre\\VObject\\Property\\VCard\\DateAndOrTime',
        // rfc6715 properties
        'EXPERTISE' => 'WappoVendor\\Sabre\\VObject\\Property\\FlatText',
        'HOBBY' => 'WappoVendor\\Sabre\\VObject\\Property\\FlatText',
        'INTEREST' => 'WappoVendor\\Sabre\\VObject\\Property\\FlatText',
        'ORG-DIRECTORY' => 'WappoVendor\\Sabre\\VObject\\Property\\FlatText',
    ];
    /**
     * Returns the current document type.
     *
     * @return int
     */
    public function getDocumentType()
    {
        if (!$this->version) {
            $version = (string) $this->VERSION;
            switch ($version) {
                case '2.1':
                    $this->version = self::VCARD21;
                    break;
                case '3.0':
                    $this->version = self::VCARD30;
                    break;
                case '4.0':
                    $this->version = self::VCARD40;
                    break;
                default:
                    // We don't want to cache the version if it's unknown,
                    // because we might get a version property in a bit.
                    return self::UNKNOWN;
            }
        }
        return $this->version;
    }
    /**
     * Converts the document to a different vcard version.
     *
     * Use one of the VCARD constants for the target. This method will return
     * a copy of the vcard in the new version.
     *
     * At the moment the only supported conversion is from 3.0 to 4.0.
     *
     * If input and output version are identical, a clone is returned.
     *
     * @param int $target
     *
     * @return VCard
     */
    public function convert($target)
    {
        $converter = new \WappoVendor\Sabre\VObject\VCardConverter();
        return $converter->convert($this, $target);
    }
    /**
     * VCards with version 2.1, 3.0 and 4.0 are found.
     *
     * If the VCARD doesn't know its version, 2.1 is assumed.
     */
    const DEFAULT_VERSION = self::VCARD21;
    /**
     * Validates the node for correctness.
     *
     * The following options are supported:
     *   Node::REPAIR - May attempt to automatically repair the problem.
     *
     * This method returns an array with detected problems.
     * Every element has the following properties:
     *
     *  * level - problem level.
     *  * message - A human-readable string describing the issue.
     *  * node - A reference to the problematic node.
     *
     * The level means:
     *   1 - The issue was repaired (only happens if REPAIR was turned on)
     *   2 - An inconsequential issue
     *   3 - A severe issue.
     *
     * @param int $options
     *
     * @return array
     */
    public function validate($options = 0)
    {
        $warnings = [];
        $versionMap = [self::VCARD21 => '2.1', self::VCARD30 => '3.0', self::VCARD40 => '4.0'];
        $version = $this->select('VERSION');
        if (1 === \count($version)) {
            $version = (string) $this->VERSION;
            if ('2.1' !== $version && '3.0' !== $version && '4.0' !== $version) {
                $warnings[] = ['level' => 3, 'message' => 'Only vcard version 4.0 (RFC6350), version 3.0 (RFC2426) or version 2.1 (icm-vcard-2.1) are supported.', 'node' => $this];
                if ($options & self::REPAIR) {
                    $this->VERSION = $versionMap[self::DEFAULT_VERSION];
                }
            }
            if ('2.1' === $version && $options & self::PROFILE_CARDDAV) {
                $warnings[] = ['level' => 3, 'message' => 'CardDAV servers are not allowed to accept vCard 2.1.', 'node' => $this];
            }
        }
        $uid = $this->select('UID');
        if (0 === \count($uid)) {
            if ($options & self::PROFILE_CARDDAV) {
                // Required for CardDAV
                $warningLevel = 3;
                $message = 'vCards on CardDAV servers MUST have a UID property.';
            } else {
                // Not required for regular vcards
                $warningLevel = 2;
                $message = 'Adding a UID to a vCard property is recommended.';
            }
            if ($options & self::REPAIR) {
                $this->UID = \WappoVendor\Sabre\VObject\UUIDUtil::getUUID();
                $warningLevel = 1;
            }
            $warnings[] = ['level' => $warningLevel, 'message' => $message, 'node' => $this];
        }
        $fn = $this->select('FN');
        if (1 !== \count($fn)) {
            $repaired = false;
            if ($options & self::REPAIR && 0 === \count($fn)) {
                // We're going to try to see if we can use the contents of the
                // N property.
                if (isset($this->N)) {
                    $value = \explode(';', (string) $this->N);
                    if (isset($value[1]) && $value[1]) {
                        $this->FN = $value[1] . ' ' . $value[0];
                    } else {
                        $this->FN = $value[0];
                    }
                    $repaired = true;
                    // Otherwise, the ORG property may work
                } elseif (isset($this->ORG)) {
                    $this->FN = (string) $this->ORG;
                    $repaired = true;
                    // Otherwise, the EMAIL property may work
                } elseif (isset($this->EMAIL)) {
                    $this->FN = (string) $this->EMAIL;
                    $repaired = true;
                }
            }
            $warnings[] = ['level' => $repaired ? 1 : 3, 'message' => 'The FN property must appear in the VCARD component exactly 1 time', 'node' => $this];
        }
        return \array_merge(parent::validate($options), $warnings);
    }
    /**
     * A simple list of validation rules.
     *
     * This is simply a list of properties, and how many times they either
     * must or must not appear.
     *
     * Possible values per property:
     *   * 0 - Must not appear.
     *   * 1 - Must appear exactly once.
     *   * + - Must appear at least once.
     *   * * - Can appear any number of times.
     *   * ? - May appear, but not more than once.
     *
     * @var array
     */
    public function getValidationRules()
    {
        return [
            'ADR' => '*',
            'ANNIVERSARY' => '?',
            'BDAY' => '?',
            'CALADRURI' => '*',
            'CALURI' => '*',
            'CATEGORIES' => '*',
            'CLIENTPIDMAP' => '*',
            'EMAIL' => '*',
            'FBURL' => '*',
            'IMPP' => '*',
            'GENDER' => '?',
            'GEO' => '*',
            'KEY' => '*',
            'KIND' => '?',
            'LANG' => '*',
            'LOGO' => '*',
            'MEMBER' => '*',
            'N' => '?',
            'NICKNAME' => '*',
            'NOTE' => '*',
            'ORG' => '*',
            'PHOTO' => '*',
            'PRODID' => '?',
            'RELATED' => '*',
            'REV' => '?',
            'ROLE' => '*',
            'SOUND' => '*',
            'SOURCE' => '*',
            'TEL' => '*',
            'TITLE' => '*',
            'TZ' => '*',
            'URL' => '*',
            'VERSION' => '1',
            'XML' => '*',
            // FN is commented out, because it's already handled by the
            // validate function, which may also try to repair it.
            // 'FN'           => '+',
            'UID' => '?',
        ];
    }
    /**
     * Returns a preferred field.
     *
     * VCards can indicate wether a field such as ADR, TEL or EMAIL is
     * preferred by specifying TYPE=PREF (vcard 2.1, 3) or PREF=x (vcard 4, x
     * being a number between 1 and 100).
     *
     * If neither of those parameters are specified, the first is returned, if
     * a field with that name does not exist, null is returned.
     *
     * @param string $fieldName
     *
     * @return VObject\Property|null
     */
    public function preferred($propertyName)
    {
        $preferred = null;
        $lastPref = 101;
        foreach ($this->select($propertyName) as $field) {
            $pref = 101;
            if (isset($field['TYPE']) && $field['TYPE']->has('PREF')) {
                $pref = 1;
            } elseif (isset($field['PREF'])) {
                $pref = $field['PREF']->getValue();
            }
            if ($pref < $lastPref || \is_null($preferred)) {
                $preferred = $field;
                $lastPref = $pref;
            }
        }
        return $preferred;
    }
    /**
     * Returns a property with a specific TYPE value (ADR, TEL, or EMAIL).
     *
     * This function will return null if the property does not exist. If there are
     * multiple properties with the same TYPE value, only one will be returned.
     *
     * @param string $propertyName
     * @param string $type
     *
     * @return VObject\Property|null
     */
    public function getByType($propertyName, $type)
    {
        foreach ($this->select($propertyName) as $field) {
            if (isset($field['TYPE']) && $field['TYPE']->has($type)) {
                return $field;
            }
        }
    }
    /**
     * This method should return a list of default property values.
     *
     * @return array
     */
    protected function getDefaults()
    {
        return ['VERSION' => '4.0', 'PRODID' => '-//Sabre//Sabre VObject ' . \WappoVendor\Sabre\VObject\Version::VERSION . '//EN', 'UID' => 'sabre-vobject-' . \WappoVendor\Sabre\VObject\UUIDUtil::getUUID()];
    }
    /**
     * This method returns an array, with the representation as it should be
     * encoded in json. This is used to create jCard or jCal documents.
     *
     * @return array
     */
    public function jsonSerialize()
    {
        // A vcard does not have sub-components, so we're overriding this
        // method to remove that array element.
        $properties = [];
        foreach ($this->children() as $child) {
            $properties[] = $child->jsonSerialize();
        }
        return [\strtolower($this->name), $properties];
    }
    /**
     * This method serializes the data into XML. This is used to create xCard or
     * xCal documents.
     *
     * @param Xml\Writer $writer XML writer
     */
    public function xmlSerialize(\WappoVendor\Sabre\Xml\Writer $writer)
    {
        $propertiesByGroup = [];
        foreach ($this->children() as $property) {
            $group = $property->group;
            if (!isset($propertiesByGroup[$group])) {
                $propertiesByGroup[$group] = [];
            }
            $propertiesByGroup[$group][] = $property;
        }
        $writer->startElement(\strtolower($this->name));
        foreach ($propertiesByGroup as $group => $properties) {
            if (!empty($group)) {
                $writer->startElement('group');
                $writer->writeAttribute('name', \strtolower($group));
            }
            foreach ($properties as $property) {
                switch ($property->name) {
                    case 'VERSION':
                        break;
                    case 'XML':
                        $value = $property->getParts();
                        $fragment = new \WappoVendor\Sabre\Xml\Element\XmlFragment($value[0]);
                        $writer->write($fragment);
                        break;
                    default:
                        $property->xmlSerialize($writer);
                        break;
                }
            }
            if (!empty($group)) {
                $writer->endElement();
            }
        }
        $writer->endElement();
    }
    /**
     * Returns the default class for a property name.
     *
     * @param string $propertyName
     *
     * @return string
     */
    public function getClassNameForPropertyName($propertyName)
    {
        $className = parent::getClassNameForPropertyName($propertyName);
        // In vCard 4, BINARY no longer exists, and we need URI instead.
        if ('Sabre\\VObject\\Property\\Binary' == $className && self::VCARD40 === $this->getDocumentType()) {
            return 'WappoVendor\\Sabre\\VObject\\Property\\Uri';
        }
        return $className;
    }
}
