@extends('layouts.none')

@section('main_container')
    <div class="right_col">
        <div class="">
            <div class="page-title">
                <div class="title_left">
                    <h3>OA数据报表</h3>
                </div>
            </div>

            <div class="clearfix"></div>

            <div class="row">
                <div class="col-md-12 col-sm-12 col-xs-12">
                    <div class="x_panel">
                        <div class="x_title">
                            <h2>会议签到查询 <small>meeting_sign</small></h2>
                            <div class="clearfix"></div>
                        </div>

                        <div class="x_content">

                            <form class="form-horizontal">

                                <div class="row">
                                    <div class="form-group col-sm-6">
                                        <label class="col-sm-2 control-label">会议开始时间</label>
                                        <div class="col-sm-10">
                                            <div id="reportrange" class="pull-left" style="background: #fff; cursor: pointer; padding: 5px 10px; border: 1px solid #ccc">
                                                <i class="glyphicon glyphicon-calendar fa fa-calendar"></i>
                                                <span></span> <b class="caret"></b>
                                            </div>
                                            <input id="startDate" type="hidden" name="start" value="{{ $q['start'] }}">
                                            <input id="endDate" type="hidden" name="end" value="{{ $q['end'] }}">
                                        </div>
                                    </div>

                                    <div class="form-group col-sm-6">
                                        <div class="pull-right col-sm-3">
                                            <button id="searchBtn" type="button" class="btn btn-default">
                                                <span class="glyphicon glyphicon-search"></span> 查询
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </form>
                                <p> @if($tips)
                                        <span>{{ $tips }}</span>
                                    @endif
                                    @if ($warning)
                                        <span style="color: #a94442" class="control-label"><span class="glyphicon glyphicon-info-sign"></span>{{ $warning['msg'] }}</span>
                                    @endif
                                </p>
                                <div class="table-responsive">
                                    <table class="table table-hover table-bordered">
                                        <thead>
                                        <tr>
                                            <th>会议ID</th>
                                            <th>会议名称</th>
                                            <th>签到/应到人数</th>
                                            <th>签到进度</th>
                                            <th>详情</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        @forelse ($rows as $row)
                                            <tr>
                                                <th>{{ $row->id }}</th>
                                                <th>{{ $row->name }}</th>
                                                <th>{{ $row->signed }}/{{ $row->total }}</th>
                                                <th>{{ floor($row->signed/$row->total * 100) }}%</th>
                                                <th>查看</th>
                                            <tr>
                                        @empty
                                            <tr>
                                                <th colspan="5" style="text-align: center">没有数据</th>
                                            </tr>
                                        @endforelse
                                        </tbody>
                                    </table>
                                </div>

                                @if($rows)
                                    {{ $rows->appends($q)->links() }}
                                @endif

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    $("#searchBtn").on('click', function(){
        $(this).closest('form').attr("action", "{{ route('report.meeting', ['display' => 'default']) }}").submit();
    });
</script>
@endpush
