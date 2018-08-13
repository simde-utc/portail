<?php

namespace App\Traits\Model;

use Illuminate\Database\Eloquent\Builder;
use App\Exceptions\PortailException;
use Spatie\BinaryUuid\HasBinaryUuid as OriginalHasBinaryUuid;

trait HasBinaryUuid
{
	use OriginalHasBinaryUuid;

	public function getKeyName() {
		return 'id';
	}

	public function getUuidAttribute() {
		return $this->getUuidTextAttribute();
	}
}
