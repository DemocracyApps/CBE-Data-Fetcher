<?php namespace App\DRSObjects;

use Illuminate\Database\Eloquent\Model;

class EloquentPropertiedObject extends Model
{
    protected $properties = [];

    public function __construct()
    {
        parent::__construct();

        self::saving(function ($dbObject) {
            $this->attributes['properties'] = json_encode($this->properties);
        });

    }

    public function newFromBuilder($attributes = array(), $connection = NULL)
    {
        $instance = parent::newFromBuilder($attributes, $connection);
        if (array_key_exists('properties', $instance->attributes)) {
            $instance->properties = json_decode($instance->attributes['properties'], true);
        }
        return $instance;
    }

    public function setProperty ($propName, $propValue)
    {
        $this->properties[$propName] = $propValue;
    }

    public function hasProperty ($propName)
    {
        $hasProperty = false;
        if ($this->properties) {
            if (array_key_exists($propName, $this->properties)) {
                $hasProperty = true;
            }
        }
        return $hasProperty;
    }

    public function getProperty ($propName)
    {
        $propValue = null;
        if ($this->properties && array_key_exists($propName, $this->properties)) {
            $propValue = $this->properties[$propName];
        }
        return $propValue;
    }



}