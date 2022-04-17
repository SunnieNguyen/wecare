<?php

namespace WappoVendor\Illuminate\Database\Eloquent;

interface Scope
{
    /**
     * Apply the scope to a given Eloquent query builder.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $builder
     * @param  \Illuminate\Database\Eloquent\Model  $model
     * @return void
     */
    public function apply(\WappoVendor\Illuminate\Database\Eloquent\Builder $builder, \WappoVendor\Illuminate\Database\Eloquent\Model $model);
}
