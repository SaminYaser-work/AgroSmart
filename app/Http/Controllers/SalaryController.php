<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\Salary;

class SalaryController extends Controller
{
    public function getSalaryReportIndividual($worker_id)
    {
        return Attendance::query()
            ->select([
                'date',
                \DB::raw('@hours_worked := TIMESTAMPDIFF(HOUR, time_in, time_out) as hours_worked,
                       @diff := @hours_worked - workers.expected_hours as diff,
                       IF(time_in IS NULL OR time_out IS NULL, @base := 0, @base := ROUND(workers.salary * workers.expected_hours, 2)) AS base,
                       IF(@diff > 0, @ot := ROUND(workers.over_time_rate * @diff, 2), @ot:=0) AS overtime,
                       IF(@diff < 0, @pen := ROUND(workers.salary * @diff, 2) * -1, @pen := 0) AS penalty,
                       @total := ROUND(@base + @ot - @pen, 2) AS total')
            ])
            ->join('workers', 'attendances.worker_id', '=', 'workers.id')
            ->where('attendances.worker_id', $worker_id);
    }

    public function getSalaryReportIndividualWithSum($worker_id)
    {
        $data = $this->getSalaryReportIndividual($worker_id)->get();
        $agg = $data->groupBy('worker_id')->sum('base')->sum('overtime')->sum('penalty')->sum('total')->get();
    }

    public function getSalaryMonthly($farm_id)
    {
        return Attendance::query()
            ->select([
                'date',
                \DB::raw('@hours_worked := TIMESTAMPDIFF(HOUR, time_in, time_out) as hours_worked,
                       @diff := @hours_worked - workers.expected_hours as diff,
                       IF(time_in IS NULL OR time_out IS NULL, @base := 0, @base := ROUND(workers.salary * workers.expected_hours, 2)) AS base,
                       IF(@diff > 0, @ot := ROUND(workers.over_time_rate * @diff, 2), @ot:=0) AS overtime,
                       IF(@diff < 0, @pen := ROUND(workers.salary * @diff, 2) * -1, @pen := 0) AS penalty,
                       @total := ROUND(@base + @ot - @pen, 2) AS total')
            ])
            ->join('workers', 'attendances.worker_id', '=', 'workers.id')
            ->where('workers.farm_id', $farm_id)
            ->groupBy('attendances.date');
    }

    public function TotalSalaryByMonth() {
        return Salary::query()
            ->select([
                'month',
                \DB::raw('SUM(total) as total')
            ])
            ->groupBy('month');
    }

    public function getSalaryDue()
    {
        return Salary::query()->where('paid', false)->sum('total');
    }

    public function getTotalSalaryPaid()
    {
        return Salary::query()->where('paid', true)->sum('total');
    }
}
