<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace WappoVendor\Symfony\Component\Translation\Loader;

use WappoVendor\Symfony\Component\Config\Resource\FileResource;
use WappoVendor\Symfony\Component\Config\Util\XmlUtils;
use WappoVendor\Symfony\Component\Translation\Exception\InvalidArgumentException;
use WappoVendor\Symfony\Component\Translation\Exception\InvalidResourceException;
use WappoVendor\Symfony\Component\Translation\Exception\NotFoundResourceException;
use WappoVendor\Symfony\Component\Translation\MessageCatalogue;
/**
 * XliffFileLoader loads translations from XLIFF files.
 *
 * @author Fabien Potencier <fabien@symfony.com>
 */
class XliffFileLoader implements \WappoVendor\Symfony\Component\Translation\Loader\LoaderInterface
{
    /**
     * {@inheritdoc}
     */
    public function load($resource, $locale, $domain = 'messages')
    {
        if (!\stream_is_local($resource)) {
            throw new \WappoVendor\Symfony\Component\Translation\Exception\InvalidResourceException(\sprintf('This is not a local file "%s".', $resource));
        }
        if (!\file_exists($resource)) {
            throw new \WappoVendor\Symfony\Component\Translation\Exception\NotFoundResourceException(\sprintf('File "%s" not found.', $resource));
        }
        $catalogue = new \WappoVendor\Symfony\Component\Translation\MessageCatalogue($locale);
        $this->extract($resource, $catalogue, $domain);
        if (\class_exists('WappoVendor\\Symfony\\Component\\Config\\Resource\\FileResource')) {
            $catalogue->addResource(new \WappoVendor\Symfony\Component\Config\Resource\FileResource($resource));
        }
        return $catalogue;
    }
    private function extract($resource, \WappoVendor\Symfony\Component\Translation\MessageCatalogue $catalogue, $domain)
    {
        try {
            $dom = \WappoVendor\Symfony\Component\Config\Util\XmlUtils::loadFile($resource);
        } catch (\InvalidArgumentException $e) {
            throw new \WappoVendor\Symfony\Component\Translation\Exception\InvalidResourceException(\sprintf('Unable to load "%s": %s.', $resource, $e->getMessage()), $e->getCode(), $e);
        }
        $xliffVersion = $this->getVersionNumber($dom);
        $this->validateSchema($xliffVersion, $dom, $this->getSchema($xliffVersion));
        if ('1.2' === $xliffVersion) {
            $this->extractXliff1($dom, $catalogue, $domain);
        }
        if ('2.0' === $xliffVersion) {
            $this->extractXliff2($dom, $catalogue, $domain);
        }
    }
    /**
     * Extract messages and metadata from DOMDocument into a MessageCatalogue.
     *
     * @param \DOMDocument     $dom       Source to extract messages and metadata
     * @param MessageCatalogue $catalogue Catalogue where we'll collect messages and metadata
     * @param string           $domain    The domain
     */
    private function extractXliff1(\DOMDocument $dom, \WappoVendor\Symfony\Component\Translation\MessageCatalogue $catalogue, $domain)
    {
        $xml = \simplexml_import_dom($dom);
        $encoding = \strtoupper($dom->encoding);
        $xml->registerXPathNamespace('xliff', 'urn:oasis:names:tc:xliff:document:1.2');
        foreach ($xml->xpath('//xliff:trans-unit') as $translation) {
            $attributes = $translation->attributes();
            if (!(isset($attributes['resname']) || isset($translation->source))) {
                continue;
            }
            $source = isset($attributes['resname']) && $attributes['resname'] ? $attributes['resname'] : $translation->source;
            // If the xlf file has another encoding specified, try to convert it because
            // simple_xml will always return utf-8 encoded values
            $target = $this->utf8ToCharset((string) (isset($translation->target) ? $translation->target : $translation->source), $encoding);
            $catalogue->set((string) $source, $target, $domain);
            $metadata = [];
            if ($notes = $this->parseNotesMetadata($translation->note, $encoding)) {
                $metadata['notes'] = $notes;
            }
            if (isset($translation->target) && $translation->target->attributes()) {
                $metadata['target-attributes'] = [];
                foreach ($translation->target->attributes() as $key => $value) {
                    $metadata['target-attributes'][$key] = (string) $value;
                }
            }
            if (isset($attributes['id'])) {
                $metadata['id'] = (string) $attributes['id'];
            }
            $catalogue->setMetadata((string) $source, $metadata, $domain);
        }
    }
    /**
     * @param string $domain
     */
    private function extractXliff2(\DOMDocument $dom, \WappoVendor\Symfony\Component\Translation\MessageCatalogue $catalogue, $domain)
    {
        $xml = \simplexml_import_dom($dom);
        $encoding = \strtoupper($dom->encoding);
        $xml->registerXPathNamespace('xliff', 'urn:oasis:names:tc:xliff:document:2.0');
        foreach ($xml->xpath('//xliff:unit') as $unit) {
            foreach ($unit->segment as $segment) {
                $source = $segment->source;
                // If the xlf file has another encoding specified, try to convert it because
                // simple_xml will always return utf-8 encoded values
                $target = $this->utf8ToCharset((string) (isset($segment->target) ? $segment->target : $source), $encoding);
                $catalogue->set((string) $source, $target, $domain);
                $metadata = [];
                if (isset($segment->target) && $segment->target->attributes()) {
                    $metadata['target-attributes'] = [];
                    foreach ($segment->target->attributes() as $key => $value) {
                        $metadata['target-attributes'][$key] = (string) $value;
                    }
                }
                if (isset($unit->notes)) {
                    $metadata['notes'] = [];
                    foreach ($unit->notes->note as $noteNode) {
                        $note = [];
                        foreach ($noteNode->attributes() as $key => $value) {
                            $note[$key] = (string) $value;
                        }
                        $note['content'] = (string) $noteNode;
                        $metadata['notes'][] = $note;
                    }
                }
                $catalogue->setMetadata((string) $source, $metadata, $domain);
            }
        }
    }
    /**
     * Convert a UTF8 string to the specified encoding.
     *
     * @param string $content  String to decode
     * @param string $encoding Target encoding
     *
     * @return string
     */
    private function utf8ToCharset($content, $encoding = null)
    {
        if ('UTF-8' !== $encoding && !empty($encoding)) {
            return \mb_convert_encoding($content, $encoding, 'UTF-8');
        }
        return $content;
    }
    /**
     * Validates and parses the given file into a DOMDocument.
     *
     * @param string $file
     * @param string $schema source of the schema
     *
     * @throws InvalidResourceException
     */
    private function validateSchema($file, \DOMDocument $dom, $schema)
    {
        $internalErrors = \libxml_use_internal_errors(true);
        $disableEntities = \libxml_disable_entity_loader(false);
        if (!@$dom->schemaValidateSource($schema)) {
            \libxml_disable_entity_loader($disableEntities);
            throw new \WappoVendor\Symfony\Component\Translation\Exception\InvalidResourceException(\sprintf('Invalid resource provided: "%s"; Errors: %s.', $file, \implode("\n", $this->getXmlErrors($internalErrors))));
        }
        \libxml_disable_entity_loader($disableEntities);
        $dom->normalizeDocument();
        \libxml_clear_errors();
        \libxml_use_internal_errors($internalErrors);
    }
    private function getSchema($xliffVersion)
    {
        if ('1.2' === $xliffVersion) {
            $schemaSource = \file_get_contents(__DIR__ . '/schema/dic/xliff-core/xliff-core-1.2-strict.xsd');
            $xmlUri = 'http://www.w3.org/2001/xml.xsd';
        } elseif ('2.0' === $xliffVersion) {
            $schemaSource = \file_get_contents(__DIR__ . '/schema/dic/xliff-core/xliff-core-2.0.xsd');
            $xmlUri = 'informativeCopiesOf3rdPartySchemas/w3c/xml.xsd';
        } else {
            throw new \WappoVendor\Symfony\Component\Translation\Exception\InvalidArgumentException(\sprintf('No support implemented for loading XLIFF version "%s".', $xliffVersion));
        }
        return $this->fixXmlLocation($schemaSource, $xmlUri);
    }
    /**
     * Internally changes the URI of a dependent xsd to be loaded locally.
     *
     * @param string $schemaSource Current content of schema file
     * @param string $xmlUri       External URI of XML to convert to local
     *
     * @return string
     */
    private function fixXmlLocation($schemaSource, $xmlUri)
    {
        $newPath = \str_replace('\\', '/', __DIR__) . '/schema/dic/xliff-core/xml.xsd';
        $parts = \explode('/', $newPath);
        $locationstart = 'file:///';
        if (0 === \stripos($newPath, 'phar://')) {
            $tmpfile = \tempnam(\sys_get_temp_dir(), 'symfony');
            if ($tmpfile) {
                \copy($newPath, $tmpfile);
                $parts = \explode('/', \str_replace('\\', '/', $tmpfile));
            } else {
                \array_shift($parts);
                $locationstart = 'phar:///';
            }
        }
        $drive = '\\' === \DIRECTORY_SEPARATOR ? \array_shift($parts) . '/' : '';
        $newPath = $locationstart . $drive . \implode('/', \array_map('rawurlencode', $parts));
        return \str_replace($xmlUri, $newPath, $schemaSource);
    }
    /**
     * Returns the XML errors of the internal XML parser.
     *
     * @param bool $internalErrors
     *
     * @return array An array of errors
     */
    private function getXmlErrors($internalErrors)
    {
        $errors = [];
        foreach (\libxml_get_errors() as $error) {
            $errors[] = \sprintf('[%s %s] %s (in %s - line %d, column %d)', \LIBXML_ERR_WARNING == $error->level ? 'WARNING' : 'ERROR', $error->code, \trim($error->message), $error->file ?: 'n/a', $error->line, $error->column);
        }
        \libxml_clear_errors();
        \libxml_use_internal_errors($internalErrors);
        return $errors;
    }
    /**
     * Gets xliff file version based on the root "version" attribute.
     * Defaults to 1.2 for backwards compatibility.
     *
     * @throws InvalidArgumentException
     *
     * @return string
     */
    private function getVersionNumber(\DOMDocument $dom)
    {
        /** @var \DOMNode $xliff */
        foreach ($dom->getElementsByTagName('xliff') as $xliff) {
            $version = $xliff->attributes->getNamedItem('version');
            if ($version) {
                return $version->nodeValue;
            }
            $namespace = $xliff->attributes->getNamedItem('xmlns');
            if ($namespace) {
                if (0 !== \substr_compare('urn:oasis:names:tc:xliff:document:', $namespace->nodeValue, 0, 34)) {
                    throw new \WappoVendor\Symfony\Component\Translation\Exception\InvalidArgumentException(\sprintf('Not a valid XLIFF namespace "%s".', $namespace));
                }
                return \substr($namespace, 34);
            }
        }
        // Falls back to v1.2
        return '1.2';
    }
    /**
     * @param string|null $encoding
     *
     * @return array
     */
    private function parseNotesMetadata(\SimpleXMLElement $noteElement = null, $encoding = null)
    {
        $notes = [];
        if (null === $noteElement) {
            return $notes;
        }
        /** @var \SimpleXMLElement $xmlNote */
        foreach ($noteElement as $xmlNote) {
            $noteAttributes = $xmlNote->attributes();
            $note = ['content' => $this->utf8ToCharset((string) $xmlNote, $encoding)];
            if (isset($noteAttributes['priority'])) {
                $note['priority'] = (int) $noteAttributes['priority'];
            }
            if (isset($noteAttributes['from'])) {
                $note['from'] = (string) $noteAttributes['from'];
            }
            $notes[] = $note;
        }
        return $notes;
    }
}
