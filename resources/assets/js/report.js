;$(document).ready(function() {
    init_daterangepicker_right();

    function init_daterangepicker_right() {

        if( typeof ($.fn.daterangepicker) === 'undefined'){ return; }
        console.log('init_daterangepicker');

        var cb = function(start, end, label) {
            console.log(start.toISOString(), end.toISOString(), label);
            $('#reportrange span').html(start.format('YYYY-MM-DD') + ' 到 ' + end.format('YYYY-MM-DD'));
        };

        var optionSet1 = {
            startDate: $('#startDate').val(),
            endDate: $('#endDate').val(),
            minDate: '2012-01-01',
            maxDate: moment(),
            // dateLimit: {
            //     days: 60
            // },
            showDropdowns: true,
            showWeekNumbers: true,
            timePicker: false,
            timePickerIncrement: 1,
            timePicker12Hour: true,
            ranges: {
                '今天': [moment(), moment()],
                '昨天': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                '近7天': [moment().subtract(6, 'days'), moment()],
                '近30天': [moment().subtract(29, 'days'), moment()],
                '当月': [moment().startOf('month'), moment().endOf('month')],
                '上一个月': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
            },
            opens: 'right',
            buttonClasses: ['btn btn-default'],
            applyClass: 'btn-small btn-primary',
            cancelClass: 'btn-small',
            format: 'YYYY-MM-DD',
            separator: ' 到 ',
            locale: {
                format: 'YYYY-MM-DD',
                applyLabel: '确认',
                cancelLabel: '取消',
                fromLabel: 'From',
                toLabel: 'To',
                customRangeLabel: '自定义',
                daysOfWeek: ['日', '一', '二', '三', '四', '五', '六'],
                monthNames: ['一月', '二月', '三月', '四月', '五月', '六月', '七月', '八月', '九月', '十月', '十一月', '十二月'],
                firstDay: 1
            }
        };

        $('#reportrange span').html($('#startDate').val() + ' 到 ' + $('#endDate').val());

        $('#reportrange').daterangepicker(optionSet1, cb);

        $('#reportrange').on('show.daterangepicker', function() {
            console.log("show event fired");
        });
        $('#reportrange').on('hide.daterangepicker', function() {
            console.log("hide event fired");
        });
        $('#reportrange').on('apply.daterangepicker', function(ev, picker) {
            $('#startDate').val(picker.startDate.format('YYYY-MM-DD'));
            $('#endDate').val(picker.endDate.format('YYYY-MM-DD'));
            console.log("apply event fired, start/end dates are " + picker.startDate.format('YYYY-MM-DD') + " 到 " + picker.endDate.format('YYYY-MM-DD'));
        });
        $('#reportrange').on('cancel.daterangepicker', function(ev, picker) {
            console.log("cancel event fired");
        });

        // $('#options1').click(function() {
        //     $('#reportrange').data('daterangepicker').setOptions(optionSet1, cb);
        // });
        //
        // $('#options2').click(function() {
        //     $('#reportrange').data('daterangepicker').setOptions(optionSet2, cb);
        // });
        //
        // $('#destroy').click(function() {
        //     $('#reportrange').data('daterangepicker').remove();
        // });

    }
});