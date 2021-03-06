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
 * Type that maps ab SQL BINARY/VARBINARY to a PHP resource stream.
 *
 * @author Steve Müller <st.mueller@dzh-online.de>
 * @since  2.5
 */
class BinaryType extends \WappoVendor\Doctrine\DBAL\Types\Type
{
    /**
     * {@inheritdoc}
     */
    public function getSQLDeclaration(array $fieldDeclaration, \WappoVendor\Doctrine\DBAL\Platforms\AbstractPlatform $platform)
    {
        return $platform->getBinaryTypeDeclarationSQL($fieldDeclaration);
    }
    /**
     * {@inheritdoc}
     */
    public function convertToPHPValue($value, \WappoVendor\Doctrine\DBAL\Platforms\AbstractPlatform $platform)
    {
        if (null === $value) {
            return null;
        }
        if (\is_string($value)) {
            $fp = \fopen('php://temp', 'rb+');
            \fwrite($fp, $value);
            \fseek($fp, 0);
            $value = $fp;
        }
        if (!\is_resource($value)) {
            throw \WappoVendor\Doctrine\DBAL\Types\ConversionException::conversionFailed($value, self::BINARY);
        }
        return $value;
    }
    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return \WappoVendor\Doctrine\DBAL\Types\Type::BINARY;
    }
    /**
     * {@inheritdoc}
     */
    public function getBindingType()
    {
        return \PDO::PARAM_LOB;
    }
}
