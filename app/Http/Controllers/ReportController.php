<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use Maatwebsite\Excel\Facades\Excel;


class ReportController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request) {
        $stime=microtime(true);
        $start = $request->get('start', date('Y-m-d', strtotime('-3 month')));
        $end = $request->get('end', date('Y-m-d'));
        $code = trim($request->get('code', ''));
        $name = trim($request->get('name', ''));

        $q = [
            'start' => $start,
            'end' => $end,
            'code' => $code,
            'name' => $name,
        ];

        $builder = DB::connection('oracle')->table('jzpm_mt_cplan_b')
            ->leftJoin('jzpm_mt_cplan', 'jzpm_mt_cplan_b.pk_mt_cplan', '=', 'jzpm_mt_cplan.pk_mt_cplan')
            ->leftJoin('bd_invbasdoc', 'jzpm_mt_cplan_b.pk_invbasdoc', '=', 'bd_invbasdoc.pk_invbasdoc')
            ->leftJoin('bd_cubasdoc', 'jzpm_mt_cplan_b.pk_cubasdoc', '=', 'bd_cubasdoc.pk_cubasdoc')
            ->select('jzpm_mt_cplan_b.pk_mt_cplan', 'bd_invbasdoc.invcode', 'bd_invbasdoc.invname', 'bd_invbasdoc.invspec', 'jzpm_mt_cplan_b.nnum', 'jzpm_mt_cplan_b.nprice', 'jzpm_mt_cplan_b.vdef1', 'bd_cubasdoc.custname', 'jzpm_mt_cplan.tbilltime', 'jzpm_mt_cplan_b.vdef3')
            ->whereRaw('nvl(jzpm_mt_cplan_b.dr, 0) = 0')
            ->whereRaw("substr(jzpm_mt_cplan.tbilltime, 1, 10)>='$start'")
            ->whereRaw("substr(jzpm_mt_cplan.tbilltime, 1, 10)<='$end'");

        if ($code !== '') {
            $builder->where('bd_invbasdoc.invcode', 'like', "%$code%");
        }

        if ($name !== '') {
            $builder->where('bd_invbasdoc.invname', 'like', "%$name%");
        }

        $data = $builder->orderBy('jzpm_mt_cplan.tbilltime', 'desc')
            ->paginate(100);
        $etime=microtime(true);
        $tips = null;
        $warning = null;
        if ($request->get('page') == null) {
            $tips = "用时间 " . round(($etime-$stime), 3) . " 秒为您检索出 " . $data->total() ." 条数据";
        }
        if ($data->total() >= 10000) {
            $warning = [
                'msg' => '数据大于1w条，请缩小查询范围后导出Excel'
            ];
        }
        return view('report', ['rows' => $data, 'q' => $q, 'tips' => $tips, 'warning' => $warning]);
    }

    public function export(Request $request){
        $start = $request->get('start', date('Y-m-d', strtotime('-3 month')));
        $end = $request->get('end', date('Y-m-d'));
        $code = trim($request->get('code', ''));
        $name = trim($request->get('name', ''));

        $builder = DB::connection('oracle')->table('jzpm_mt_cplan_b')
            ->leftJoin('jzpm_mt_cplan', 'jzpm_mt_cplan_b.pk_mt_cplan', '=', 'jzpm_mt_cplan.pk_mt_cplan')
            ->leftJoin('bd_invbasdoc', 'jzpm_mt_cplan_b.pk_invbasdoc', '=', 'bd_invbasdoc.pk_invbasdoc')
            ->leftJoin('bd_cubasdoc', 'jzpm_mt_cplan_b.pk_cubasdoc', '=', 'bd_cubasdoc.pk_cubasdoc')
            ->select('jzpm_mt_cplan_b.pk_mt_cplan', 'bd_invbasdoc.invcode', 'bd_invbasdoc.invname', 'bd_invbasdoc.invspec', 'jzpm_mt_cplan_b.nnum', 'jzpm_mt_cplan_b.nprice', 'jzpm_mt_cplan_b.vdef1', 'bd_cubasdoc.custname', 'jzpm_mt_cplan.tbilltime', 'jzpm_mt_cplan_b.vdef3')
            ->whereRaw('nvl(jzpm_mt_cplan_b.dr, 0) = 0')
            ->whereRaw("substr(jzpm_mt_cplan.tbilltime, 1, 10)>='$start'")
            ->whereRaw("substr(jzpm_mt_cplan.tbilltime, 1, 10)<='$end'");

        if ($code !== '') {
            $builder->where('bd_invbasdoc.invcode', 'like', "%$code%");
        }

        if ($name !== '') {
            $builder->where('bd_invbasdoc.invname', 'like', "%$name%");
        }

        $query = $builder->orderBy('jzpm_mt_cplan.tbilltime', 'desc');

        Excel::create("XYJH-$start-$end",function($excel) use ($query){
            $excel->sheet('需用计划', function($sheet) use ($query){
                $query->chunk(1000, function($data) use ($sheet) {
                    $sheet->fromArray(json_decode(json_encode($data), true), null, 'A1', false, false);
                });
                $sheet->prependRow(array(
                    '', 'NO', '物资编码', '物资名称', '规格', '申报数量', '单价', '添加剂', '供应商', '核定时间', '备注'
                ));
            });
        })->export('xls');
    }
}
