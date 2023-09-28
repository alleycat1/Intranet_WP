/**
 * Listen to value changes into the setup wizard
 * and toggle steps when needed.
 */
 window.addEventListener('barn2_setup_wizard_changed', ( dispatchedEvent ) => {

	const layout = dispatchedEvent.detail.layout;

	const showStep = dispatchedEvent.detail.showStep
	const hideStep = dispatchedEvent.detail.hideStep

	if ( layout === 'grid' ) {
		showStep( 'grid' )
		hideStep( 'table' )
		hideStep( 'filters' )
	}

	if ( layout === 'table' ) {
		showStep( 'table' )
		showStep( 'filters' )
		hideStep( 'grid' )
	}

}, false);
