<?php

namespace App\Traits;
use App\Exceptions\PortailException;

trait HasStages
{
    public static function getTopStage(array $data = [], $with = []) {
        $tableName = (new static)->getTable();
        $model = static::whereNull('parent_id')->with($with);

		foreach ($data as $key => $value) {
            if (!\Schema::hasColumn($tableName, $key))
                throw new PortailException('L\'attribut "'.$key.'" n\'existe pas');

            $model = $model->where($key, $value);
        }

		return $model->get();
    }

    public static function getStage(int $stage, array $data = [], $with = []) {
        $tableName = (new static)->getTable();
        $collection = static::getTopStage($data, $with);

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

				$collection = $collection->merge($children->get());
			}
		}

		return $collection;
	}

	public static function getStages(int $from = null, int $to = null, array $data = [], $with = []) {
        $tableName = (new static)->getTable();
        $collection = static::getStage($from ?? 0, $data, $with);
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

				$model->children = $children->get();
				$toAdd = $toAdd->merge($model->children);
			}
		}

		return $collection;
	}
}
