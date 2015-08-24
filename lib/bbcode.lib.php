<?php
	function htmlFromBBCode($text) {
		//Bold
		$text = preg_replace('/\[b\]/i','<span style="font-weight:bold;">',$text);
		$text = preg_replace('/\[\/b\]/i','</span>',$text);
		//Italicized
		$text = preg_replace('/\[i\]/i','<span style="font-style:italic;">',$text);
		$text = preg_replace('/\[\/i\]/i','</span>',$text);
		//Underlined
		$text = preg_replace('/\[u\]/i','<span style="text-decoration:underline;">',$text);
		$text = preg_replace('/\[\/u\]/i','</span>',$text);
		//Strikethrough
		$text = preg_replace('/\[s\]/i','<span style="text-decoration:line-through;">',$text);
		$text = preg_replace('/\[\/s\]/i','</span>',$text);
		//Url Simple
		$text = preg_replace('/\[url\](.*?)\[\/url\]/i','<a href="${1}" title="${1}">${1}</a>',$text);
		//Url Complex
		$text = preg_replace('/\[url=(.*?)\](.*?)\[\/url\]/i','<a href="${1}" title="${2}">${2}</a>',$text);
		//Image
		$text = preg_replace('/\[img\](.*?)\[\/img\]/i','<img src="${1}" title="${1}">',$text);
		//Quote
		//Todo
		//Code
		$text = preg_replace('/\[code\](.*?)\[\/code\]/i','<pre>${1}</pre>',$text);
		//Size
		$text = preg_replace('/\[style size=(.*?)\](.*?)\[\/style\]/i','<span style="font-size:${1};">${2}</span>',$text);
		//Color
		$text = preg_replace('/\[style color=(.*?)\](.*?)\[\/style\]/i','<span style="color:${1};">${2}</span>',$text);
		$text = preg_replace('/\[color=(.*?)\](.*?)\[\/color\]/i','<span style="color:${1};">${2}</span>',$text);
		//List
		$text = preg_replace('/\[\*\](.*?)(?=\s?\[)/i','<li>${1}</li>',$text);
		$text = preg_replace('/\[list\]/i','<ul>',$text);
		$text = preg_replace('/\[\/list\]/i','</ul>',$text);
		//Table
		$text = preg_replace('/\[table\]/i','<table>',$text);
		$text = preg_replace('/\[\/table\]/i','</table>',$text);
		$text = preg_replace('/\[tr\]/i','<tr>',$text);
		$text = preg_replace('/\[\/tr\]/i','</tr>',$text);
		$text = preg_replace('/\[td\]/i','<td>',$text);
		$text = preg_replace('/\[\/td\]/i','</td>',$text);
		
		return $text;
	}
	
	if (isset($_GET['t'])) {
		print(htmlFromBBCode(htmlspecialchars('[s][u][i][b][color=#FF0000]]Blegh[/color][/b][/i][/u][/s]',ENT_QUOTES)));
	}
?>