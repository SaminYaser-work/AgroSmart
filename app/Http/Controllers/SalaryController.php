<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\Salary;

class SalaryController extends Controller
{
    public function getSalaryReportIndividual($worker_id)
    {
//        return Attendance::query()
//            ->select([
//                'date',
//                \DB::raw('@hours_worked := TIMESTAMPDIFF(HOUR, time_in, time_out) as hours_worked,
//                       @diff := @hours_worked - workers.expected_hours as diff,
//                       IF(time_in IS NULL OR time_out IS NULL, @base := 0, @base := ROUND(workers.salary * workers.expected_hours, 2)) AS base,
//                       IF(@diff > 0, @ot := ROUND(workers.over_time_rate * @diff, 2), @ot:=0) AS overtime,
//                       IF(@diff < 0, @pen := ROUND(workers.salary * @diff, 2) * -1, @pen := 0) AS penalty,
//                       @total := ROUND(@base + @ot - @pen, 2) AS total')
//            ])
//            ->join('workers', 'attendances.worker_id', '=', 'workers.id')
//            ->where('attendances.worker_id', $worker_id);


        $subquery = \DB::table('attendances')
            ->selectRaw("date,
        strftime('%H', time_out) - strftime('%H', time_in) as hours_worked,
        CASE WHEN time_in IS NULL OR time_out IS NULL THEN 0 ELSE ROUND(workers.salary * workers.expected_hours, 2) END AS base,
        CASE WHEN (strftime('%H', time_out) - strftime('%H', time_in)) > workers.expected_hours THEN ROUND(workers.over_time_rate * ((strftime('%H', time_out) - strftime('%H', time_in)) - workers.expected_hours), 2) ELSE 0 END AS overtime,
        CASE WHEN (strftime('%H', time_out) - strftime('%H', time_in)) < workers.expected_hours THEN ROUND(workers.salary * ((strftime('%H', time_out) - strftime('%H', time_in)) - workers.expected_hours), 2) * -1 ELSE 0 END AS penalty")
            ->leftJoin('workers', 'attendances.worker_id', '=', 'workers.id')
            ->where('attendances.worker_id', $worker_id);

        return Attendance::query()
            ->fromRaw("({$subquery->toSql()}) as subquery")
            ->mergeBindings($subquery)
            ->selectRaw("date, SUM(COALESCE(base, 0) + COALESCE(overtime, 0) - COALESCE(penalty, 0)) AS total, hours_worked, base, overtime, penalty")
            ->groupBy('date');
    }


    public function getSalaryDue()
    {
        return Salary::query()->where('paid', false)->sum('total');
    }

    public function getSalaryDueOfFarm(int $farm_id)
    {
        return Salary::query()
            ->whereFarmId($farm_id)
            ->wherePaid(false)
            ->sum('total');
    }

    public function getSalaryOfFarmInADay(int $farm_id, string $date)
    {
        return Salary::query()
            ->whereFarmId($farm_id)
            ->where('date','=', $date)
            ->sum('total');
    }

    public function getTotalSalaryPaid()
    {
        return Salary::query()->where('paid', true)->sum('total');
    }
}
