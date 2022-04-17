<?php

namespace WappoVendor\Illuminate\Database\Eloquent\Relations;

use BadMethodCallException;
use WappoVendor\Illuminate\Database\Eloquent\Model;
use WappoVendor\Illuminate\Database\Eloquent\Builder;
use WappoVendor\Illuminate\Database\Eloquent\Collection;
/**
 * @mixin \Illuminate\Database\Eloquent\Builder
 */
class MorphTo extends \WappoVendor\Illuminate\Database\Eloquent\Relations\BelongsTo
{
    /**
     * The type of the polymorphic relation.
     *
     * @var string
     */
    protected $morphType;
    /**
     * The models whose relations are being eager loaded.
     *
     * @var \Illuminate\Database\Eloquent\Collection
     */
    protected $models;
    /**
     * All of the models keyed by ID.
     *
     * @var array
     */
    protected $dictionary = [];
    /**
     * A buffer of dynamic calls to query macros.
     *
     * @var array
     */
    protected $macroBuffer = [];
    /**
     * Create a new morph to relationship instance.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  \Illuminate\Database\Eloquent\Model  $parent
     * @param  string  $foreignKey
     * @param  string  $ownerKey
     * @param  string  $type
     * @param  string  $relation
     * @return void
     */
    public function __construct(\WappoVendor\Illuminate\Database\Eloquent\Builder $query, \WappoVendor\Illuminate\Database\Eloquent\Model $parent, $foreignKey, $ownerKey, $type, $relation)
    {
        $this->morphType = $type;
        parent::__construct($query, $parent, $foreignKey, $ownerKey, $relation);
    }
    /**
     * Set the constraints for an eager load of the relation.
     *
     * @param  array  $models
     * @return void
     */
    public function addEagerConstraints(array $models)
    {
        $this->buildDictionary($this->models = \WappoVendor\Illuminate\Database\Eloquent\Collection::make($models));
    }
    /**
     * Build a dictionary with the models.
     *
     * @param  \Illuminate\Database\Eloquent\Collection  $models
     * @return void
     */
    protected function buildDictionary(\WappoVendor\Illuminate\Database\Eloquent\Collection $models)
    {
        foreach ($models as $model) {
            if ($model->{$this->morphType}) {
                $this->dictionary[$model->{$this->morphType}][$model->{$this->foreignKey}][] = $model;
            }
        }
    }
    /**
     * Get the results of the relationship.
     *
     * @return mixed
     */
    public function getResults()
    {
        return $this->ownerKey ? $this->query->first() : null;
    }
    /**
     * Get the results of the relationship.
     *
     * Called via eager load method of Eloquent query builder.
     *
     * @return mixed
     */
    public function getEager()
    {
        foreach (\array_keys($this->dictionary) as $type) {
            $this->matchToMorphParents($type, $this->getResultsByType($type));
        }
        return $this->models;
    }
    /**
     * Get all of the relation results for a type.
     *
     * @param  string  $type
     * @return \Illuminate\Database\Eloquent\Collection
     */
    protected function getResultsByType($type)
    {
        $instance = $this->createModelByType($type);
        $query = $this->replayMacros($instance->newQuery())->mergeConstraintsFrom($this->getQuery())->with($this->getQuery()->getEagerLoads());
        return $query->whereIn($instance->getTable() . '.' . $instance->getKeyName(), $this->gatherKeysByType($type))->get();
    }
    /**
     * Gather all of the foreign keys for a given type.
     *
     * @param  string  $type
     * @return array
     */
    protected function gatherKeysByType($type)
    {
        return \WappointmentLv::collect($this->dictionary[$type])->map(function ($models) {
            return \WappointmentLv::head($models)->{$this->foreignKey};
        })->values()->unique()->all();
    }
    /**
     * Create a new model instance by type.
     *
     * @param  string  $type
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function createModelByType($type)
    {
        $class = \WappoVendor\Illuminate\Database\Eloquent\Model::getActualClassNameForMorph($type);
        return new $class();
    }
    /**
     * Match the eagerly loaded results to their parents.
     *
     * @param  array   $models
     * @param  \Illuminate\Database\Eloquent\Collection  $results
     * @param  string  $relation
     * @return array
     */
    public function match(array $models, \WappoVendor\Illuminate\Database\Eloquent\Collection $results, $relation)
    {
        return $models;
    }
    /**
     * Match the results for a given type to their parents.
     *
     * @param  string  $type
     * @param  \Illuminate\Database\Eloquent\Collection  $results
     * @return void
     */
    protected function matchToMorphParents($type, \WappoVendor\Illuminate\Database\Eloquent\Collection $results)
    {
        foreach ($results as $result) {
            if (isset($this->dictionary[$type][$result->getKey()])) {
                foreach ($this->dictionary[$type][$result->getKey()] as $model) {
                    $model->setRelation($this->relation, $result);
                }
            }
        }
    }
    /**
     * Associate the model instance to the given parent.
     *
     * @param  \Illuminate\Database\Eloquent\Model  $model
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function associate($model)
    {
        $this->parent->setAttribute($this->foreignKey, $model instanceof \WappoVendor\Illuminate\Database\Eloquent\Model ? $model->getKey() : null);
        $this->parent->setAttribute($this->morphType, $model instanceof \WappoVendor\Illuminate\Database\Eloquent\Model ? $model->getMorphClass() : null);
        return $this->parent->setRelation($this->relation, $model);
    }
    /**
     * Dissociate previously associated model from the given parent.
     *
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function dissociate()
    {
        $this->parent->setAttribute($this->foreignKey, null);
        $this->parent->setAttribute($this->morphType, null);
        return $this->parent->setRelation($this->relation, null);
    }
    /**
     * Get the foreign key "type" name.
     *
     * @return string
     */
    public function getMorphType()
    {
        return $this->morphType;
    }
    /**
     * Get the dictionary used by the relationship.
     *
     * @return array
     */
    public function getDictionary()
    {
        return $this->dictionary;
    }
    /**
     * Replay stored macro calls on the actual related instance.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    protected function replayMacros(\WappoVendor\Illuminate\Database\Eloquent\Builder $query)
    {
        foreach ($this->macroBuffer as $macro) {
            $query->{$macro['method']}(...$macro['parameters']);
        }
        return $query;
    }
    /**
     * Handle dynamic method calls to the relationship.
     *
     * @param  string  $method
     * @param  array   $parameters
     * @return mixed
     */
    public function __call($method, $parameters)
    {
        try {
            return parent::__call($method, $parameters);
        } catch (\BadMethodCallException $e) {
            $this->macroBuffer[] = \compact('method', 'parameters');
            return $this;
        }
    }
}
