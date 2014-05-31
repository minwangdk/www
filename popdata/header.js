$(document).ready(function () {
	
$('#loginbtn').click(function() {
	event.preventDefault();
	$('.logbox').toggleClass('hidden');
	$(this).toggleClass('loginbtnclicked');
	$('.mask').toggleClass('hidden');
})


$('.mask').click(function() {  
  $('.logbox').toggleClass('hidden');
  $('a').removeClass('loginbtnclicked');
  $('.mask').toggleClass('hidden');
})










});