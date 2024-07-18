
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "https://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd"> 
<html xmlns="https://www.w3.org/1999/xhtml"> 
<head> 
	<title>Leaflet Location Picker Demo</title> 
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" /> 

	<link rel="stylesheet" href="{{ asset('maps/leaflet.css') }}" />

	<link rel="stylesheet" href="{{ asset('maps/leaflet-locationpicker.css') }}" />
	<link rel="stylesheet" href="{{ asset('maps/style.css') }}" />
</head>

<body>
<h3><a href="../"><big>◄</big> Leaflet Location Picker</a></h3>

<h4>Simple Example: <em></em></h4>

<form id="insert">
	<label>Insert new geographic location:</label><br />
	<input class="geolocs" type="text" value="" size="20" />
<pre>
$('.geolocs').leafletLocationPicker();
</pre>
</form>

<form id="default">
	Change default geographic location: <br />
	<input class="geolocs" type="text" value="17.9787,81.0352" size="20" />
</form>

<form id="format">
	Custom location format: <br />
	<input id="geoloc2" type="text" value="" size="20" /> <br />
<pre>
$('#geoloc2').leafletLocationPicker({
	locationFormat: '{lat}@{lng}#WGS84',
	position: 'bottomleft'
});
</pre>
</form>

<form id="callback">
	Custom callback: <br />
	<input id="geoloc4" type="text" value="" size="20" />
	<br /><br />
	<i>Value from callback:</i><br />
	<em style="color:blue"></em><br />
<pre>
$('#geoloc4').leafletLocationPicker(function(e) {
	$(this).siblings('em').text(e.location);
});
</pre>
</form>

<form id="events">
	Events: <em style="color:red"></em><br />
	<input id="geoloc3" type="text" value="" size="20" />
	<br />
	<br /><input id="geolat" type="text" value="" size="20" />
	<br /><input id="geolng" type="text" value="" size="20" />
	<br /><i>string location</i><br />
<pre>
$('#geoloc3').leafletLocationPicker({
	locationSep: ' - '
})
.on('show', function(e) {
	$(this).siblings('em').text('click on map for insert the location');
})
.on('hide', function(e) {
	$(this).siblings('em').text('');
})
.on('changeLocation', function(e) {
	$(this)
	.siblings('#geolat').val( e.latlng.lat )
	.siblings('#geolng').val( e.latlng.lng )
	.siblings('i').text('"'+e.location+'"');
});
</pre>
</form>

<script src="{{ asset('maps/leaflet.js') }}"></script>
<script src="{{ asset('maps/jquery-2.1.0.min.js') }}"></script>

<form id="fixedContAlwaysOpen">
	Fixed container and always open map: <br />
	<div  style="min-height: 140;min-width: 200;">
			<input id="geoloc5" type="text" value="" size="20" />
			<div id="fixedMapCont" style="border: 1px solid black; min-height: 140;min-width: 200;"></div>
	</div>
<pre>
	$('#geoloc5').leafletLocationPicker({
			alwaysOpen: true,
			mapContainer: "#fixedMapCont"
	});
</pre>
</form>


<script src="{{ asset('maps/leaflet2.js') }}"></script>
<script src="{{ asset('maps/jquery-2.2.4.min.js') }}" integrity="sha256-BbhdlvQf/xTY9gja0Dq3HiwQF8LaCRTXxZKRutelT44=" crossorigin="anonymous"></script>
<script src="{{ asset('maps/leaflet-locationpicker.js') }}"></script>
<script>

//multiple istances
$('.geolocs').leafletLocationPicker();

//custom location format
$('#geoloc2').leafletLocationPicker({
	locationFormat: '{lat}@{lng}#WGS84',
	position: 'bottomleft'
});

//events
$('#geoloc3').leafletLocationPicker({
		locationSep: ' - '
	})
	.on('show', function(e) {
		$(this).siblings('em').text('click on map for insert the location');
	})
	.on('hide', function(e) {
		$(this).siblings('em').text('');
	})
	.on('changeLocation', function(e) {
		$(this)
		.siblings('#geolat').val( e.latlng.lat )
		.siblings('#geolng').val( e.latlng.lng )
		.siblings('i').text('"'+e.location+'"');
	});

//callback
$('#geoloc4').leafletLocationPicker(function(e) {
	$(this).siblings('em').text(e.location);
});

//fix n alwaysOpen
$('#geoloc5').leafletLocationPicker({
		alwaysOpen: true,
		mapContainer: "#fixedMapCont"
});


</script>

</body>
</html>
