$(document).ready(function() {
	$('#playerSearch').val('');
	
	$('#playerSearch').keyup(_.debounce(function() {
		$.post('api.php', { playerName : $('#playerSearch').val()}).done(function(data) {
			try {
				list = '';
				
				$.each($.parseJSON(data),function(key,val) {
					list += '<div id=\"suggestionRow' + key + '\" class=\"suggestionRow text\">' + val + '</div>'
				});
				
				if (list !== '') {
					$('#suggestions').css('display','block');
					$('#suggestions').html(list);
					
					$('.suggestionRow').click(function() {
						$('#playerSearch').val($(this).text());
					});
				} else {
					$('#suggestions').css('display','none');
				}
			} catch (e) {
				$('#suggestions').css('display','none');
			}
		});
	}, 250));
});