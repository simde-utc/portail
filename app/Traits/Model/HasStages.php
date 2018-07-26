<?php

namespace App\Traits\Model;
use App\Exceptions\PortailException;

trait HasStages
{
    public static function getTopStage(array $data = [], $with = [], Callable $callback = null) {
        $tableName = (new static)->getTable();
        $model = static::whereNull('parent_id')->with($with);

		foreach ($data as $key => $value) {
            if (!\Schema::hasColumn($tableName, $key))
                throw new PortailException('L\'attribut "'.$key.'" n\'existe pas');

            $model = $model->where($key, $value);
        }

        $collection = $model->get();

        if ($callback)
            $collection = $callback($collection) ?? $collection;

		return $collection;
    }

    public static function getStage(int $stage, array $data = [], $with = [], Callable $callback = null) {
        $tableName = (new static)->getTable();
        $collection = static::getTopStage($data, $with, $callback);

		for ($i = 0; $i < $stage; $i++) {
			$before = $collection;
			$collection = collect();

			foreach ($before as $model) {
				$children = $model->children()->with($with);

        		foreach ($data as $key => $value) {
                    if (!\Schema::hasColumn($tableName, $key))
                        throw new PortailException('L\'attribut "'.$key.'" n\'existe pas');

                    $children = $children->where($key, $value);
                }

                $children = $children->get();
                if ($callback)
                    $children = $callback($children) ?? $children;

				$collection = $collection->merge($children);
			}
		}

		return $collection;
	}

	public static function getStages(int $from = null, int $to = null, array $data = [], $with = [], Callable $callback = null) {
        $tableName = (new static)->getTable();
        $collection = static::getStage($from ?? 0, $data, $with, $callback);
		$toAdd = $collection;

		for ($i = $from ?? 0; is_null($to) || $i < $to; $i++) {
			$toAddChildren = $toAdd;
			$toAdd = collect();

            if (count($toAddChildren) === 0)
                break;

			foreach ($toAddChildren as $model) {
				$children = $model->children()->with($with);

                foreach ($data as $key => $value) {
                    if (!\Schema::hasColumn($tableName, $key))
                        throw new PortailException('L\'attribut "'.$key.'" n\'existe pas');

                    $children = $children->where($key, $value);
                }

                $children = $children->get();
                if ($callback)
                    $children = $callback($children) ?? $children;

				$model->children = $children;
				$toAdd = $toAdd->merge($model->children);
			}
		}

		return $collection;
	}
}
