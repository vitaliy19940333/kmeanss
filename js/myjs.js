
$(document).ready(function(){
	/*$('.main-content img').attr({'data-toggle':'modal','data-target':'#newModal','onclick':'getSrc(this);'});
	$('ul#breadcrumbs-one li a:last').addClass('current');*/
	$("#breadcrumbs-one li:last a").addClass('current');
	
	   
});
function getSrc(e){
		var newSrc = document.getElementById('newSrc');
		newSrc.src = e.src;
}
