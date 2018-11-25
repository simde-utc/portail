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
        'name', 'is_spring', 'year', 'begining_at', 'ending_at',
    ];

    protected $cast = [
        'is_spring' => 'boolean',
        'year' => 'char',
    ];

    protected $must = [
        'begining_at', 'ending_at',
    ];

    protected $selection = [
        'paginate' => [],
        'order' => [],
        'filter' => [],
    ];

    /**
     * Permet de récupérer un semestre en fonction de son id et de son nom.
     *
     * @param  string $semester
     * @return Semester|null
     */
    public static function getSemester(string $semester)
    {
        return static::where('id', $semester)->orWhere('name', $semester)->first();
    }

    /**
     * Permet de récupére le semestre courant.
     *
     * @param  string $currentYear
     * @param  string $currentMonth
     * @param  string $currentDay
     * @return Semester
     */
    public static function getThisSemester(string $currentYear=null, string $currentMonth=null, string $currentDay=null)
    {
        if ($currentYear === null) {
            $currentYear = date('y');
        }

        if ($currentMonth === null) {
            $currentMonth = date('m');
        }

        if ($currentDay === null) {
            $currentDay = date('d');
        }

        $currentDate = $currentYear.'-'.$currentMonth.'-'.$currentDay;

        $semester = self::whereDate('begining_at', '<=', $currentDate)
          ->whereDate('ending_at', '>=', $currentDate)
          ->first();

        if ($semester === null) {
            $semester_id = self::createASemester($currentYear, $currentMonth);

            return $semester_id === null ? null : self::find($semester_id);
        }

        return $semester;
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

        foreach ($config['begining_at'] as $key => $value) {
            $beginingMonth = $value['month'];
            $endingMonth = $config['ending_at'][$key]['month'];

            if (self::needToBeGenerated($beginingMonth, $endingMonth, $currentMonth)) {
                if ($beginingMonth > $endingMonth && $currentMonth <= $endingMonth) {
                    $currentYear -= 1;
                }

                $thisSemester = static::where('name', ($config['name'][$key]).$currentYear)->first();

                if ($thisSemester === null) {
                    $begin = $currentYear.'-'.($config['begining_at'][$key]['month']).'-';
                    $begin .= ($config['begining_at'][$key]['day']).'-'.($config['begining_at'][$key]['time']);
                    $end = ($beginingMonth > $endingMonth ? ($currentYear + 1) : $currentYear).'-';
                    $end .= ($config['ending_at'][$key]['month']).'-'.($config['ending_at'][$key]['day']);
                    $end .= '-'.($config['ending_at'][$key]['time']);

                    return self::create([
                        'name' => ($config['name'][$key]).$currentYear,
                        'is_spring' => $key,
                        'year' => $currentYear,
                        'begining_at' => $begin,
                        'ending_at' => $end,
                    ])->id;
                } else {
                    return $thisSemester->id;
                }
            }
        }
    }

    /**
     * Indique s'il est nécessaire de générer le semestre ou non en fonction du mois courant.
     *
     * @param  string $beginingMonth
     * @param  string $endingMonth
     * @param  string $currentMonth
     * @return boolean
     */
    protected static function needToBeGenerated(string $beginingMonth, string $endingMonth, string $currentMonth)
    {
        return ($beginingMonth <= $endingMonth && $beginingMonth <= $currentMonth && $currentMonth <= $endingMonth)
            || ($beginingMonth > $endingMonth && $beginingMonth <= $currentMonth && $currentMonth <= 12)
            || ($beginingMonth > $endingMonth && 0 <= $currentMonth && $currentMonth <= $endingMonth);
    }
}
