<?php

namespace App\Http\Controllers\Report;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;


class RentingController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
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
        $ids = DB::connection('rent')->table('vw_ht')->select('序号')->get();
        $idsString = implode(',', array_column($ids->toArray(), '序号'));

        $pdo = DB::connection('rent')->getPdo();
        $stmt = $pdo->prepare('EXEC Report_Server_TianYi_Array ?,?,?,?');

        $stmt->execute([$idsString , "%", $start, $end]);

        $stmt->nextRowset();
        $result = $stmt->fetchAll();

        $projects = [];
        $materials = [];
        foreach ($result as $item) {
            array_push($projects, $item['项目部']);
            array_push($materials, $item['品名']);
        }
        $report = null;
        $projects = array_unique($projects);

        $materials = array_unique($materials);
        foreach ($projects as $project) {
            $report[$project] = null;
            foreach ($materials as $material) {
                $report[$project][$material] = [
                    'property' => '',
                    'renting' => 0,
                    'price' => 0,
                    'rent' => 0,
                ];
            }
        }

        foreach ($result as $item) {
            if ($item['清包属性'] !== '') {
                $report[$item['项目部']][$item['品名']]['property'] = $item['清包属性'];
            }
            $report[$item['项目部']][$item['品名']]['renting'] += $item['在租量'];
            $report[$item['项目部']][$item['品名']]['price'] = $item['租金单价'];
            $report[$item['项目部']][$item['品名']]['rent'] += $item['租金'];
        }

        $tableHeadRow2 = '';
        $tableHead = '<thead><tr>
                            <td rowspan="2">单位</td><td rowspan="2">清包属性</td>';
        foreach ($materials as $material) {
            $tableHead = $tableHead . '<td colspan="3">' . $material . '</td>';
            $tableHeadRow2 = $tableHeadRow2 . '<td>在租量</td><td>租金单价</td><td>租金</td>';
        }
        $tableHead = $tableHead . '<td rowspan="2">租金合计</td></tr><tr>'. $tableHeadRow2 .'</tr></thead>';

        $statistics = [];
        foreach ($materials as $material) {
            $statistics[$material] = [
                'renting' => 0,
                'rent' => 0,
            ];
        }

        $finalTotal = 0;
        $tableData = '<tbody>';
        foreach ($report as $project=>$data) {
            $totalRent = 0;
            $tableData = $tableData . '<tr></tr><td>' . $project . '</td><td></td>';
            foreach ($data as $material=>$item) {
                $tableData = $tableData . '<td>' . $item['renting'] . '</td><td>' . $item['price'] . '</td><td>' . $item['rent'] . '</td>';
                $statistics[$material]['renting'] += $item['renting'];
                $statistics[$material]['rent'] += $item['rent'];
                $totalRent += $item['rent'];
            }
            $finalTotal += $totalRent;
            $tableData = $tableData . '<td>' . $totalRent . '</td></tr>';
        }

        $tableData = $tableData . '<tr><td>统计</td><td></td>';
        foreach ($statistics as $material) {
            $tableData = $tableData . '<td>' . $material['renting'] . '</td><td></td><td>' . $material['rent'] . '</td>';
        }
        $tableData = $tableData . '<td>' . $finalTotal . '</td></tr></tbody>';

        $table = $tableHead . $tableData;

        $etime=microtime(true);
        $tips = null;
        $warning = null;
        if ($request->get('page') == null) {
            $tips = "统计用时间 " . round(($etime-$stime), 3) . ' 秒';
        }
        return view('report.renting', ['table' => $table, 'q' => $q, 'tips' => $tips, 'warning' => $warning]);
    }

}
