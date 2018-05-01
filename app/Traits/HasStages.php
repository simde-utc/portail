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
                throw new PortailException('L\'attribut '.$key.' n\'existe pas');

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
				$childs = $model->childs()->with($with);

        		foreach ($data as $key => $value) {
                    if (!\Schema::hasColumn($tableName, $key))
                        throw new PortailException('L\'attribut '.$key.' n\'existe pas');

                    $childs = $childs->where($key, $value);
                }

				$collection = $collection->merge($childs->get());
			}
		}

		return $collection;
	}

	public static function getStages(int $from = null, int $to = null, array $data = [], $with = []) {
        $tableName = (new static)->getTable();
        $collection = static::getStage($from ?? 0, $data, $with);
		$toAdd = $collection;

		for ($i = $from ?? 0; is_null($to) || $i < $to; $i++) {
			$toAddChilds = $toAdd;
			$toAdd = collect();

            if (count($toAddChilds) === 0)
                break;

			foreach ($toAddChilds as $model) {
				$childs = $model->childs()->with($with);

                foreach ($data as $key => $value) {
                    if (!\Schema::hasColumn($tableName, $key))
                        throw new PortailException('L\'attribut '.$key.' n\'existe pas');

                    $childs = $childs->where($key, $value);
                }

				$model->childs = $childs->get();
				$toAdd = $toAdd->merge($model->childs);
			}
		}

		return $collection;
	}
}
