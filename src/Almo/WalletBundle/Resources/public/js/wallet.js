$(document).ready(function() {
	
	$(function() {
		 $(".datepicker").datetimepicker({
			 format: 'Y-m-d H:i', 
			 dayOfWeekStart : 1,
//			 defaultTime: 'now'
		 });
	 });
	
//	$(".datepicker").datapicker();
	
	// show/hide notice in operation list
	$(".al_operation_needs_detail").click(function(){
		$(this).next(".al_operation_detail_row").toggle();	
	});
});