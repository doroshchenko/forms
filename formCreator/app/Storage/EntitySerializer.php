<?php
/**
 * Created by PhpStorm.
 * User: dimador
 * Date: 11.10.16
 * Time: 15.07
 */

namespace formCreator\app\Storage;


class EntitySerializer
{
    public static function serialize($object)
    {
        $data = array();
        $reflection = new \ReflectionClass($object);
        $props = $reflection->getProperties();
        foreach ($props as $prop)
        {
            if ($prop->name != 'storage') {
                $data[$prop->name] = $object->{'get'.ucfirst($prop->name)}();
                if (gettype($data[$prop->name]) == 'object') {
                    self::serialize($data[$prop->name]);
                } elseif (gettype($data[$prop->name]) == 'array') {
                    foreach ($data[$prop->name] as $index => $value) {
                        if (gettype($value) == 'object') {
                            $data[$prop->name][$index] = self::serialize($value);
                        }
                    }
                }
            }
        }

        return $data;
    }

    /**
     *  create object and set fields
     * @param $data
     * @param $class
     * @return array|bool
     */
    public static function createEntities($data, $class)
    {
        if (!$data) {
            return false;
        }
        $entities = array();
        foreach ($data as $row)
        {
            $reflection = new \ReflectionClass($class);
            $entity = $reflection->newInstance();
            foreach ($row as $prop => $value) {

                $entity->{'set'.$prop}($value);
            }
            $entities[] = $entity;
        }

        return $entities;
    }
}

