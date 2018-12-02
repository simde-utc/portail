<?php
/**
 * Ajoute au controlleur un accès aux semestres.
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
     * Récupère le semestre sépcifié.
     *
     * @param  string $semester_id
     * @return Semester
     */
    protected function getSemester(string $semester_id=null)
    {
        if ($semester_id) {
            if ($semester_id !== 'current') {
                return Semester::getSemester($semester_id);
            }
        }

        return Semester::getThisSemester();
    }
}
