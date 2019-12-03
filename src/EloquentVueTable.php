<?php namespace Braceyourself\EloquentVueTable;

use Prophecy\Exception\Doubler\MethodNotFoundException;

class EloquentVueTable
{
    protected static $meta_fields = [];

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
    public static function getTableColumns($instance)
    {
        return Schema::connection($instance->getConnectionName())
            ->getColumnListing($instance->getTable());

    }


}
