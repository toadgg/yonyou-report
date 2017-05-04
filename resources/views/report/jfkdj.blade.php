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
                            <h2>罚款登记查询 <small>jzpm_qa_prizpuns_b</small></h2>
                            <div class="clearfix"></div>
                        </div>

                        <div class="x_content">

                            <form class="form-horizontal">

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
                                            <button id="statisticsBtn" type="button" class="btn btn-default disabled">
                                                <span class="fa fa-bar-chart"></span> 统计
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </form>

                            @if ($q['display'] == 'statistics')

                            @elseif($q['display'] == 'default')
                                <div class="table-responsive">
                                    <table class="table table-hover table-bordered">
                                        <thead>
                                        <tr>
                                            <th>序号</th>
                                            <th class="col-md-1">单号</th>
                                            <th>项目部</th>
                                            <th class="col-md-1">被罚款人</th>
                                            <th>处罚原因</th>
                                            <th>处罚金额</th>
                                            <th style="min-width: 100px">处罚日期</th>
                                            <th style="min-width: 100px">上交日期</th>
                                            <th class="col-md-1">开单人</th>
                                            <th>备注</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        @forelse ($rows as $row)
                                            <tr>
                                                <th>{{ $loop->iteration }}</th>
                                                <th>{{ $row->vbillno }}</th>
                                                <th>{{ $row->vname }}</th>
                                                <th>{{ $row->vpsnname }}</th>
                                                <th>{{ $row->veventdesc }}</th>
                                                <th>{{ $row->nmny }}</th>
                                                <th>{{ $row->dbilldate }}</th>
                                                <th>{{ $row->ddate }}</th>
                                                <th>{{ $row->pk_psndoc }}</th>
                                                <th>{{ $row->vmeno }}</th>
                                            <tr>
                                        @empty
                                            <tr>
                                                <th colspan="10" style="text-align: center">没有数据</th>
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
        $(this).closest('form').attr("action", "{{ route('report.jfkdj') }}").submit();
    });

    $("#exportBtn").on('click', function(){
        $(this).closest('form').attr("action", "{{ route('export.jfkdj') }}" ).submit();
    });
    $("#statisticsBtn").on('click', function(){
        {{--$(this).closest('form').attr("action", "{{ route('report.jfkdj.statistics') }}" ).submit();--}}
    });
</script>
@endpush
