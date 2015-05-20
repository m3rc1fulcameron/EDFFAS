$(document).ready(function() {
	$('#loginForm').css('display','none');
	$('#loginForm').parent().append('<a id=\"loginFormSpawner\" href=\"#\" title=\"Login\">Login</a>');
	
	$('#loginFormSpawner').click(function() {
		$('#loginFormSpawner').css('display','none');
		$('#loginForm').css('display','block');
	});
});