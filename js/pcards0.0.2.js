var playerRegistry = new Array();

function spawnCard(player) {
	var cardName = 'playerCard_' + player.name.toString().toLowerCase();
	var cardSelector = '#' + cardName;
	var exists = false;
	
	console.log("Player card id is " + cardName);
	
	//Create card if it does not already exist.
	if (!$(cardSelector).length) {
		console.log('Card for ' + player.name + ' does not exist. Creating it.');
		
		$('#cardDisplay').append('\
			<div id="' + cardName + '" class="playerCardContainer"></div>\
		');	
	} else {
		//Clear card
		console.log("Clearing card.");
		$(cardSelector).html('');
		exists = true;
	}
	
	//Wanted Advisories
	if (player.wantedAdvisories) {
		console.log("Player is wanted. Spawning advisories.");
		
		var output = '<div class="advisory wantedAdvisory"><p>';
		
		$.each(player.wantedAdvisories,function(key,val) {
			output += 'ADVISORY: Wanted by [' + (val.creatorAbbreviatedName || val.creator) + ']';
			
			if (val.reason) {
				output += '; Reason: "' + val.reason + '"<br>';
			} else {
				output += '<br>';
			}
		});
		
		output += '</p></div>';
		
		$('#playerCard_test').append(output);
	}
	
	//Bounty Advisories
	if (player.bounties) {
		console.log("Player has bounties. Spawning advisories.")
		
		var output = '<div class="advisory bountyAdvisory"><p>';
		
		$.each(player.bounties,function(key,val) {
			output += 'ADVISORY: Bounty of $' + val.amount + ' posted by ' + val.creator;
			
			if (val.reason) {
				output += '; Reason: "' + val.reason + '"<br>';
			} else {
				output += '<br>';
			}
		});
		
		output += '</p></div>';
		
		$(cardSelector).append(output);
	}
	
	//Faction Conflict Advisory
	//Todo
	/*if (user && user.faction && player.faction && user.faction.allegianceID != player.faction.allegianceID) {
		$(cardSelector).append();
	}*/
	
	//Power Conflict Advisory
	//Todo
	/*if (user && user.power && player.power && user.power.allegianceID != player.power.allegianceID) {
		$(cardSelector).append();
	}*/
	
	//Basic Information
	console.log("Spawning basic player information.");
	
	var title = player.name
	if (player.power) {
		console.log("Player is in a power.");
		title += ' <span class="textBlob" style="background-color:' + player.power.backgroundColor + ';color:' + player.power.textColor + '">(' + player.power.name + ')</span>';
	}
	if (player.faction) {
		console.log("Player is in a faction.");
		title += ' <span class="textBlob" style="background-color:' + player.faction.backgroundColor + ';color:' + player.faction.textColor + '">[' + (player.faction.abbreviatedName || player.faction.name) + ']</span>';
	}
	
	var notes = '';
	alert
	if (player.notes) {
		console.log("There are notes about the player.");
		notes = '\
			<div class="playerCardNotes">\
				<p>' + player.notes + '</p>\
			</div>\
		';
	}
	
	var output = '\
		<div class="playerCardBody">\
			<div class="playerCardInfo">\
				<div class="playerCardTitle">\
					<span class="text">' + title + '</span>\
				</div>\
				\
				<span class="text">' + player.rank + '/' + player.ship + '</span>\
			</div>\
			\
			<div class="playerCardLastSeen">\
				<span class="text">Last seen in ' + /*player.spot.system +*/ '<br> on ' + /*player.spot.date +*/ ' @ ' + /*player.spot.time +*/ ' UTC.</span>\
			</div>\
			' + notes + '\
		</div>\
	';
	
	$(cardSelector).append(output);
	
	if (!exists) {
		$(cardSelector).slideDown();
	}
}

function createCard(name) {
	var name = name.toString().toLowerCase();
	
	$.when(
		$.post("api.php", { 'apiTarget' : 'comprehensive', 'name' : name }).done(function(data) {
			if (!playerRegistry[name]) {
				playerRegistry[name] = new Object();
			}
			
			data = $.parseJSON(data)
			
			if (data.error) {
				console.log('An error occured during api request. Response: ' + JSON.stringify(data));
				playerRegistry[name] = data;
			} else {
				console.log('API query successful.');
				console.log(JSON.stringify(data));
				playerRegistry[data.name.toString().toLowerCase()] = data;
			}
		}).fail(function() {
			console.log('Tried to request invalid resource.');
			playerRegistry[name].error = 'invalidResource';
		})
	).then(function() {		
		if (playerRegistry[name].error) {
			if (playerRegistry[name].error == 'missingPlayer') {
				alert('This player is not in the database.');
			} else {
				alert('An unexpected error (' + playerRegistry[name].error + ') occured.');
			}
		} else {
			console.log("No errors occured. Spawning card.");
			spawnCard(playerRegistry[name]);
		}
	});
}