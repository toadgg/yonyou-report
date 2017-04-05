@extends('layouts.app')

@section('main_container')
    <div class="right_col">
        <div class="">
            <div class="page-title">
                <div class="title_left">
                    <h3>国家级非物质文化遗产名录</h3>
                </div>
            </div>

            <div class="clearfix"></div>

            <div class="row">
                <div class="col-md-12 col-sm-12 col-xs-12">
                    <div class="x_panel">
                        <div class="x_title">
                            <h2>非遗名录查询 <small>V1.0</small></h2>
                            <div class="clearfix"></div>
                        </div>

                        <div class="x_content">

                            <form class="form-horizontal">
                                <div class="row form-group">
                                    <a href="{{ route('report.heritages', ['category' => '全部']) }}" class="btn @if($target == '全部') btn-dark @else btn-primary @endif" type="button">全部({{ $total }})</a>
                                    @forelse ($categories as $category)
                                        <a href="{{ route('report.heritages', ['category' => $category->category]) }}" class="btn @if($target == $category->category) btn-dark @else btn-primary @endif" type="button">{{ $category->category }}({{ $category->total }})</a>
                                    @endforeach
                                </div>
                            </form>

                            <div class="table-responsive">
                                <table class="table table-hover table-bordered">
                                    <thead>
                                    <tr>
                                        <th>序号</th>
                                        <th>编号</th>
                                        <th>项目名称</th>
                                        <th>申报地区或单位</th>
                                        <th>批次</th>
                                        <th>类别</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @forelse ($rows as $row)
                                        <tr>
                                            <th class="col-md-1">{{ $row->id }}</th>
                                            <th class="col-md-1">{{ $row->no }}</th>
                                            <th>{{ $row->name }}</th>
                                            <th>{{ $row->area }}</th>
                                            <th class="col-md-1">{{ $row->batch }}</th>
                                            <th class="col-md-1">{{ $row->category }}</th>
                                        <tr>
                                    @empty
                                        <tr>
                                            <th colspan="6" style="text-align: center">没有数据</th>
                                        </tr>
                                    @endforelse
                                    </tbody>
                                </table>
                            </div>

                            @if($rows)
                                {{ $rows->appends(['category' => $target])->links() }}
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
        $(this).closest('form').attr("action", "{{ route('report.heritages') }}").submit();
    });

    $("#exportBtn").on('click', function(){
        $(this).closest('form').attr("action", "{{ route('export.heritages') }}" ).submit();
    });
</script>
@endpush
