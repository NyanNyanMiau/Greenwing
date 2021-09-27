jQuery(document).ready(function(){

	var dataStore = typeof localStorage === "object" && localStorage;
	if (!dataStore) return;
	var dataKey = 'openStates';

	var firstRun = true;
	$('.collapse').on('hidden.bs.collapse', function () {
		$(this).parent().removeClass('open');
		var s = JSON.parse(dataStore.getItem(dataKey)) || {};
		delete s[ this.id ];
		dataStore.setItem(dataKey, JSON.stringify(s));
		return false;
	});

	$('.collapse').on('shown.bs.collapse', function () {
		$(this).parent().addClass('open');
		if (firstRun) return;
		
		var s = JSON.parse(dataStore.getItem(dataKey)) || {};
		s[ this.id ] = true;
		dataStore.setItem(dataKey, JSON.stringify(s));
		return false;
	});

	var openStates = JSON.parse(dataStore.getItem(dataKey)) || {};
	for (var key in openStates) {
	    // skip loop if the property is from prototype
	    if (!openStates.hasOwnProperty(key)) continue;
	    if ( key.indexOf('project') > -1 ){
	    	$('#'+key).collapse('show');
	    }
	}
	window.setTimeout(function(){
		for (var key in openStates) {
		    // skip loop if the property is from prototype
		    if (!openStates.hasOwnProperty(key)) continue;
		    if ( key.indexOf('task') > -1 ){
		    	$('#'+key).collapse('show');
		    }
		}
		firstRun = false;
	}, 300)
})