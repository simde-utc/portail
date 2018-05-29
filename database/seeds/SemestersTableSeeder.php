<?php

use Illuminate\Database\Seeder;
use App\Models\Semester;

class SemestersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
		$beginingMonths = config('semester.begining_at');

		for ($startingYear = 11; $startingYear < date('y'); $startingYear) {
			foreach ($beginingMonths as $beginingMonth) {
				if (isset($beforeMonth) && $beforeMonth > $beginingMonth['month'])
					$startingYear++;

				Semester::createASemester($startingYear, $beginingMonth['month']);
				$beforeMonth = $beginingMonth['month'];
			}
		}
    }
}
