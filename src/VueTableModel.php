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
        return tap(EloquntVueTable::getMetaFields(), function (&$fields) {
            $fields = collect($fields)->mapWithKeys(function ($field) {
                return [$item => static::getFieldValue($field)];
            });
        });
    }


    public static function getFieldValue($field)
    {
        $inst = static::newInstance();

        return $inst->{Str::camel("get$field")}();
    }


    public function getTableColumns()
    {
        return EloquntVueTable::getTableColumns($this);
    }

    public function getScopes()
    {
        $reflection = new \ReflectionClass($this);
        $methods = $reflection->getMethods();

        return $methods->filter(function ($method) {
            return Str::startsWith($method->name, 'scope');
        });
    }

    public function getActions()
    {
        return $this->actions;
    }

    final public function getTotalCount()
    {
        return static::count();
    }

    public function getSlug()
    {
        return tap(collect(explode('\\', static::class)), function (Collection &$collection) {
            $collection->map(function ($i) {
                return Str::kebab($i);
            })->push(Str::plural($collection->pop()));
        });


    }
}
