<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Antrian;

class ReportController extends Controller
{
    public function timeframe(Request $request)
    {
        $model = \DB::select("
            select 
            SUM(IF(a.type =1 && DATE_FORMAT(a.created_at,'%Y-%m-%d')='2022-04-09', 1,0)) as prioritas09,
            SUM(IF(a.type =2 && DATE_FORMAT(a.created_at,'%Y-%m-%d')='2022-04-09', 1,0)) as umum09,
            SUM(IF(a.type =1 && DATE_FORMAT(a.created_at,'%Y-%m-%d')='2022-04-10', 1,0)) as prioritas10,
            SUM(IF(a.type =2 && DATE_FORMAT(a.created_at,'%Y-%m-%d')='2022-04-10', 1,0)) as umum10
            from antrians a
        ");

        return $model;
    }

    public function kategori_antrian(Request $request)
    {
        $model = \DB::select("
            select 
            SUM(IF(a.type =1, 1,0)) as prioritas,
            SUM(IF(a.type =2, 1,0)) as umum
            from antrians a
        ");

        return $model[0];
    }

    public function report_keperluan(Request $request)
    {
        $model = \DB::select("
            select a.id, a.nama,
            (
                select count(*) from antrians b where b.keperluan_id=a.id
            )as jumlah
            from keperluans a
        ");

        return $model;
    }
}
