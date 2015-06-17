function search(username) {
	createCard(username);
}

$(document).ready(function() {
	$('#playerSearch').val('');
	
	$('#playerSearch').keydown(function(event) {
		if (event.which == 13) {
			search($('#playerSearch').val());
			$('#playerSearch').blur();
		}
	});
	
	$('#playerSearch').keyup(_.debounce(function() {
		
		if ($('#playerSearch').val().length > 1 && ($('#playerSearch').is(':focus'))) {
			$.post('api.php', { 'apiTarget' : 'wildcardByName', 'name' : $('#playerSearch').val()}).done(function(data) {
				data = $.parseJSON(data);
				
				if (data.error) {
					$('#suggestions').slideUp(150);
				} else {
					list = '';				
					
					$.each(data,function(key,val) {					
						list += '<div id=\"suggestionRow' + key + '\" class=\"suggestionRow text\">' + val.name + '</div>';
					});
					
					$('#suggestions').html(list);
					//$('#suggestions').css('display','block');
					$('#suggestions').slideDown(150);
					//$('#suggestions').html(list);
					
					$('.suggestionRow').mousedown(function() {
						$('#playerSearch').val($(this).text());
						search($(this).text());
					});
				}
			});
		} else {
			$('#suggestions').slideUp(150);
		};
	}, 250));
	
	$('#playerSearch').blur(function() {
		$('#suggestions').slideUp(150);
	})
});