<?php

declare (strict_types=1);
namespace WappoVendor\Doctrine\Common\Cache;

use DateTime;
use MongoDB\BSON\Binary;
use MongoDB\BSON\UTCDateTime;
use WappoVendor\MongoDB\Collection;
use WappoVendor\MongoDB\Database;
use MongoDB\Driver\Exception\Exception;
use WappoVendor\MongoDB\Model\BSONDocument;
use function serialize;
use function time;
use function unserialize;
/**
 * MongoDB cache provider for ext-mongodb
 *
 * @internal Do not use - will be removed in 2.0. Use MongoDBCache instead
 */
class ExtMongoDBCache extends \WappoVendor\Doctrine\Common\Cache\CacheProvider
{
    /** @var Database */
    private $database;
    /** @var Collection */
    private $collection;
    /** @var bool */
    private $expirationIndexCreated = false;
    /**
     * This provider will default to the write concern and read preference
     * options set on the Database instance (or inherited from MongoDB or
     * Client). Using an unacknowledged write concern (< 1) may make the return
     * values of delete() and save() unreliable. Reading from secondaries may
     * make contain() and fetch() unreliable.
     *
     * @see http://www.php.net/manual/en/mongo.readpreferences.php
     * @see http://www.php.net/manual/en/mongo.writeconcerns.php
     */
    public function __construct(\WappoVendor\MongoDB\Collection $collection)
    {
        // Ensure there is no typemap set - we want to use our own
        $this->collection = $collection->withOptions(['typeMap' => null]);
        $this->database = new \WappoVendor\MongoDB\Database($collection->getManager(), $collection->getDatabaseName());
    }
    /**
     * {@inheritdoc}
     */
    protected function doFetch($id)
    {
        $document = $this->collection->findOne(['_id' => $id], [\WappoVendor\Doctrine\Common\Cache\MongoDBCache::DATA_FIELD, \WappoVendor\Doctrine\Common\Cache\MongoDBCache::EXPIRATION_FIELD]);
        if ($document === null) {
            return false;
        }
        if ($this->isExpired($document)) {
            $this->createExpirationIndex();
            $this->doDelete($id);
            return false;
        }
        return \unserialize($document[\WappoVendor\Doctrine\Common\Cache\MongoDBCache::DATA_FIELD]->getData());
    }
    /**
     * {@inheritdoc}
     */
    protected function doContains($id)
    {
        $document = $this->collection->findOne(['_id' => $id], [\WappoVendor\Doctrine\Common\Cache\MongoDBCache::EXPIRATION_FIELD]);
        if ($document === null) {
            return false;
        }
        if ($this->isExpired($document)) {
            $this->createExpirationIndex();
            $this->doDelete($id);
            return false;
        }
        return true;
    }
    /**
     * {@inheritdoc}
     */
    protected function doSave($id, $data, $lifeTime = 0)
    {
        try {
            $this->collection->updateOne(['_id' => $id], ['$set' => [\WappoVendor\Doctrine\Common\Cache\MongoDBCache::EXPIRATION_FIELD => $lifeTime > 0 ? new \MongoDB\BSON\UTCDateTime((\time() + $lifeTime) * 1000) : null, \WappoVendor\Doctrine\Common\Cache\MongoDBCache::DATA_FIELD => new \MongoDB\BSON\Binary(\serialize($data), \MongoDB\BSON\Binary::TYPE_GENERIC)]], ['upsert' => true]);
        } catch (\MongoDB\Driver\Exception\Exception $e) {
            return false;
        }
        return true;
    }
    /**
     * {@inheritdoc}
     */
    protected function doDelete($id)
    {
        try {
            $this->collection->deleteOne(['_id' => $id]);
        } catch (\MongoDB\Driver\Exception\Exception $e) {
            return false;
        }
        return true;
    }
    /**
     * {@inheritdoc}
     */
    protected function doFlush()
    {
        try {
            // Use remove() in lieu of drop() to maintain any collection indexes
            $this->collection->deleteMany([]);
        } catch (\MongoDB\Driver\Exception\Exception $e) {
            return false;
        }
        return true;
    }
    /**
     * {@inheritdoc}
     */
    protected function doGetStats()
    {
        $uptime = null;
        $memoryUsage = null;
        try {
            $serverStatus = $this->database->command(['serverStatus' => 1, 'locks' => 0, 'metrics' => 0, 'recordStats' => 0, 'repl' => 0])->toArray()[0];
            $uptime = $serverStatus['uptime'] ?? null;
        } catch (\MongoDB\Driver\Exception\Exception $e) {
        }
        try {
            $collStats = $this->database->command(['collStats' => $this->collection->getCollectionName()])->toArray()[0];
            $memoryUsage = $collStats['size'] ?? null;
        } catch (\MongoDB\Driver\Exception\Exception $e) {
        }
        return [\WappoVendor\Doctrine\Common\Cache\Cache::STATS_HITS => null, \WappoVendor\Doctrine\Common\Cache\Cache::STATS_MISSES => null, \WappoVendor\Doctrine\Common\Cache\Cache::STATS_UPTIME => $uptime, \WappoVendor\Doctrine\Common\Cache\Cache::STATS_MEMORY_USAGE => $memoryUsage, \WappoVendor\Doctrine\Common\Cache\Cache::STATS_MEMORY_AVAILABLE => null];
    }
    /**
     * Check if the document is expired.
     */
    private function isExpired(\WappoVendor\MongoDB\Model\BSONDocument $document) : bool
    {
        return isset($document[\WappoVendor\Doctrine\Common\Cache\MongoDBCache::EXPIRATION_FIELD]) && $document[\WappoVendor\Doctrine\Common\Cache\MongoDBCache::EXPIRATION_FIELD] instanceof \MongoDB\BSON\UTCDateTime && $document[\WappoVendor\Doctrine\Common\Cache\MongoDBCache::EXPIRATION_FIELD]->toDateTime() < new \DateTime();
    }
    private function createExpirationIndex() : void
    {
        if ($this->expirationIndexCreated) {
            return;
        }
        $this->collection->createIndex([\WappoVendor\Doctrine\Common\Cache\MongoDBCache::EXPIRATION_FIELD => 1], ['background' => true, 'expireAfterSeconds' => 0]);
    }
}
