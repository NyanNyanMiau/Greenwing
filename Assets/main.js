jQuery(document).ready(function(){

	// should catch all norm forms not modal ones
	$(document).on('submit', 'form', function() {
	    var btnEl = $(this).find('[type="submit"]').prop('disabled', true);
	    var l = $('<i class="fa fa-spinner fa-pulse me-2"></i> ');
	    if (btnEl.is('input')) {
	    	btnEl.val('loading ...');
	    } else {
	    	btnEl.prepend(l);
	    }
	});

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