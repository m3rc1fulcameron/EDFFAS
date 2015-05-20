function spawnPlayerCard(username) {
	try {
		$.post('api.php?apiTarget=all', { playerName : $('#playerSearch').val()}).done(function(data) {
			results = $.parseJSON(data);
			
			alert('Player: ' + results.username + '\nFaction: ' + results.faction + '\nRank: ' + results.rank + '\nShip: ' + results.ship + '\nLKS: ' + results.lastKnownPosition + '\nNotes: ' + results.notes);
		});
	} catch (e) {
		alert(e);
	}
};

$(document).ready(function() {
	$('#playerSearch').val('');
	
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
						$('#suggestions').css('display','none');
						spawnPlayerCard($(this).text());
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