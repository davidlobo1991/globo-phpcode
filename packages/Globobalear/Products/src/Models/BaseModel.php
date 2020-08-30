<?php
/**
 * Created by PhpStorm.
 * User: mramonell
 * Date: 17/7/17
 * Time: 11:40
 */

namespace Globobalear\Products\Models;


use Illuminate\Database\Eloquent\Model;

class BaseModel extends Model
{
    /**
     * Override. Create a new Eloquent query builder for the model.
     *
     * @param  \Illuminate\Database\Query\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder|EloquentBuilder
     **/
    public function newEloquentBuilder($query): EloquentBuilder
    {
        return new EloquentBuilder($query);
    }
}
