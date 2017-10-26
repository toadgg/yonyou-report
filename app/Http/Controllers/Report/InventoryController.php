<?php

namespace App\Http\Controllers\Report;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use DB;
use Maatwebsite\Excel\Facades\Excel;


class InventoryController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
    }

    private function _createBuilder(array $query) {
        $_builder = DB::connection('oracle')->table('BD_INVBASDOC')
            ->leftJoin('BD_MEASDOC', 'BD_INVBASDOC.PK_MEASDOC', '=', 'BD_MEASDOC.PK_MEASDOC')
            ->leftJoin('BD_INVCL', 'BD_INVBASDOC.PK_INVCL', '=', 'BD_INVCL.PK_INVCL')
            ->leftJoin('SM_USER SM_USER_CREATOR', 'BD_INVBASDOC.CREATOR', '=', 'SM_USER_CREATOR.CUSERID')
            ->leftJoin('SM_USER SM_USER_MODIFIER', 'BD_INVBASDOC.MODIFIER', '=', 'SM_USER_MODIFIER.CUSERID')
            ->selectRaw('CONCAT(bd_invbasdoc.INVNAME, bd_invbasdoc.INVSPEC) as PK, BD_INVBASDOC.INVCODE, BD_INVBASDOC.INVNAME, BD_INVBASDOC.INVSPEC, BD_INVCL.INVCLASSNAME, BD_MEASDOC.MEASNAME, substr(BD_INVBASDOC.CREATETIME, 1, 10) as CREATETIME, SM_USER_CREATOR.USER_NAME as CREATOR, substr(BD_INVBASDOC.MODIFYTIME, 1, 10) as MODIFYTIME, SM_USER_MODIFIER.USER_NAME as MODIFIER')
            ->whereRaw("nvl(BD_INVBASDOC.dr, 0) = 0 and (substr(BD_INVBASDOC.CREATETIME, 1, 10)>=? and substr(BD_INVBASDOC.CREATETIME, 1, 10)<=?)", [$query['start'], $query['end']]);
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

        $data = $builder->orderBy('BD_INVBASDOC.CREATETIME', 'desc')
            ->paginate(100);

        $extend = DB::connection('sqlsrv')->table('TY_XINXIWEIHU')
            ->join('TY_XXWHZJCH', 'TY_XINXIWEIHU.ty_xinxiid', '=', 'TY_XXWHZJCH.ty_xinxiid')
            ->where('TY_XXWHZJCH.cunhuoname', '!=', '')
            ->whereIn('TY_XXWHZJCH.cunhuoname', array_column($data->items(), 'invname'))
            ->selectRaw('(TY_XXWHZJCH.cunhuoname + TY_XXWHZJCH.guige) as pk, TY_XXWHZJCH.*, TY_XINXIWEIHU.name, TY_XINXIWEIHU.dept, TY_XINXIWEIHU.time, TY_XINXIWEIHU.nc, TY_XINXIWEIHU.shuoming')
            ->get()
            ->keyBy('pk');

        $fwoaExtend = DB::connection('fwoa')->table('formtable_main_38_dt2')
            ->join('formtable_main_38', 'formtable_main_38_dt2.mainid', '=', 'formtable_main_38.id')
            ->join('HrmResource', 'formtable_main_38.sqr', '=', 'HrmResource.id')
            ->join('HrmDepartment', 'formtable_main_38.bm', '=', 'HrmDepartment.id')
            ->where('formtable_main_38_dt2.chmc', '!=', '')
            ->whereIn('formtable_main_38_dt2.chmc', array_column($data->items(), 'invname'))
            ->selectRaw('(formtable_main_38_dt2.chmc + formtable_main_38_dt2.ggxh) as pk, HrmResource.lastname as name, HrmDepartment.departmentname as dept')
            ->get()
            ->keyBy('pk');

        $extend = $extend->merge($fwoaExtend);

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
        return view('report.inventory', ['rows' => $data, 'extend' => $extend, 'q' => $q, 'tips' => $tips, 'warning' => $warning]);
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

        $collection = $builder->orderBy('BD_INVBASDOC.CREATETIME', 'desc')->get();

        $groupData = $collection->groupBy('createtime');
        foreach ($groupData as $key => $value) {
            $groupData[$key] = $value->groupBy('invclassname');
            foreach ($groupData[$key] as $key2 => $value2) {
                $groupData[$key][$key2] = $value2->count();
            }
        }
        return view('report.inventory', ['rows' => $groupData, 'q' => $q]);
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

        $data = $builder->orderBy('BD_INVBASDOC.CREATETIME', 'desc')->get()->toArray();

        $extend = DB::connection('sqlsrv')->table('TY_XINXIWEIHU')
            ->join('TY_XXWHZJCH', 'TY_XINXIWEIHU.ty_xinxiid', '=', 'TY_XXWHZJCH.ty_xinxiid')
            ->where('TY_XXWHZJCH.cunhuoname', '!=', '')
            ->selectRaw('(TY_XXWHZJCH.cunhuoname + TY_XXWHZJCH.guige) as pk, TY_XXWHZJCH.*, TY_XINXIWEIHU.name, TY_XINXIWEIHU.dept, TY_XINXIWEIHU.time, TY_XINXIWEIHU.nc, TY_XINXIWEIHU.shuoming')
            ->get()
            ->keyBy('pk');

        $fwoaExtend = DB::connection('fwoa')->table('formtable_main_38_dt2')
            ->join('formtable_main_38', 'formtable_main_38_dt2.mainid', '=', 'formtable_main_38.id')
            ->join('HrmResource', 'formtable_main_38.sqr', '=', 'HrmResource.id')
            ->join('HrmDepartment', 'formtable_main_38.bm', '=', 'HrmDepartment.id')
            ->where('formtable_main_38_dt2.chmc', '!=', '')
            ->selectRaw('(formtable_main_38_dt2.chmc + formtable_main_38_dt2.ggxh) as pk, HrmResource.lastname as name, HrmDepartment.departmentname as dept')
            ->get()
            ->keyBy('pk');

        $extend = $extend->merge($fwoaExtend);

        foreach ($data as $inventory) {
            if (empty($extend[$inventory->pk])) {
                $inventory->dept = '';
            } else {
                $inventory->dept = $extend[$inventory->pk]->dept;
            }
            unset($inventory->pk);
        }

        Excel::create("INVENTORY-$start-$end",function($excel) use ($data){
            $excel->sheet('存货列表', function($sheet) use ($data){
                $sheet->fromArray(json_decode(json_encode($data), true), null, 'A1', false, false);
                $sheet->prependRow(array(
                    '存货编码', '存货名称', '规格型号', '存货分类', '计量单位', '创建时间', '创建人', '修改时间', '修改人', '申请单位'
                ));
            });
        })->export('xls');
    }
}
