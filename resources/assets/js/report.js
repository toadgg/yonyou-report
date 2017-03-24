;$('.input-daterange').datepicker({
    language: "zh-CN",
    format: 'yyyy-mm-dd',
    autoclose: true,
    todayBtn: "linked",
    todayHighlight : true,
    endDate : new Date()
});
$("#searchBtn").on('click', function(){
    $(this).closest('form').attr("action", "/").submit();
});

$("#exportBtn").on('click', function(){
    $(this).closest('form').attr("action", "export").submit();
});
$(".alert-success").fadeTo(2000, 500).slideUp(500, function(){
    $("#alert-success").slideUp(500);
});