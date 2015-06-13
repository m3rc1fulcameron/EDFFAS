var playerCards = new Array();

var cardSpawnLockout = false;

function spawnPlayerCard(username) {
	if (!$('#playerCard_' + username).length) {
		$('#results').append('<div id="playerCard_' + username + '" class="playerCardContainer"></div>')
	}
	
	$('#playerCard_' + username).html('');
	
	if (playerCards[username].advisories.length != 0) {
		try {			
			var output = '<div class="playerCardAdvisory"><p>';
			
			$.each(playerCards[username].advisories,function(key,val) {
				output += 'ADVISORY: WANTED BY [' + val.factionAbbreviation + ']';
				
				if (val.reason == '') {
					output += '<br>';
				} else {
					output += '; REASON: ' + val.reason + '<br>';
				}
			});
			
			output += '</p></div>';
			
			console.log('Output is "' + output + '"');
		} catch (e) {
			console.log(e);
			return false;
		}
		
		$('#playerCard_' + username).append(output);
	}
	
	if ((playerCards[username].bounties.length != 0)) {
		try {			
			var output = '<div class="playerCardBounty"><p>';
			
			$.each(playerCards[username].bounties,function(key,val) {
				output += 'ADVISORY: BOUNTY OF ' + val.amount + 'cR POSTED BY <a class="playerCardSpawnButton" href="#">' + val.username + '</a>';
				
				if (val.reason == '') {
					output += '<br>';
				} else {
					output += '; REASON: ' + val.reason + '<br>';
				}
			});
			
			output += '</p></div>';
			
			console.log('Output is "' + output + '"');
		} catch (e) {
			console.log(e);
			return false;
		}
		
		$('#playerCard_' + username).append(output);
	}
	
	
	var factionOutput = '';
	if (playerCards[username].factionAbbreviation != null) {
		factionOutput = ' <span class="textBlob" style="background-color:' + playerCards[username].factionColor + ';">[' + playerCards[username].factionAbbreviation + ']</span>';
	} else if (playerCards[username].factionName != null) {
		factionOutput = ' <span class="textBlob" style="background-color:' + playerCards[username].factionColor + ';">[' + playerCards[username].factionName + ']</span>';
	}
	
	var powerOutput = '';
	if (playerCards[username].powerName != null) {
		powerOutput = ' <span class="textBlob" style="background-color:' + playerCards[username].powerColor + ';">(' + playerCards[username].powerName + ')</span>'
	}
	
	$('#playerCard_' + username).append('\
		<div class="playerCardBody">\
			<div class="playerCardInfo">\
				<span class="text playerCardTitle">' + playerCards[username].username + powerOutput + factionOutput + '</span><br>\
				<span class="text">' + rankTable[playerCards[username].rank] + '/' + playerCards[username].ship + '</span>\
			</div>\
			\
			<div class="playerCardLastSeen">\
				<span class="text">Last Seen in ' + playerCards[username].lastKnownPosition + '<br>on 00/00/00 @ 00:00 GMT.</span>\
			</div>\
			\
			<div class="playerCardNotes">\
				<p>\
					' + playerCards[username].notes + '\
				</p>\
			</div>\
		</div>\
	');
	
	$('.playerCardSpawnButton').click(function() {
		updatePlayerCard($(this).html());
	})
}

function updatePlayerCard (username) {	
	if (cardSpawnLockout == true) {
		return false;
	} else {
		cardSpawnLockout = true;
	}
	
	if (!playerCards[username]) {
		playerCards[username] = new Object();
	}
	
	$.when(
		//Basic information
		$.post('api.php?apiTarget=all', { playerName : username}).done(function(data) {		
			var data = $.parseJSON(data);
			
			if (data && !data.error) {
				$.extend(playerCards[username],data);
			} else {
				$.extend(playerCards[username],{'error' : true})
				
				if (data.error) {
					console.log(data.error);
				}
			}
		}),
		
		//Bounties
		playerCards[username].bounties = [{username : 'Bob', amount : 100, reason : 'This asshole was pirating my friends!'},{username : 'Bill', amount : 100000, reason : 'Just \'cuz.'}],
		
		//Wanted Status
		$.post('api.php?apiTarget=wanted', { playerName : username}).done(function(data) {		
			var data = $.parseJSON(data);
			
			if (data && !data.error) {
				playerCards[username].advisories = data;
			} else {
				$.extend(playerCards[username],{'error' : true})
				
				if (data.error) {
					console.log(data.error);
				}
			}
		})
	).then(function() {
		console.log('Info: ' + JSON.stringify(playerCards[username]))
		
		if (playerCards[username].error) {
			alert('An error occured.');
		} else {
			spawnPlayerCard(username);
		}
	});
	
	cardSpawnLockout = false;
}