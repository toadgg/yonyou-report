<?php

namespace App\Http\Controllers\Report;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use DB;
use Maatwebsite\Excel\Facades\Excel;


class CustomerController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
    }

    private function _createBuilder(array $query) {
        $_builder = DB::connection('oracle')->table('BD_CUBASDOC')
            ->leftJoin('BD_AREACL', 'BD_CUBASDOC.PK_AREACL', '=', 'BD_AREACL.PK_AREACL')
            ->leftJoin('SM_USER SM_USER_CREATOR', 'BD_CUBASDOC.CREATOR', '=', 'SM_USER_CREATOR.CUSERID')
            ->leftJoin('SM_USER SM_USER_MODIFIER', 'BD_CUBASDOC.MODIFIER', '=', 'SM_USER_MODIFIER.CUSERID')
            ->selectRaw('BD_CUBASDOC.CUSTCODE, BD_CUBASDOC.CUSTNAME, BD_CUBASDOC.CUSTSHORTNAME, BD_AREACL.AREACLNAME, BD_CUBASDOC.CONADDR,BD_CUBASDOC.PHONE1,BD_CUBASDOC.LINKMAN1, SUBSTR(BD_CUBASDOC.CREATETIME, 1, 10) AS CREATETIME, SM_USER_CREATOR.USER_NAME AS CREATOR, SUBSTR(BD_CUBASDOC.MODIFYTIME, 1, 10) AS MODIFYTIME, SM_USER_MODIFIER.USER_NAME AS MODIFIER')
            ->whereRaw("(substr(BD_CUBASDOC.CREATETIME, 1, 10)>=? and substr(BD_CUBASDOC.CREATETIME, 1, 10)<=?)", [$query['start'], $query['end']]);
        return $_builder;
    }

    public function index(Request $request) {
        $stime=microtime(true);
        $start = $request->get('start', date('Y-m-d', strtotime('-1 month')));
        $end = $request->get('end', date('Y-m-d'));

        $q = [
            'start' => $start,
            'end' => $end,
            'display' => 'default'
        ];

        $builder = $this->_createBuilder($q);

        $data = $builder->orderBy('BD_CUBASDOC.CREATETIME', 'desc')
            ->paginate(100);

        $extend = DB::connection('sqlsrv')->table('TY_XXWHZJKS')
            ->join('TY_XINXIWEIHU', 'TY_XINXIWEIHU.ty_xinxiid', '=', 'TY_XXWHZJKS.ty_xinxiid')
            ->where('TY_XXWHZJKS.keshangname', '!=', '')
            ->whereIn('TY_XXWHZJKS.keshangname', array_column($data->items(), 'custname'))
            ->select('TY_XXWHZJKS.*', 'TY_XINXIWEIHU.name', 'TY_XINXIWEIHU.dept', 'TY_XINXIWEIHU.time', 'TY_XINXIWEIHU.nc', 'TY_XINXIWEIHU.shuoming')
            ->get()
            ->keyBy('keshangname');

        $fwoaExtend = DB::connection('fwoa')->table('formtable_main_38_dt1')
            ->join('formtable_main_38', 'formtable_main_38_dt1.mainid', '=', 'formtable_main_38.id')
            ->join('HrmResource', 'formtable_main_38.sqr', '=', 'HrmResource.id')
            ->join('HrmDepartment', 'formtable_main_38.bm', '=', 'HrmDepartment.id')
            ->where('formtable_main_38_dt1.ksmc', '!=', '')
            ->whereIn('formtable_main_38_dt1.ksmc', array_column($data->items(), 'custname'))
            ->select('formtable_main_38_dt1.*', 'HrmResource.lastname as name', 'HrmDepartment.departmentname as dept')
            ->get()
            ->keyBy('ksmc');

        $extend = $extend->merge($fwoaExtend);

//        $groupData = $data->groupBy('createtime');
//        foreach ($groupData as $key => $value) {
//            $groupData[$key] = $value->groupBy('areaclname');
//            foreach ($groupData[$key] as $key2 => $value2) {
//                $groupData[$key][$key2] = $value2->count();
//            }
//        }
//        dd($groupData);

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
        return view('report.customer', ['rows' => $data, 'extend' => $extend, 'q' => $q, 'tips' => $tips, 'warning' => $warning]);
    }


    public function statistics(Request $request) {
        $start = $request->get('start', date('Y-m-d', strtotime('-1 month')));
        $end = $request->get('end', date('Y-m-d'));

        $q = [
            'start' => $start,
            'end' => $end,
            'display' => 'statistics'
        ];

        $builder = $this->_createBuilder($q);

        $collection = $builder->orderBy('BD_CUBASDOC.CREATETIME', 'desc')->get();
        $groupData = $collection->groupBy('createtime');
        foreach ($groupData as $key => $value) {
            $groupData[$key] = $value->groupBy('areaclname');
            foreach ($groupData[$key] as $key2 => $value2) {
                $groupData[$key][$key2] = $value2->count();
            }
        }
        return view('report.customer', ['rows' => $groupData, 'q' => $q]);
    }


    public function export(Request $request){
        $start = $request->get('start', date('Y-m-d', strtotime('-1 month')));
        $end = $request->get('end', date('Y-m-d'));

        $q = [
            'start' => $start,
            'end' => $end,
            'display' => 'none'
        ];

        $builder = $this->_createBuilder($q);

        $data = $builder->orderBy('BD_CUBASDOC.CREATETIME', 'desc')->get()->toArray();

        $extend = DB::connection('sqlsrv')->table('TY_XXWHZJKS')
            ->join('TY_XINXIWEIHU', 'TY_XINXIWEIHU.ty_xinxiid', '=', 'TY_XXWHZJKS.ty_xinxiid')
            ->where('TY_XXWHZJKS.keshangname', '!=', '')
            ->select('TY_XXWHZJKS.*', 'TY_XINXIWEIHU.name', 'TY_XINXIWEIHU.dept', 'TY_XINXIWEIHU.time', 'TY_XINXIWEIHU.nc', 'TY_XINXIWEIHU.shuoming')
            ->get()
            ->keyBy('keshangname');

        $fwoaExtend = DB::connection('fwoa')->table('formtable_main_38_dt1')
            ->join('formtable_main_38', 'formtable_main_38_dt1.mainid', '=', 'formtable_main_38.id')
            ->join('HrmResource', 'formtable_main_38.sqr', '=', 'HrmResource.id')
            ->join('HrmDepartment', 'formtable_main_38.bm', '=', 'HrmDepartment.id')
            ->where('formtable_main_38_dt1.ksmc', '!=', '')
            ->select('formtable_main_38_dt1.*', 'HrmResource.lastname as name', 'HrmDepartment.departmentname as dept')
            ->get()
            ->keyBy('ksmc');

        $extend = $extend->merge($fwoaExtend);

        foreach ($data as $customer) {
            if (empty($extend[$customer->custname])) {
                $customer->dept = '';
            } else {
                $customer->dept = $extend[$customer->custname]->dept;
            }
        }

        Excel::create("CUSTOMER-$start-$end",function($excel) use ($data){
            $excel->sheet('客商列表', function($sheet) use ($data){
                $sheet->fromArray(json_decode(json_encode($data), true), null, 'A1', false, false);
                $sheet->prependRow(array(
                    '客商编码', '客商名称', '客商简称', '客商类别', '地址', '联系人电话', '联系人姓名', '创建时间', '创建人', '修改时间', '修改人', '申请单位'
                ));
            });
        })->export('xls');
    }
}
