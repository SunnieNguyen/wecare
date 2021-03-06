<?php

namespace WappoVendor\Sabre\VObject\Property\VCard;

/**
 * Date property.
 *
 * This object encodes vCard DATE values.
 *
 * @copyright Copyright (C) fruux GmbH (https://fruux.com/)
 * @author Evert Pot (http://evertpot.com/)
 * @license http://sabre.io/license/ Modified BSD License
 */
class Date extends \WappoVendor\Sabre\VObject\Property\VCard\DateAndOrTime
{
    /**
     * Returns the type of value.
     *
     * This corresponds to the VALUE= parameter. Every property also has a
     * 'default' valueType.
     *
     * @return string
     */
    public function getValueType()
    {
        return 'DATE';
    }
    /**
     * Sets the property as a DateTime object.
     *
     * @param \DateTimeInterface $dt
     */
    public function setDateTime(\DateTimeInterface $dt)
    {
        $this->value = $dt->format('Ymd');
    }
}
