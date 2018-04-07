<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Semester extends Model
{
    protected $fillable = [
        'name', 'is_spring', 'year', 'begining_at', 'ending_at',
    ];

    protected $cast = [
        'is_spring' => 'boolean',
        'year' => 'char',
    ];

    public static function getThisSemester($currentYear = null, $currentMonth = null, $currentDay = null) {
        if ($currentYear === null)
            $currentYear = date('y');
        if ($currentMonth === null)
            $currentMonth = date('m');
        if ($currentDay === null)
            $currentDay = date('d');

        $currentDate = $currentYear.'-'.$currentMonth.'-'.$currentDay;

        $semester = self::whereDate('begining_at', '<=', $currentDate)
          ->whereDate('ending_at', '>=', $currentDate)
          ->get()->first();

        if ($semester === null) {
            $id = self::createASemester($currentYear, $currentMonth);

            return $id === null ? null : self::find($id);
        }

        return $semester;
    }

    public static function getThisYear($currentYear = null) {
        $config = config('semester');
        $year = [];

        if ($currentYear === null)
            $currentYear = date('y');

        foreach ($config['begining_at'] as $key => $value)
            array_push($year, self::getThisSemester(($config['ending_at'][$key]['month'] < $value['month'] ? ($currentYear - 1) : $currentYear), $value['month'], $value['day']));

        return $year;
    }

    public static function createASemester($currentYear = null, $currentMonth = null) {
        $config = config('semester');

        if ($currentYear === null)
            $currentYear = date('y');
        if ($currentMonth === null)
            $currentMonth = date('m');

        foreach ($config['begining_at'] as $key => $value) {
            $beginingMonth = $value['month'];
            $endingMonth = $config['ending_at'][$key]['month'];

            if (($beginingMonth <= $endingMonth && $beginingMonth <= $currentMonth && $currentMonth <= $endingMonth) ||
              ($beginingMonth > $endingMonth && $beginingMonth <= $currentMonth && $currentMonth <= 12) ||
              ($beginingMonth > $endingMonth && 0 <= $currentMonth && $currentMonth <= $endingMonth)) {
                if ($beginingMonth > $endingMonth && $currentMonth <= $endingMonth)
                    $currentYear -= 1;

                $thisSemester = self::where('name', ($config['name'][$key]).$currentYear)->get()->first();

                if ($thisSemester === null)
                    return self::create([
                        'name' => ($config['name'][$key]).$currentYear,
                        'is_spring' => $key,
                        'year' => $currentYear,
                        'begining_at' => $currentYear.'-'.($config['begining_at'][$key]['month']).'-'.($config['begining_at'][$key]['day']).'-'.($config['begining_at'][$key]['time']),
                        'ending_at' => ($beginingMonth > $endingMonth ? ($currentYear + 1) : $currentYear).'-'.($config['ending_at'][$key]['month']).'-'.($config['ending_at'][$key]['day']).'-'.($config['ending_at'][$key]['time']),
                    ])->id;
                else
                    return $thisSemester->id;
            }
        }
    }

    public function assoMember() {
        return $this->hasMany('App\Models\AssoMember');
    }

    public function currentAssos() {
        return $this->belongsToMany('App\Models\Asso', 'assos_members');
    }

    public function currentMembers() {
        return $this->belongsToMany('App\Models\User', 'assos_members');
    }
}
