function search(username) {
	updatePlayerCard(username);
	$('#suggestions').css('display','none');
}

$(document).ready(function() {
	$('#playerSearch').val('');
	
	$('#playerSearch').keydown(function(event) {
		if (event.which == 13) {
			search($('#playerSearch').val());
		}
	})
	
	$('#playerSearch').keyup(_.debounce(function() {
		
		if ($('#playerSearch').val() != '') {
			$.post('api.php?apiTarget=nameOnly', { playerName : $('#playerSearch').val()}).done(function(data) {
				list = '';				
				
				$.each($.parseJSON(data),function(key,val) {					
					list += '<div id=\"suggestionRow' + key + '\" class=\"suggestionRow text\">' + val + '</div>';
				});
				
				if (list !== '') {
					$('#suggestions').html(list);
					//$('#suggestions').css('display','block');
					$('#suggestions').slideDown(150);
					//$('#suggestions').html(list);
					
					$('.suggestionRow').click(function() {
						$('#playerSearch').val($(this).text());
						search($(this).text());
					});
				} else {
					$('#suggestions').slideUp(150);
				};
			});
		} else {
			$('#suggestions').slideUp(150);
		};
	}, 250));
});