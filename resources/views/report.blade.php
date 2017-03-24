@extends('layouts.app')

@section('content')
    <div class="container">
        <form class="form-horizontal">
            <div class="row">
                <div class="form-group col-sm-6">
                    <label for="invcode" class="col-sm-2 control-label">物资编码</label>
                    <div class="col-sm-10">
                        <input type="number" name="code" class="form-control" value="{{ $q['code'] }}" placeholder="请输入查询的物资编码">
                    </div>
                </div>
                <div class="form-group col-sm-6">
                    <label for="invname" class="col-sm-2 control-label">物资名称</label>
                    <div class="col-sm-10">
                        <input type="text" name="name" class="form-control" value="{{ $q['name'] }}" placeholder="请输入查询的物资名称">
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="form-group col-sm-6">
                    <label class="col-sm-2 control-label">起始时间</label>
                    <div class="col-sm-10">
                        <div class="input-group input-daterange">
                            <input type="text" class="form-control" name="start" value="{{ $q['start'] }}">
                            <span class="input-group-addon">to</span>
                            <input type="text" class="form-control" name="end" value="{{ $q['end'] }}">
                        </div>
                    </div>
                </div>

                <div class="form-group col-sm-6">
                    @if ($warning)
                        <label style="color: #a94442" class="col-sm-8 control-label"><span class="glyphicon glyphicon-info-sign"></span>{{ $warning['msg'] }}</label>
                    @endif
                    <div class="pull-right col-sm-4">
                        <button id="searchBtn" type="button" class="btn btn-default">
                            <span class="glyphicon glyphicon-search"></span> 查询
                        </button>
                        <button id="exportBtn" type="button" class="btn btn-default" @if($warning) disabled @endif>
                            <span class="glyphicon glyphicon-export"></span> 导出
                        </button>
                    </div>
                </div>
            </div>
        </form>
        @if($tips)
            <div class="alert alert-success" role="alert">{{ $tips }}</div>
        @endif
        <table class="table table-hover table-bordered">
            <thead>
                <tr>
                    <th>#</th>
                    {{--<th>No</th>--}}
                    <th>编码</th>
                    <th>名称(规格)</th>
                    <th>申报数量</th>
                    <th>单价</th>
                    <th>添加剂</th>
                    <th>供应商</th>
                    <th>核定时间</th>
                    {{--<th>备注</th>--}}
                </tr>
            </thead>
            <tbody>
                @forelse ($rows as $row)
                    <tr>
                        <th>{{ $loop->iteration }}</th>
                        {{--<th>{{ $row->pk_mt_cplan }}</th>--}}
                        <th>{{ $row->invcode }}</th>
                        <th>{{ $row->invname }}<br><span class="small" style="font-weight:normal;">{{ $row->invspec }}</span></th>
                        <th>{{ $row->nnum }}</th>
                        <th>{{ $row->nprice }}</th>
                        <th>{{ $row->vdef1 }}</th>
                        <th>{{ $row->custname }}</th>
                        <th>{{ $row->tbilltime }}</th>
                        {{--<th>{{ $row->vdef3 }}</th>--}}
                    <tr>
                @empty
                    <tr>
                        <th colspan="9" style="text-align: center">没有数据</th>
                    </tr>
                @endforelse
            </tbody>
        </table>
        @if($rows)
            {{ $rows->appends($q)->links() }}
        @endif
    </div>
@endsection
