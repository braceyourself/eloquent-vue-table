<?php namespace Braceyourself\EloquentVueTable;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Prophecy\Exception\Doubler\MethodNotFoundException;

class EloquentVueTable
{
    protected static $meta_fields = [
        'table',
        'resource_slug',
        'total_count',
        'actions',
        'hidden',
        'columns',
        'fillable',
        'scopes',
        'casts',

    ];

    /**
     * @param array $meta_fields
     */
    public static function metaFields(array $meta_fields): void
    {
        static::$meta_fields = $meta_fields;
    }

    public static function getMetaFields()
    {
        return static::$meta_fields;
    }


    /**
     * @param VueTableModel|string $instance
     * @return mixed
     */
    public static function getColumns($instance)
    {
        return Schema::connection($instance->getConnectionName())
            ->getColumnListing($instance->getTable());

    }


    public static function getScopes($instance){

        $reflection = new \ReflectionClass($instance);
        $methods = collect($reflection->getMethods());

        return $methods->filter(function ($method) {
            return Str::startsWith($method->name, 'scope');
        });

    }


}
