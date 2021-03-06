<?php

namespace WappoVendor\Illuminate\Database\Eloquent\Relations;

use WappoVendor\Illuminate\Database\Eloquent\Model;
use WappoVendor\Illuminate\Database\Eloquent\Collection;
use WappoVendor\Illuminate\Database\Eloquent\Relations\Concerns\SupportsDefaultModels;
class HasOne extends \WappoVendor\Illuminate\Database\Eloquent\Relations\HasOneOrMany
{
    use SupportsDefaultModels;
    /**
     * Get the results of the relationship.
     *
     * @return mixed
     */
    public function getResults()
    {
        return $this->query->first() ?: $this->getDefaultFor($this->parent);
    }
    /**
     * Initialize the relation on a set of models.
     *
     * @param  array   $models
     * @param  string  $relation
     * @return array
     */
    public function initRelation(array $models, $relation)
    {
        foreach ($models as $model) {
            $model->setRelation($relation, $this->getDefaultFor($model));
        }
        return $models;
    }
    /**
     * Match the eagerly loaded results to their parents.
     *
     * @param  array  $models
     * @param  \Illuminate\Database\Eloquent\Collection  $results
     * @param  string  $relation
     * @return array
     */
    public function match(array $models, \WappoVendor\Illuminate\Database\Eloquent\Collection $results, $relation)
    {
        return $this->matchOne($models, $results, $relation);
    }
    /**
     * Make a new related instance for the given model.
     *
     * @param  \Illuminate\Database\Eloquent\Model  $parent
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function newRelatedInstanceFor(\WappoVendor\Illuminate\Database\Eloquent\Model $parent)
    {
        return $this->related->newInstance()->setAttribute($this->getForeignKeyName(), $parent->{$this->localKey});
    }
}
