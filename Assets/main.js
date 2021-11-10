jQuery(document).ready(function(){


});

/*
 * open before respond modal
 * is automatic replaced from resulting open modal
 * */
function showFullPageLoader ()
{
	removeFullPageLoader();
	$('<div id="fullPageLoader"><h2>Loading ...</h2></div>').appendTo( $('body') );
};

function removeFullPageLoader ()
{
	$('#fullPageLoader').remove();
}