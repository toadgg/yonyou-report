@extends('layouts.none')

@section('main_container')
    <div class="right_col">
        <div class="">

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
                                        <label class="col-sm-2 control-label">会议时间</label>
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
                                            <th>会议类型</th>
                                            <th>会议地点</th>
                                            <th>联系人</th>
                                            <th>会议时间</th>
                                            <th>状态</th>
                                            <th>未签到</th>
                                            <th>签到/应到</th>
                                            <th>签到进度</th>
                                            <th>查看详情</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        @forelse ($rows as $row)
                                            <tr>
                                                <th>{{ $row->id }}</th>
                                                <th>{{ $row->name }}</th>
                                                <th>{{ $row->type }}</th>
                                                <th>{{ $row->address }}</th>
                                                <th>{{ $row->contacter }}</th>
                                                <th>{{ $row->begintime }}</th>
                                                <th>
                                                    @if ($row->meetingstatus == 0)
                                                        草稿
                                                    @elseif ($row->meetingstatus == 1)
                                                        待审批
                                                    @elseif ($row->meetingstatus == 2)
                                                        正常
                                                    @elseif ($row->meetingstatus == 3)
                                                        退回
                                                    @elseif ($row->meetingstatus == 4)
                                                        取消
                                                    @elseif ($row->meetingstatus == 5)
                                                        结束
                                                    @else
                                                        未知状态
                                                    @endif
                                                </th>
                                                <th>{{ $row->total - $row->signed }}</th>
                                                <th>{{ $row->signed }}/{{ $row->total }}</th>
                                                <th>{{ floor($row->signed/$row->total * 100) }}%</th>
                                                <th><a data-toggle="modal" data-name="{{ $row->name }}" data-id="{{ $row->id }}" data-target=".bs-example-modal-lg">详细信息</a></th>
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

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade bs-example-modal-lg" tabindex="-1" role="dialog" aria-hidden="true" style="display: none;">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">×</span>
                    </button>
                    <h4 class="modal-title" id="myModalLabel">会议标题</h4>
                </div>
                <div class="modal-body">
                    <div>
                        <button id="unsignBtn" type="button" class="btn btn-default active">未签到</button>
                        <button id="signBtn" type="button" class="btn btn-default">全部</button>
                        <button id="refreshBtn" type="button" class="btn btn-default glyphicon glyphicon-refresh"></button>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-hover table-bordered">
                            <thead>
                            <tr>
                                <th>部门</th>
                                <th>人员</th>
                                <th>手机号码</th>
                                <th>签到时间</th>
                            </tr>
                            </thead>
                            <tbody>

                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">关闭</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    var refreshUrl;
    $("#searchBtn").on('click', function(){
        $(this).closest('form').attr("action", "{{ route('report.meeting', ['display' => 'default']) }}").submit();
    });
    $("table a").on('click', function(){
        $("#myModalLabel").html($(this).data('name'));
        var id = $(this).data('id');
        var url = "{{ route('report.meeting') }}" + '/' + id;
        refreshUrl = url;
        var $tbody = $(".modal table tbody");
        $tbody.html('<tr><th colspan="4" style="text-align: center">数据请求中...</th></tr>');
        $.get(url, function(result){
            $tbody.html(result);
            if($("#signBtn").hasClass('active')) {
                $('tr.sign').show();
            } else {
                $('tr.sign').hide();
            }
        });
    });
    $("#signBtn").on('click', function(){
        $(this).toggleClass('active').blur();
        if($(this).hasClass('active')) {
            $('tr.sign').show();
        } else {
            $('tr.sign').hide();
        }
    });
    $("#refreshBtn").on('click', function(){
        $.get(refreshUrl, function(result){
            $(".modal table tbody").html(result);
            $("#refreshBtn").blur();
            if($("#signBtn").hasClass('active')) {
                $('tr.sign').show();
            } else {
                $('tr.sign').hide();
            }
        });
    });
</script>
@endpush
