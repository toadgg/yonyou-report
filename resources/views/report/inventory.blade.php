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
                            <h2>存货查询 <small>bd_invbasdoc</small></h2>
                            <div class="clearfix"></div>
                        </div>

                        <div class="x_content">

                            <form class="form-horizontal">
                                {{--<div class="row">--}}
                                    {{--<div class="form-group col-sm-6">--}}
                                        {{--<label for="invcode" class="col-sm-2 control-label">物资编码</label>--}}
                                        {{--<div class="col-sm-10">--}}
                                            {{--<input type="number" name="code" class="form-control" value="{{ $q['code'] }}" placeholder="请输入查询的物资编码">--}}
                                        {{--</div>--}}
                                    {{--</div>--}}
                                    {{--<div class="form-group col-sm-6">--}}
                                        {{--<label for="invname" class="col-sm-2 control-label">物资名称</label>--}}
                                        {{--<div class="col-sm-10">--}}
                                            {{--<input type="text" name="name" class="form-control" value="{{ $q['name'] }}" placeholder="请输入查询的物资名称">--}}
                                        {{--</div>--}}
                                    {{--</div>--}}
                                {{--</div>--}}

                                <div class="row">
                                    <div class="form-group col-sm-6">
                                        <label class="col-sm-2 control-label">操作时间</label>
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
                                            <th>日期</th>
                                            <th>明细</th>
                                            <th>总计</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        @forelse ($rows as $key => $row)
                                            <tr>
                                                <th class="col-md-2">{{ $key }}</th>
                                                <th>
                                                    @php( $total = 0 )
                                                    @foreach ($rows[$key] as $key2 => $row2)
                                                        @php( $total +=  $row2)
                                                        {{ $key2 }}-{{ $row2 }}条 <br>
                                                    @endforeach
                                                </th>
                                                <th>{{ $total }}</th>
                                            <tr>
                                        @empty
                                            <tr>
                                                <th colspan="2" style="text-align: center">没有数据</th>
                                            </tr>
                                        @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            @elseif($q['display'] == 'default')
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
                                            <th>存货编码</th>
                                            <th>存货名称</th>
                                            <th class="col-md-1">规格型号</th>
                                            <th>存货分类</th>
                                            <th>计量单位</th>
                                            <th>创建人</th>
                                            <th>创建时间</th>
                                            <th>申请单位</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        @forelse ($rows as $row)
                                            <tr>
                                                {{--<th>{{ $row->pk_mt_cplan }}</th>--}}
                                                <th>{{ $row->invcode }}</th>
                                                <th>{{ $row->invname }}</th>
                                                <th>{{ $row->invspec }}</th>
                                                <th>{{ $row->invclassname }}</th>
                                                <th>{{ $row->measname }}</th>
                                                <th>{{ $row->creator }}</th>
                                                <th>{{ $row->createtime }}</th>
                                                <th>
                                                    @if(!empty($extend[$row->pk]))
                                                        {{ $extend[$row->pk]->dept }}
                                                    @endif
                                                </th>
                                            <tr>
                                        @empty
                                            <tr>
                                                <th colspan="9" style="text-align: center">没有数据</th>
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
        $(this).closest('form').attr("action", "{{ route('report.inventory', ['display' => 'default']) }}").submit();
    });

    $("#exportBtn").on('click', function(){
        $(this).closest('form').attr("action", "{{ route('export.inventory') }}" ).submit();
    });

    $("#statisticsBtn").on('click', function(){
        $(this).closest('form').attr("action", "{{ route('report.inventory.statistics', ['display' => 'statistics']) }}" ).submit();
    });
</script>
@endpush
