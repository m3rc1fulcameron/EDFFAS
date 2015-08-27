var playerRegistry = new Array();

function spawnCard(player) {
	var cardName = 'playerCard_' + player.name.toString().toLowerCase();
	var cardSelector = '#' + cardName;
	var exists = false;
	
	console.log("Player card id is " + cardName);
	
	//Create card if it does not already exist.
	if (!$(cardSelector).length) {		
		$('#cardDisplay').append('\
			<div id="' + cardName + '" class="playerCardContainer"></div>\
		');
		
		$(cardSelector).html('\
			<div class="playerCardBody"></div>\
			<div class="playerCardControls">\
				<a href="#" title="Refresh Card" class="playerCardRefreshButton"><img src="img/refresh.png"></a>\
				<a href="#" title="Dismiss Card" class="playerCardDismissButton"><img src="img/trash.png"></a>\
			</div>\
		');
		
		$(cardSelector + ' .playerCardControls .playerCardDismissButton').click(function() {
			$(this).parent().children().off('click');
			$(this).parent().parent().slideUp(function() {
				$(this).remove();
			});
			
			return false;
		});
		
		//Refresh Card
		$(cardSelector + ' .playerCardControls .playerCardRefreshButton').click(function() {
			createCard(player.name);
			return false;
		});
	} else {
		exists = true;
	}
	
	//Wanted Advisories
	if (player.wantedAdvisories) {
		if (!$(cardSelector + ' .wantedAdvisory').length) {
			$(cardSelector).prepend('<div class="advisory wantedAdvisory"></div>');
		}
		
		var output = '<p>';
		
		$.each(player.wantedAdvisories,function(key,val) {
			output += 'ADVISORY: Wanted by [' + (val.creatorAbbreviatedName || val.creator) + ']';
			
			if (val.reason) {
				output += '; Reason: "' + val.reason + '"<br>';
			} else {
				output += '<br>';
			}
		});
		
		output += '</p>';
		
		$(cardSelector + ' .wantedAdvisory').html(output);
	} else if ($(cardSelector + ' .wantedAdvisory').length) {
		$(cardSelector + ' .wantedAdvisory').remove();
	}
	
	//Bounty Advisories
	if (player.bounties) {
		if (!$(cardSelector + ' .bountyAdvisory').length) {
			$(cardSelector).prepend('<div class="advisory bountyAdvisory"></div>');
		}
		
		var output = '<p>';
		
		$.each(player.bounties,function(key,val) {
			output += 'ADVISORY: Bounty of $' + Number(val.amount).toLocaleString('en') + ' posted by ' + val.creator;
			
			if (val.reason) {
				output += '; Reason: "' + val.reason + '"<br>';
			} else {
				output += '<br>';
			}
		});
		
		output += '</p>';
		
		$(cardSelector + ' .bountyAdvisory').html(output);
	} else if ($(cardSelector + ' .bountyAdvisory').length) {
		$(cardSelector + ' .bountyAdvisory').remove();
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
	
	//Basic Info
	var title = player.name
	if (player.power) {
		title += ' <span class="textBlob" style="background-color:' + player.power.backgroundColor + ';color:' + player.power.textColor + '">(' + player.power.name + ')</span>';
	}
	if (player.faction) {
		title += ' <span class="textBlob" style="background-color:' + player.faction.backgroundColor + ';color:' + player.faction.textColor + '">[' + (player.faction.abbreviatedName || player.faction.name) + ']</span>';
	}
	
	var notes = '';
	
	if (player.notes) {
		notes = '\
			<div class="playerCardNotes">\
				<p>&quot;' + player.notes + '&quot;</p>\
			</div>\
		';
	}
	
	//Body of the card with the basic info.
	var output = '\
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
	';
	
	//Add content to card
	$(cardSelector + ' .playerCardBody').html(output);
	
	//Play animation if the card does not exist.
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