var playerRegistry = new Array();

function spawnCard(player) {
	var cardName = '#playerCard_' + player.name;
	
	//Create card if it does not already exist.
	if (!$(cardName).length) {
		console.log('Card for ' + player.name + ' does not exist. Creating it.');
		
		$('#cardDisplay').append('\
			<div id="playerCard_' + player.name + '"></div>\
		');	
	}
	
	//Wanted Advisories
	if (player.wantedAdvisories) {
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
		
		$(cardName).append(output);
	}
	
	//Bounty Advisories
	if (player.bounties) {
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
		
		$(cardName).append(output);
	}
	
	//Faction Conflict Advisory
	//Todo
	/*if (user && user.faction && player.faction && user.faction.allegianceID != player.faction.allegianceID) {
		$(cardName).append();
	}*/
	
	//Power Conflict Advisory
	//Todo
	/*if (user && user.power && player.power && user.power.allegianceID != player.power.allegianceID) {
		$(cardName).append();
	}*/
	
	//Basic Information
	var title = player.name
	if (player.power) {
		title += ' <span class="textBlob" style="background-color:' + player.power.backgroundColor + ';color:' + player.power.textColor + '">(' + player.power.name + ')</span>';
	}
	if (player.faction) {
		title += ' <span class="textBlob" style="background-color:' + player.faction.backgroundColor + ';color:' + player.faction.textColor + '">[' + player.faction.name + ']</span>';
	}
	
	var notes = '';
	alert
	if (player.notes) {
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
	
	$(cardName).append(output);
}

function createCard(name) {
	$.when(
		$.post("api.php", { 'apiTarget' : 'comprehensive', 'name' : name }).done(function(data) {
			if (!playerRegistry[name]) {
				playerRegistry[name] = new Object();
			}
			
			
			if (data.error) {
				console.log('An error occured during api request. Response: ' + data);
				playerRegistry[name].error = $.parseJSON(data.error);
			} else {
				console.log('API query successful.');
				data = $.parseJSON(data);
				console.log(JSON.stringify(data));
				playerRegistry[data.name] = data;
			}
		}).fail(function() {
			console.log('Tried to request invalid resource.');
			playerRegistry[name].error = 'invalidResource';
		})
	).then(function() {
		if (playerRegistry[name].error) {
			if (playerRegistry[name].error = 'missingPlayer') {
				alert('This player is not in the database.');
			} else {
				alert('An unexpected error (' + playerRegistry[name].error + ') occured.');
			}
		} else {
			spawnCard(playerRegistry[name]);
		}
	});
}

$(document).ready(function() {
	createCard('test');
})