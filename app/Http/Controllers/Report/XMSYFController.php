<?php

namespace App\Http\Controllers\Report;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;
use Maatwebsite\Excel\Facades\Excel;


class XMSYFController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request) {
        $stime=microtime(true);
        $start = $request->get('start', date('Y-m-d', strtotime('-3 month')));
        $end = $request->get('end', date('Y-m-d'));
//        $code = trim($request->get('code', ''));
//        $name = trim($request->get('name', ''));

        $q = [
            'start' => $start,
            'end' => $end,
//            'code' => $code,
//            'name' => $name,
        ];

        $builder = DB::connection('oracle')->table('JZPM_PC_FACTBILLL_B')
            ->leftJoin('JZPM_PC_FACTBILL', 'JZPM_PC_FACTBILL.PK_FACTBILL', '=', 'JZPM_PC_FACTBILLL_B.PK_FACTBILL')
            ->leftJoin('FDC_BD_PROJECT', 'FDC_BD_PROJECT.PK_PROJECT', '=', 'JZPM_PC_FACTBILL.PK_PROJECT')
            ->select('FDC_BD_PROJECT.VNAME', 'JZPM_PC_FACTBILL.DBUSIDATE', 'JZPM_PC_FACTBILLL_B.NFEEBASEMNY', 'JZPM_PC_FACTBILLL_B.VDEF1', 'JZPM_PC_FACTBILLL_B.VDEF2', 'JZPM_PC_FACTBILLL_B.VMOME', 'JZPM_PC_FACTBILL.VBILLSTATUS')
            ->whereRaw('nvl(JZPM_PC_FACTBILLL_B.DR, 0) = 0 AND JZPM_PC_FACTBILL.VBILLSTATUS = 1')
            ->whereRaw("substr(JZPM_PC_FACTBILL.DBUSIDATE, 1, 10)>='$start'")
            ->whereRaw("substr(JZPM_PC_FACTBILL.DBUSIDATE, 1, 10)<='$end'");

//        if ($code !== '') {
//            $builder->where('bd_invbasdoc.invcode', 'like', "%$code%");
//        }
//
//        if ($name !== '') {
//            $builder->where('bd_invbasdoc.invname', 'like', "%$name%");
//        }

        $data = $builder->orderBy('JZPM_PC_FACTBILL.DBUSIDATE', 'desc')
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
        return view('report.xmsyf', ['rows' => $data, 'q' => $q, 'tips' => $tips, 'warning' => $warning]);
    }

    public function export(Request $request){
        $start = $request->get('start', date('Y-m-d', strtotime('-3 month')));
        $end = $request->get('end', date('Y-m-d'));
//        $code = trim($request->get('code', ''));
//        $name = trim($request->get('name', ''));

        $builder = DB::connection('oracle')->table('JZPM_PC_FACTBILLL_B')
            ->leftJoin('JZPM_PC_FACTBILL', 'JZPM_PC_FACTBILL.PK_FACTBILL', '=', 'JZPM_PC_FACTBILLL_B.PK_FACTBILL')
            ->leftJoin('FDC_BD_PROJECT', 'FDC_BD_PROJECT.PK_PROJECT', '=', 'JZPM_PC_FACTBILL.PK_PROJECT')
            ->select('FDC_BD_PROJECT.VNAME', 'JZPM_PC_FACTBILL.DBUSIDATE', 'JZPM_PC_FACTBILLL_B.NFEEBASEMNY', 'JZPM_PC_FACTBILLL_B.VDEF1', 'JZPM_PC_FACTBILLL_B.VDEF2', 'JZPM_PC_FACTBILLL_B.VMOME')
            ->whereRaw('nvl(JZPM_PC_FACTBILLL_B.DR, 0) = 0 AND JZPM_PC_FACTBILL.VBILLSTATUS = 1')
            ->whereRaw("substr(JZPM_PC_FACTBILL.DBUSIDATE, 1, 10)>='$start'")
            ->whereRaw("substr(JZPM_PC_FACTBILL.DBUSIDATE, 1, 10)<='$end'");

//        if ($code !== '') {
//            $builder->where('bd_invbasdoc.invcode', 'like', "%$code%");
//        }
//
//        if ($name !== '') {
//            $builder->where('bd_invbasdoc.invname', 'like', "%$name%");
//        }

        $query = $builder->orderBy('JZPM_PC_FACTBILL.DBUSIDATE', 'desc');

        Excel::create("XMSYF-$start-$end",function($excel) use ($query){
            $excel->sheet('项目试验费', function($sheet) use ($query){
                $query->chunk(1000, function($data) use ($sheet) {
                    $sheet->fromArray(json_decode(json_encode($data), true), null, 'A1', false, false);
                });
                $sheet->prependRow(array(
                    '序号', '项目名称', '申请日期', '金额（元）', '支付范围（时间）', '试验室名称', '备注'
                ));
            });
        })->export('xls');
    }
}
