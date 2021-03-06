<?php

/*
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS
 * "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT
 * LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR
 * A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT
 * OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL,
 * SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT
 * LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE,
 * DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY
 * THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT
 * (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE
 * OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 *
 * This software consists of voluntary contributions made by many individuals
 * and is licensed under the MIT license. For more information, see
 * <http://www.doctrine-project.org>.
 */
namespace WappoVendor\Doctrine\DBAL\Types;

use WappoVendor\Doctrine\DBAL\Platforms\AbstractPlatform;
/**
 * Type that maps an SQL TIME to a PHP DateTime object.
 *
 * @since 2.0
 */
class TimeType extends \WappoVendor\Doctrine\DBAL\Types\Type
{
    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return \WappoVendor\Doctrine\DBAL\Types\Type::TIME;
    }
    /**
     * {@inheritdoc}
     */
    public function getSQLDeclaration(array $fieldDeclaration, \WappoVendor\Doctrine\DBAL\Platforms\AbstractPlatform $platform)
    {
        return $platform->getTimeTypeDeclarationSQL($fieldDeclaration);
    }
    /**
     * {@inheritdoc}
     */
    public function convertToDatabaseValue($value, \WappoVendor\Doctrine\DBAL\Platforms\AbstractPlatform $platform)
    {
        return $value !== null ? $value->format($platform->getTimeFormatString()) : null;
    }
    /**
     * {@inheritdoc}
     */
    public function convertToPHPValue($value, \WappoVendor\Doctrine\DBAL\Platforms\AbstractPlatform $platform)
    {
        if ($value === null || $value instanceof \DateTime) {
            return $value;
        }
        $val = \DateTime::createFromFormat('!' . $platform->getTimeFormatString(), $value);
        if (!$val) {
            throw \WappoVendor\Doctrine\DBAL\Types\ConversionException::conversionFailedFormat($value, $this->getName(), $platform->getTimeFormatString());
        }
        return $val;
    }
}
