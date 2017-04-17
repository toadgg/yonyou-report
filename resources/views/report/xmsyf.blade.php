@extends('layouts.app')

@section('main_container')
    <div class="right_col">
        <div class="">
            <div class="page-title">
                <div class="title_left">
                    <h3>NC数据报表</h3>
                </div>
            </div>

            <div class="clearfix"></div>

            <div class="row">
                <div class="col-md-12 col-sm-12 col-xs-12">
                    <div class="x_panel">
                        <div class="x_title">
                            <h2>项目试验费查询 <small>jzpm_pc_factbilll_b</small></h2>
                            <div class="clearfix"></div>
                        </div>

                        <div class="x_content">

                            <form class="form-horizontal">
                                <div class="row">
                                    <div class="form-group col-sm-6">
                                        <label for="invcode" class="col-sm-2 control-label">项目名称</label>
                                        <div class="col-sm-10">
                                            <input type="text" name="project" class="form-control" value="{{ $q['project'] }}" placeholder="请输入查询的项目名称">
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="form-group col-sm-6">
                                        <label class="col-sm-2 control-label">起始时间</label>
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
                                        <div class="pull-right col-sm-7">
                                            <button id="searchBtn" type="button" class="btn btn-default">
                                                <span class="glyphicon glyphicon-search"></span> 查询
                                            </button>
                                            <button id="exportBtn" type="button" class="btn btn-default">
                                                <span class="glyphicon glyphicon-export"></span> 导出
                                            </button>
                                            <button id="statisticsBtn" type="button" class="btn btn-default">
                                                <span class="fa fa-bar-chart"></span> 统计
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </form>

                            @if ($q['display'] == 'statistics')
                                <div class="table-responsive">
                                    <table class="table table-hover table-bordered">
                                        <thead>
                                        <tr>
                                            <th>项目</th>
                                            <th>明细</th>
                                            <th>项目总计</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        @forelse ($rows as $key => $row)
                                            <tr>
                                                <th class="col-md-2">{{ $key }}</th>
                                                <th>
                                                    @forelse ($row['items'] as $item)
                                                        {{$item->vbillno}}:{{$item->vname}}[ {{$item->nfeebasemny}} ] <br>
                                                    @endforeach
                                                </th>
                                                <th>{{ $row['total'] }}</th>
                                            <tr>
                                        @empty
                                            <tr>
                                                <th colspan="3" style="text-align: center">没有数据</th>
                                            </tr>
                                        @endforelse
                                        <tr>
                                            <th class="col-md-2">总计</th>
                                            <th></th>
                                            <th>{{ $sum }}</th>
                                        <tr>
                                        </tbody>
                                    </table>
                                </div>
                            @elseif($q['display'] == 'default')
                                <div class="table-responsive">
                                    <table class="table table-hover table-bordered">
                                        <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>单据号</th>
                                            <th>项目名称</th>
                                            <th class="col-md-1">申请日期</th>
                                            <th class="col-md-1">金额（元）</th>
                                            <th>支付范围（时间）</th>
                                            <th>试验室名称</th>
                                            <th>备注</th>
                                            {{--<th>备注</th>--}}
                                        </tr>
                                        </thead>
                                        <tbody>
                                        @forelse ($rows as $row)
                                            <tr>
                                                <th>{{ $loop->iteration }}</th>
                                                <th>{{ $row->vbillno }}</th>
                                                <th>{{ $row->vname }}</th>
                                                <th>{{ $row->dbusidate }}</th>
                                                <th>{{ $row->nfeebasemny }}</th>
                                                <th>{{ $row->vdef1 }}</th>
                                                <th>{{ $row->vdef2 }}</th>
                                                <th>{{ $row->vmome }}</th>
                                            <tr>
                                        @empty
                                            <tr>
                                                <th colspan="8" style="text-align: center">没有数据</th>
                                            </tr>
                                        @endforelse
                                        </tbody>
                                    </table>
                                </div>

                                @if($rows)
                                    {{ $rows->appends($q)->links() }}
                                @endif
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
        $(this).closest('form').attr("action", "{{ route('report.xmsyf', ['display' => 'default']) }}").submit();
    });

    $("#exportBtn").on('click', function(){
        $(this).closest('form').attr("action", "{{ route('export.xmsyf') }}" ).submit();
    });
    $("#statisticsBtn").on('click', function(){
        $(this).closest('form').attr("action", "{{ route('report.xmsyf.statistics', ['display' => 'statistics']) }}" ).submit();
    });
</script>
@endpush
