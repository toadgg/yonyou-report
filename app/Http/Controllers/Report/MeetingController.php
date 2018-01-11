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
            ->leftJoin('HrmResource', 'HrmResource.id', '=', 'meeting.contacter')
            ->leftJoin('MeetingRoom', 'MeetingRoom.id', '=', 'meeting.address')
            ->leftJoin('Meeting_Type', 'Meeting_Type.id', '=', 'meeting.meetingtype')
            ->selectRaw('meeting.id , meeting.name, Meeting_Type.name type, HrmResource.lastname contacter, MeetingRoom.name address, meeting.begindate+\' \'+meeting.begintime begintime, meeting.meetingstatus, count(meeting_sign.signTime) signed, count(*) total')
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

        $data = $builder->groupBy(['meeting.id', 'meeting.name', 'meeting.begindate', 'meeting.begintime', 'HrmResource.lastname', 'MeetingRoom.name', 'Meeting_Type.name', 'meeting.meetingstatus'])->orderBy('meeting.id', 'desc')
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

    public function show($id)
    {
        $data = DB::connection('fwoa')->table('HrmResource')
            ->leftJoin('HrmDepartment', 'HrmDepartment.id', '=', 'HrmResource.departmentid')
            ->leftJoin('meeting_sign', 'meeting_sign.userid', '=', 'HrmResource.id')
            ->selectRaw('HrmDepartment.departmentname, HrmResource.lastname, HrmResource.mobile, meeting_sign.signTime')
            ->where("meeting_sign.meetingid", $id)
            ->orderBy('meeting_sign.signTime')
            ->orderBy('HrmDepartment.departmentname')
            ->get();

        if (count($data) == 0) {
            echo '<tr><th colspan="4" style="text-align: center">没有数据</th></tr>';
        } else {
            foreach ($data as $row) {
                echo '<tr class="'. ($row->signTime==null ? 'unsign' : 'sign') . '"><th>'. $row->departmentname .'</th><th>'. $row->lastname .'</th><th>'. $row->mobile .'</th><th>'. $row->signTime .'</th><tr>';
            }
        }
    }
}
