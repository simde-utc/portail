<?php
/**
 * Modèle correspondant aux semestres.
 *
 * @author Samy Nastuzzi <samy@nastuzzi.fr>
 *
 * @copyright Copyright (c) 2018, SiMDE-UTC
 * @license GNU GPL-3.0
 */

namespace App\Models;

class Semester extends Model
{
    protected $fillable = [
        'name', 'is_spring', 'year', 'begin_at', 'end_at',
    ];

    protected $dates = [
        'begin_at', 'end_at'
    ];

    protected $cast = [
        'is_spring' => 'boolean',
        'year' => 'char',
    ];

    protected $must = [
        'is_spring', 'begin_at', 'end_at',
    ];

    protected $selection = [
        'paginate' => [],
        'order' => [],
        'filter' => [],
    ];

    /**
     * Permet de récupérer un semestre en fonction de son id et de son nom.
     *
     * @param  string $semester_id
     * @return Semester|null
     */
    public static function getSemester(string $semester_id=null)
    {
        return static::where('id', $semester_id)->orWhere('name', $semester_id)->firstOrFail();
    }

    /**
     * Permet de récupérer le semestre courant.
     *
     * @param  string $currentYear
     * @param  string $currentMonth
     * @param  string $currentDay
     * @return Semester
     */
    public static function getThisSemester(string $currentYear=null, string $currentMonth=null, string $currentDay=null)
    {
        $currentYear = $currentYear ?? date('y');
        $currentMonth = $currentMonth ?? date('m');
        $currentDay = $currentDay ?? date('d');

        $currentDate = $currentYear.'-'.$currentMonth.'-'.$currentDay;

        $semester = self::whereDate('begin_at', '<=', $currentDate)
          ->whereDate('end_at', '>=', $currentDate)
          ->first();

        if ($semester === null) {
            $semester_id = self::createASemester($currentYear, $currentMonth);

            return $semester_id === null ? null : self::find($semester_id);
        }

        return $semester;
    }

    /**
     * Permet de récupérer les semestres de cette année scolaire.
     *
     * @param  string $currentYear
     * @param  string $currentMonth
     * @param  string $currentDay
     * @return array
     */
    public static function getThisYear(string $currentYear=null, string $currentMonth=null, string $currentDay=null)
    {
        $config = config('semester');
        $currentYear = $currentYear ?? date('y');
        $currentMonth = $currentMonth ?? date('m');
        $currentDay = $currentDay ?? date('d');

        $firstBegin = $config['begin_at'][0];
        $firstEnd = $config['end_at'][0];

        if (!static::isInTheSemester($firstBegin['month'], $firstEnd['month'], $currentMonth)) {
            if ($currentMonth <= $firstBegin['month']) {
                $currentYear--;
            }

            return static::getThisYear($currentYear, $firstBegin['month'], $firstBegin['day']);
        }

        $semesters = [];

        foreach ($config['begin_at'] as $key => $semester) {
            $semesters[] = static::getThisSemester($currentYear, $semester['month'], $semester['day']);

            if ($semester['month'] > $config['end_at'][$key]['month']) {
                $currentYear++;
            }
        }

        return $semesters;
    }

    /**
     * Création d'un semestre.
     *
     * @param  integer|string $currentYear
     * @param  string         $currentMonth
     * @return Semester|null
     */
    public static function createASemester($currentYear=null, string $currentMonth=null)
    {
        $config = config('semester');
        $currentYear = (int) ($currentYear ?? date('y'));
        $currentMonth = ($currentMonth ?? date('m'));

        foreach ($config['begin_at'] as $key => $value) {
            $beginingMonth = $value['month'];
            $endingMonth = $config['end_at'][$key]['month'];

            if (self::isInTheSemester($beginingMonth, $endingMonth, $currentMonth)) {
                if ($beginingMonth > $endingMonth && $currentMonth <= $endingMonth) {
                    $currentYear -= 1;
                }

                $thisSemester = static::where('name', ($config['name'][$key]).$currentYear)->first();

                if ($thisSemester === null) {
                    $begin = $currentYear.'-'.($config['begin_at'][$key]['month']).'-';
                    $begin .= ($config['begin_at'][$key]['day']).'-'.($config['begin_at'][$key]['time']);
                    $end = ($beginingMonth > $endingMonth ? ($currentYear + 1) : $currentYear).'-';
                    $end .= ($config['end_at'][$key]['month']).'-'.($config['end_at'][$key]['day']);
                    $end .= '-'.($config['end_at'][$key]['time']);

                    return self::create([
                        'name' => ($config['name'][$key]).$currentYear,
                        'is_spring' => $key,
                        'year' => $currentYear,
                        'begin_at' => $begin,
                        'end_at' => $end,
                    ])->id;
                } else {
                    return $thisSemester->id;
                }
            }
        }
    }

    /**
     * Indique si le mois courant est dans le semestre.
     *
     * @param  string $beginingMonth
     * @param  string $endingMonth
     * @param  string $currentMonth
     * @return boolean
     */
    protected static function isInTheSemester(string $beginingMonth, string $endingMonth, string $currentMonth)
    {
        return ($beginingMonth <= $endingMonth && $beginingMonth <= $currentMonth && $currentMonth <= $endingMonth)
            || ($beginingMonth > $endingMonth && $beginingMonth <= $currentMonth && $currentMonth <= 12)
            || ($beginingMonth > $endingMonth && 0 <= $currentMonth && $currentMonth <= $endingMonth);
    }
}
