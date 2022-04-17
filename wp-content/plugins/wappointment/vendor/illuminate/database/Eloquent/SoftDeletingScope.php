<?php

namespace WappoVendor\Illuminate\Database\Eloquent;

class SoftDeletingScope implements \WappoVendor\Illuminate\Database\Eloquent\Scope
{
    /**
     * All of the extensions to be added to the builder.
     *
     * @var array
     */
    protected $extensions = ['Restore', 'WithTrashed', 'WithoutTrashed', 'OnlyTrashed'];
    /**
     * Apply the scope to a given Eloquent query builder.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $builder
     * @param  \Illuminate\Database\Eloquent\Model  $model
     * @return void
     */
    public function apply(\WappoVendor\Illuminate\Database\Eloquent\Builder $builder, \WappoVendor\Illuminate\Database\Eloquent\Model $model)
    {
        $builder->whereNull($model->getQualifiedDeletedAtColumn());
    }
    /**
     * Extend the query builder with the needed functions.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $builder
     * @return void
     */
    public function extend(\WappoVendor\Illuminate\Database\Eloquent\Builder $builder)
    {
        foreach ($this->extensions as $extension) {
            $this->{"add{$extension}"}($builder);
        }
        $builder->onDelete(function (\WappoVendor\Illuminate\Database\Eloquent\Builder $builder) {
            $column = $this->getDeletedAtColumn($builder);
            return $builder->update([$column => $builder->getModel()->freshTimestampString()]);
        });
    }
    /**
     * Get the "deleted at" column for the builder.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $builder
     * @return string
     */
    protected function getDeletedAtColumn(\WappoVendor\Illuminate\Database\Eloquent\Builder $builder)
    {
        if (\count((array) $builder->getQuery()->joins) > 0) {
            return $builder->getModel()->getQualifiedDeletedAtColumn();
        }
        return $builder->getModel()->getDeletedAtColumn();
    }
    /**
     * Add the restore extension to the builder.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $builder
     * @return void
     */
    protected function addRestore(\WappoVendor\Illuminate\Database\Eloquent\Builder $builder)
    {
        $builder->macro('restore', function (\WappoVendor\Illuminate\Database\Eloquent\Builder $builder) {
            $builder->withTrashed();
            return $builder->update([$builder->getModel()->getDeletedAtColumn() => null]);
        });
    }
    /**
     * Add the with-trashed extension to the builder.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $builder
     * @return void
     */
    protected function addWithTrashed(\WappoVendor\Illuminate\Database\Eloquent\Builder $builder)
    {
        $builder->macro('withTrashed', function (\WappoVendor\Illuminate\Database\Eloquent\Builder $builder, $withTrashed = true) {
            if (!$withTrashed) {
                return $builder->withoutTrashed();
            }
            return $builder->withoutGlobalScope($this);
        });
    }
    /**
     * Add the without-trashed extension to the builder.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $builder
     * @return void
     */
    protected function addWithoutTrashed(\WappoVendor\Illuminate\Database\Eloquent\Builder $builder)
    {
        $builder->macro('withoutTrashed', function (\WappoVendor\Illuminate\Database\Eloquent\Builder $builder) {
            $model = $builder->getModel();
            $builder->withoutGlobalScope($this)->whereNull($model->getQualifiedDeletedAtColumn());
            return $builder;
        });
    }
    /**
     * Add the only-trashed extension to the builder.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $builder
     * @return void
     */
    protected function addOnlyTrashed(\WappoVendor\Illuminate\Database\Eloquent\Builder $builder)
    {
        $builder->macro('onlyTrashed', function (\WappoVendor\Illuminate\Database\Eloquent\Builder $builder) {
            $model = $builder->getModel();
            $builder->withoutGlobalScope($this)->whereNotNull($model->getQualifiedDeletedAtColumn());
            return $builder;
        });
    }
}
