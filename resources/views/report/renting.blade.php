@extends('layouts.app')

@section('main_container')
    <div class="right_col">
        <div class="">

            <div class="clearfix"></div>

            <div class="row">
                <div class="col-md-12 col-sm-12 col-xs-12">
                    <div class="x_panel">
                        <div class="x_title">
                            <h2>租赁费汇总查询 <small>renting</small></h2>
                            <div class="clearfix"></div>
                        </div>

                        <div class="x_content">

                            <form class="form-horizontal">

                                <div class="row">
                                    <div class="form-group col-sm-6">
                                        <label class="col-sm-2 control-label">租赁区间</label>
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
                                    <table class="table table-hover table-bordered" style="word-break: keep-all;white-space:nowrap;">
                                    {!! $table !!}
                                    </table>
                                </div>

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
        $(this).closest('form').attr("action", "{{ route('report.renting', ['display' => 'default']) }}").submit();
    });
</script>
@endpush

@push('stylesheets')
    <style>
        .table > thead > tr > td {
            vertical-align: middle;
            text-align: center;
        }
        .table-bordered > thead > tr > td[colspan] {
            border-bottom-width: 1px;
        }
    </style>
@endpush
