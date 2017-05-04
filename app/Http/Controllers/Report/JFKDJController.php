<?php

namespace App\Http\Controllers\Report;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;
use Maatwebsite\Excel\Facades\Excel;


/**
 * Class JFKDJController
 * @package App\Http\Controllers\Report
 * 质量安全--奖罚款登记
 */
class JFKDJController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
    }

    private function _createBuilder(array $query) {
        $_builder = DB::connection('oracle')->table('JZPM_QA_PRIZPUNS_B')
            ->leftJoin('JZPM_QA_PRIZPUNISH', 'JZPM_QA_PRIZPUNISH.PK_PRIZPUNISH', '=', 'JZPM_QA_PRIZPUNS_B.PK_PRIZPUNISH')
            ->leftJoin('FDC_BD_PROJECT', 'FDC_BD_PROJECT.PK_PROJECT', '=', 'JZPM_QA_PRIZPUNISH.PK_PROJECT')
            ->select('JZPM_QA_PRIZPUNISH.VBILLNO', 'FDC_BD_PROJECT.VNAME', 'JZPM_QA_PRIZPUNS_B.VPSNNAME', 'JZPM_QA_PRIZPUNISH.VEVENTDESC', 'JZPM_QA_PRIZPUNS_B.NMNY', 'JZPM_QA_PRIZPUNISH.DBILLDATE', 'JZPM_QA_PRIZPUNISH.DDATE', 'JZPM_QA_PRIZPUNISH.PK_PSNDOC', 'JZPM_QA_PRIZPUNISH.VMENO')
            ->whereRaw('nvl(JZPM_QA_PRIZPUNS_B.DR, 0) = 0 AND JZPM_QA_PRIZPUNISH.VBILLSTATUS = 1')
            ->whereRaw("substr(JZPM_QA_PRIZPUNISH.DBILLDATE, 1, 10)>=?", $query['start'])
            ->whereRaw("substr(JZPM_QA_PRIZPUNISH.DBILLDATE, 1, 10)<=?", $query['end']);
        return $_builder;
    }

    public function index(Request $request) {
        $stime=microtime(true);
        $start = $request->get('start', date('Y-m-d', strtotime('-3 month')));
        $end = $request->get('end', date('Y-m-d'));

        $q = [
            'start' => $start,
            'end' => $end,
            'display' => 'default'
        ];

        $builder = $this->_createBuilder($q);

        $data = $builder->orderBy('JZPM_QA_PRIZPUNISH.VBILLNO', 'desc')
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
        return view('report.jfkdj', ['rows' => $data, 'q' => $q, 'tips' => $tips, 'warning' => $warning]);
    }

    public function statistics(Request $request) {

    }

    public function export(Request $request){
        $start = $request->get('start', date('Y-m-d', strtotime('-3 month')));
        $end = $request->get('end', date('Y-m-d'));

        $q = [
            'start' => $start,
            'end' => $end,
            'display' => 'none'
        ];

        $builder = $this->_createBuilder($q);

        $query = $builder->orderBy('JZPM_QA_PRIZPUNISH.VBILLNO', 'desc');

        Excel::create("JFKDJ-$start-$end",function($excel) use ($query){
            $excel->sheet('罚款登记', function($sheet) use ($query){
                $query->chunk(1000, function($data) use ($sheet) {
                    $sheet->fromArray(json_decode(json_encode($data), true), null, 'A1', false, false);
                });
                $sheet->prependRow(array(
                    '序号', '单号', '项目部', '被罚款人', '处罚原因', '处罚金额', '处罚日期', '上交日期', '开单人', '备注'
                ));
            });
        })->export('xls');
    }
}
