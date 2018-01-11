<?php

namespace App\Http\Controllers\Report;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use DB;
use Maatwebsite\Excel\Facades\Excel;


class MeetingController extends Controller
{

    public function __construct()
    {
//        $this->middleware('auth');
    }

    private function _createBuilder(array $query) {
        $_builder = DB::connection('fwoa')->table('meeting')
            ->leftJoin('meeting_sign', 'meeting_sign.meetingid', '=', 'meeting.id')
            ->selectRaw('meeting.id , meeting.name, count(meeting_sign.signTime) signed, count(*) total')
            ->whereRaw("meeting.begindate between ? and ?", [$query['start'], $query['end']]);
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

        $data = $builder->groupBy(['meeting.id', 'meeting.name'])->orderBy('meeting.id', 'desc')
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
        return view('report.meeting', ['rows' => $data, 'q' => $q, 'tips' => $tips, 'warning' => $warning]);
    }

}
