rankTable = new Array(
	'Unknown',
	'Harmless',
	'Mostly Harmless',
	'Novice',
	'Competent',
	'Expert',
	'Master',
	'Dangerous',
	'Deadly',
	'Elite'
);

function populatePlayerCard(username,playerInformation) {	
	$('#playerCard_' + username + ' .playerCardBody').html(playerInformation);
}

function populatePlayerAdvisories(username,playerAdvisories) {	
	$('#playerCard_' + username + ' .playerCardAdvisory').html(playerAdvisories);
}

function getPlayerInformation(username) {
	$.post('api.php?apiTarget=all', { playerName : username}).done(function(data) {		
		data = $.parseJSON(data);
		
		if (data) {
			output = '\
				<div class="playerCardInfo">\
					<span class="text playerCardTitle">' + data.username + ' ' + data.factionAbbreviation + '</span><br>\
					<span class="text">' + rankTable[data.rank] + '/' + data.ship + '</span>\
				</div>\
				\
				<div class="playerCardLastSeen">\
					<span class="text">Last Seen in ' + data.lastKnownPosition + '<br>on 00/00/00 @ 00:00 GMT.</span>\
				</div>\
				\
				<div class="playerCardNotes">\
					<p>\
						' + data.notes + '\
					</p>\
				</div>\
			';
			
			populatePlayerCard(username,output);
		} else {
			alert("User Not Found");
		}
	});
}

function getPlayerWantedStatus(username) {
	$.post('api.php?apiTarget=wanted', { playerName : username}).done(function(data) {
		data = $.parseJSON(data);
		
		output = '';
		
		try {
			output = '<p>';
			
			$.each(data,function(key,val) {
				output += 'ADVISORY: Wanted by [' + val.factionAbbreviation + ']<br>';
			});
			
			output += '</p>';
		} catch (e) {
			output = '';
		}
		
		populatePlayerAdvisories(username,output);
	});
}

function updatePlayerCard(username) {
	if (!$('#playerCard_' + username).length) {		
		$('#results').append('\
				<div id="playerCard_' + username + '" class="playerCardContainer">\
					<div class="playerCardAdvisory"></div>\
					<div class="playerCardBody"></div>\
					<div class="playerCardControls">\
						<button class="playerCardClose" title="Delete Card">X</button>\
					</div>\
				</div>\
		');
		
		//$('#playerCard_' + username).slideDown();
		
		/*
		$('#playerCard_' + username +' .playerCardControls .playerCardClose').click(function() {
			$(this).slideUp(400,function() {
				$(this).parent().remove();
			});
		})
		*/
		
		$('#playerCard_' + username +' .playerCardControls .playerCardClose').click(function() {
			$(this).parent().parent().remove();
		});
	}
	
	getPlayerInformation(username);
	getPlayerWantedStatus(username);
};