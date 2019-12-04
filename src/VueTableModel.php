<?php namespace Braceyourself\EloquentVueTable;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

abstract class VueTableModel extends Model
{
    protected $editable = [];

    protected $field_info = [];

    protected $field_options = [];

    protected $actions = [];

    public function meta()
    {
        return tap(EloquentVueTable::getMetaFields(), function (&$fields) {
            $fields = collect($fields)->mapWithKeys(function ($field) {
                return [$field => static::getFieldValue($field)];
            });
        });
    }


    public static function getFieldValue($field)
    {
        $inst = new static();

        return $inst->{Str::camel("get_$field")}();
    }


    public function getColumns()
    {
        return EloquentVueTable::getColumns($this);
    }

    public function getScopes()
    {
        return EloquentVueTable::getScopes($this);
    }

    public function getActions()
    {
        return $this->actions;
    }

    final public function getTotalCount()
    {
        return static::count();
    }

    public function getResourceSlug()
    {
        $collection = collect(explode('\\', static::class))
            ->map(function ($i) {
                return Str::kebab($i);
            })->filter(function($i){
                return strtolower($i) !== 'app';
            });

        $collection->push(Str::plural($collection->pop()))->implode('-');

        return $collection->implode('\\');

    }
}
