<?php
/**
 * Add to the controller an access to Semesters.
 *
 * @author Samy Nastuzzi <samy@nastuzzi.fr>
 *
 * @copyright Copyright (c) 2018, SiMDE-UTC
 * @license GNU GPL-3.0
 */

namespace App\Traits\Controller\v1;

use App\Models\Semester;

trait HasSemesters
{
    /**
     * Retrieve the specified user.
     *
     * @param  string  $semester_id
     * @param  boolean $hideData
     * @return Semester
     */
    protected function getSemester(string $semester_id=null, bool $hideData=false)
    {
        if ($semester_id && $semester_id !== 'current') {
            $semester = Semester::getSemester($semester_id);
        } else {
            $semester = Semester::getThisSemester();
        }

        if ($hideData) {
            return $semester->hideData();
        }

        return $semester;

    }
}
