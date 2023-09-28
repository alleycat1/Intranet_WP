/**
 * Get ticket list
 */
function wpsc_get_ticket_list(is_humbargar = false) {

	supportcandy.current_section = 'ticket-list';

	if (is_humbargar) {
		wpsc_toggle_humbargar();
	}

	if (wpsc_is_description_text()) {
		if ( ! confirm( supportcandy.translations.warning_message )) {
			return;
		} else {
			var is_tinymce = (typeof tinyMCE != "undefined") && tinyMCE.activeEditor && ! tinyMCE.activeEditor.isHidden();
			if (is_tinymce && tinymce.get( 'description' )) {
				var description = tinyMCE.get( 'description' ).setContent( '' );
			} else {
				var description = jQuery( '#description' ).val( '' );
			}
			ticket_id = jQuery('#wpsc-current-ticket').val();
			wpsc_clear_saved_draft_reply( ticket_id );
		}
	}

	var id = supportcandy.current_ticket_id;
	if (id) {
		delete supportcandy.current_ticket_id;
		wpsc_get_individual_ticket( id );
		return;
	}

	// set flag to differenciate between ticket list and individual ticket.
	supportcandy.ticketListIsIndividual = false;

	jQuery( '.wpsc-tickets-nav, .wpsc-humbargar-menu-item' ).removeClass( 'active' );
	jQuery( '.wpsc-tickets-nav.ticket-list, .wpsc-humbargar-menu-item.ticket-list' ).addClass( 'active' );
	jQuery( '.wpsc-humbargar-title' ).html( supportcandy.humbargar_titles.ticket_list );

	window.history.replaceState( {}, null, 'admin.php?page=wpsc-tickets&section=ticket-list' );
	jQuery( '.wpsc-body' ).html( supportcandy.loader_html );

	wpsc_scroll_top();

	var data = {
		action: 'wpsc_get_ticket_list',
		_ajax_nonce: supportcandy.nonce
	};
	if (typeof supportcandy.ticketList != 'undefined' && typeof supportcandy.ticketList.filters != 'undefined') {
		data.filters = supportcandy.ticketList.filters;
	}
	jQuery.post(
		supportcandy.ajax_url,
		data,
		function (response) {
			jQuery( '.wpsc-body' ).html( response );
			wpsc_reset_responsive_style();
		}
	);
}

/**
 * Get individual ticket
 */
function wpsc_get_individual_ticket(id) {

	jQuery( '.wpsc-tickets-nav, .wpsc-humbargar-menu-item' ).removeClass( 'active' );
	jQuery( '.wpsc-tickets-nav.ticket-list, .wpsc-humbargar-menu-item.ticket-list' ).addClass( 'active' );
	jQuery( '.wpsc-humbargar-title' ).html( supportcandy.humbargar_titles.ticket_list );

	window.history.replaceState( {}, null, 'admin.php?page=wpsc-tickets&section=ticket-list&id=' + id );
	jQuery( '.wpsc-body' ).html( supportcandy.loader_html );

	wpsc_scroll_top();

	// set flag to differenciate between ticket list and individual ticket.
	supportcandy.ticketListIsIndividual = true;

	var data = {
		action: 'wpsc_get_individual_ticket',
		ticket_id: id,
	};
	jQuery.post(
		supportcandy.ajax_url,
		data,
		function (response) {
			jQuery( '.wpsc-body' ).html( response );
			wpsc_reset_responsive_style();
		}
	);
}

/**
 * Get create ticket form
 */
function wpsc_get_ticket_form(is_humbargar = false) {

	supportcandy.current_section = 'new-ticket';

	if (is_humbargar) {
		wpsc_toggle_humbargar();
	}

	if ( wpsc_is_description_text() ) {
		if ( confirm( supportcandy.translations.warning_message ) ){
			current_ticket = jQuery('#wpsc-current-ticket').val();
			wpsc_clear_saved_draft_reply( current_ticket );
		} else { 
			return 
		}
	}

	jQuery( '.wpsc-tickets-nav, .wpsc-humbargar-menu-item' ).removeClass( 'active' );
	jQuery( '.wpsc-tickets-nav.new-ticket, .wpsc-humbargar-menu-item.new-ticket' ).addClass( 'active' );
	jQuery( '.wpsc-humbargar-title' ).html( supportcandy.humbargar_titles.new_ticket );

	// set url.
	var url           = new URL( window.location.href );
	var search_params = url.searchParams;
	search_params.set( 'section', 'new-ticket' );
	search_params.delete('id');
	url.search = search_params.toString();
	window.history.replaceState( {}, null, url.toString() );
	jQuery( '.wpsc-body' ).html( supportcandy.loader_html );

	wpsc_scroll_top();

	var data = {
		action: 'wpsc_get_ticket_form',
		_ajax_nonce: supportcandy.nonce
	};
	search_params.forEach(
		function (value, key) {
			data[key] = value;
		}
	);
	jQuery.post(
		supportcandy.ajax_url,
		data,
		function (response) {
			jQuery( '.wpsc-body' ).html( response );
			wpsc_reset_responsive_style();
		}
	);
}

/**
 * Get agent settings
 */
function wpsc_get_user_profile(is_humbargar = false) {

	supportcandy.current_section = 'my-profile';

	if (is_humbargar) {
		wpsc_toggle_humbargar();
	}

	jQuery( '.wpsc-tickets-nav, .wpsc-humbargar-menu-item' ).removeClass( 'active' );
	jQuery( '.wpsc-tickets-nav.my-profile, .wpsc-humbargar-menu-item.my-profile' ).addClass( 'active' );
	jQuery( '.wpsc-humbargar-title' ).html( supportcandy.humbargar_titles.my_profile );

	window.history.replaceState( {}, null, 'admin.php?page=wpsc-tickets&section=my-profile' );
	jQuery( '.wpsc-body' ).html( supportcandy.loader_html );

	wpsc_scroll_top();

	var data = { action: 'wpsc_get_user_profile' };
	jQuery.post(
		supportcandy.ajax_url,
		data,
		function (response) {
			jQuery( '.wpsc-body' ).html( response );
			wpsc_reset_responsive_style();
		}
	);
}

/**
 * Get agent settings
 */
function wpsc_get_agent_profile(is_humbargar = false) {

	supportcandy.current_section = 'agent-profile';

	if (is_humbargar) {
		wpsc_toggle_humbargar();
	}

	jQuery( '.wpsc-tickets-nav, .wpsc-humbargar-menu-item' ).removeClass( 'active' );
	jQuery( '.wpsc-tickets-nav.agent-profile, .wpsc-humbargar-menu-item.agent-profile' ).addClass( 'active' );
	jQuery( '.wpsc-humbargar-title' ).html( supportcandy.humbargar_titles.agent_profile );

	window.history.replaceState( {}, null, 'admin.php?page=wpsc-tickets&section=agent-profile' );
	jQuery( '.wpsc-body' ).html( supportcandy.loader_html );

	wpsc_scroll_top();

	var data = { action: 'wpsc_get_agent_profile' };
	jQuery.post(
		supportcandy.ajax_url,
		data,
		function (response) {
			jQuery( '.wpsc-body' ).html( response );
			wpsc_reset_responsive_style();
			jQuery( '.wpsc-ap-nav.general' ).trigger( 'click' );
		}
	);
}

/**
 * Show other actions popover
 */
function wpsc_show_tl_other_actions() {

	jQuery( '#wpsc-more-actions' ).gpopover( 'show' );
}

/**
 * Get agent list
 */
function wpsc_get_agent_list(is_humbargar = false) {

	if (is_humbargar) {
		wpsc_toggle_humbargar();
	}

	var id = supportcandy.current_id;
	if (id) {
		delete supportcandy.current_id;
		wpsc_get_individual_agent( id );
		return;
	}

	jQuery( '.wpsc-setting-nav, .wpsc-humbargar-menu-item' ).removeClass( 'active' );
	jQuery( '.wpsc-setting-nav.agents, .wpsc-humbargar-menu-item.agents' ).addClass( 'active' );
	jQuery( '.wpsc-humbargar-title' ).html( supportcandy.humbargar_titles.agents );

	window.history.replaceState( {}, null, 'admin.php?page=wpsc-support-agents&section=agents' );
	jQuery( '.wpsc-setting-body' ).html( supportcandy.loader_html );

	wpsc_scroll_top();

	var data = { action: 'wpsc_get_agent_list' };
	jQuery.post(
		supportcandy.ajax_url,
		data,
		function (response) {
			jQuery( '.wpsc-setting-body' ).html( response );
			wpsc_reset_responsive_style();
		}
	);
}

/**
 * Create an agent
 */
function wpsc_set_add_agent(el) {

	var user = jQuery( '.wpsc-frm-add-agent #wpsc-select-user-input' ).val();
	if ( ! user) {
		alert( supportcandy.translations.req_fields_missing );
		return;
	}
	var form     = jQuery( '.wpsc-frm-add-agent' )[0];
	var dataform = new FormData( form );

	var users = dataform.getAll( 'users[]' );
	if ( ! (users.length)) {
		alert( supportcandy.translations.req_fields_missing );
		return;
	}

	jQuery( '.wpsc-modal-footer button' ).attr( 'disabled', true );
	jQuery( el ).text( supportcandy.translations.please_wait );
	jQuery.ajax(
		{
			url: supportcandy.ajax_url,
			type: 'POST',
			data: dataform,
			processData: false,
			contentType: false
		}
	).done(
		function (res) {
			wpsc_close_modal();
			wpsc_get_agent_list();
		}
	);
}

/**
 * Get edit agent modal popup
 */
function wpsc_get_edit_agent(id, _ajax_nonce) {

	wpsc_show_modal();
	var data = {
		action: 'wpsc_get_edit_agent',
		id,
		_ajax_nonce
	};
	jQuery.post(
		supportcandy.ajax_url,
		data,
		function (response) {

			// Set to modal.
			jQuery( '.wpsc-modal-header' ).text( response.title );
			jQuery( '.wpsc-modal-body' ).html( response.body );
			jQuery( '.wpsc-modal-footer' ).html( response.footer );
			// Display modal.
			wpsc_show_modal_inner_container();
		}
	);
}

/**
 * Submit changes made to an agent
 */
function wpsc_set_edit_agent(el) {

	var form     = jQuery( '.wpsc-frm-edit-agent' )[0];
	var dataform = new FormData( form );
	jQuery( '.wpsc-modal-footer button' ).attr( 'disabled', true );
	jQuery( el ).text( supportcandy.translations.please_wait );
	jQuery.ajax(
		{
			url: supportcandy.ajax_url,
			type: 'POST',
			data: dataform,
			processData: false,
			contentType: false
		}
	).done(
		function (res) {
			wpsc_close_modal();
			wpsc_get_agent_list();
		}
	);
}

/**
 * Get delete agent modal popup
 */
function wpsc_get_delete_agent(id, nonce) {

	var flag = confirm( supportcandy.translations.confirm );
	if ( ! flag) {
		return;
	}

	var data = {
		action: 'wpsc_delete_agent',
		id,
		_ajax_nonce: nonce
	};
	jQuery.post(
		supportcandy.ajax_url,
		data,
		function (res) {
			wpsc_get_agent_list();
		}
	);
}

/**
 * Submit delete an agent request
 */
function wpsc_set_delete_agent(el) {

	jQuery( '.wpsc-modal-footer button' ).attr( 'disabled', true );
	jQuery( el ).text( supportcandy.translations.please_wait );
	setTimeout(
		function () {
			wpsc_close_modal();
			wpsc_get_agent_list();
		},
		500
	);
}

/**
 * Get agent roles
 */
function wpsc_get_agent_roles(is_humbargar = false) {

	if (is_humbargar) {
		wpsc_toggle_humbargar();
	}

	jQuery( '.wpsc-setting-nav, .wpsc-humbargar-menu-item' ).removeClass( 'active' );
	jQuery( '.wpsc-setting-nav.agent-roles, .wpsc-humbargar-menu-item.agent-roles' ).addClass( 'active' );
	jQuery( '.wpsc-humbargar-title' ).html( supportcandy.humbargar_titles.agent_roles );

	window.history.replaceState( {}, null, 'admin.php?page=wpsc-support-agents&section=agent-roles' );
	jQuery( '.wpsc-setting-body' ).html( supportcandy.loader_html );

	wpsc_scroll_top();

	var data = { action: 'wpsc_get_agent_roles' };
	jQuery.post(
		supportcandy.ajax_url,
		data,
		function (response) {
			jQuery( '.wpsc-setting-body' ).html( response );
			wpsc_reset_responsive_style();
		}
	);
}

/**
 * Create an agent
 */
function wpsc_set_add_agent_role(el) {

	var label = jQuery( '.frm-add-agent-role #label' ).val().trim();
	if ( ! label) {
		alert( supportcandy.translations.req_fields_missing );
		return;
	}
	jQuery('#wpsc_add_agent_role').DataTable().search('').draw();
	var form     = jQuery( '.frm-add-agent-role' )[0];
	var dataform = new FormData( form );
	jQuery( '.wpsc-modal-footer button' ).attr( 'disabled', true );
	jQuery( el ).text( supportcandy.translations.please_wait );
	jQuery.ajax(
		{
			url: supportcandy.ajax_url,
			type: 'POST',
			data: dataform,
			processData: false,
			contentType: false
		}
	).done(
		function (res) {
	
			wpsc_get_agent_roles();
		}
	);
}

/**
 * Get clone agent role
 */
function wpsc_get_clone_agent_role(id, nonce) {

	jQuery( '.wpsc-setting-section-body' ).html( supportcandy.loader_html );
	var data = { action: 'wpsc_get_clone_agent_role', id, _ajax_nonce: nonce };
	jQuery.post(
		supportcandy.ajax_url,
		data,
		function (response) {
			wpsc_get_agent_roles();
		}
	);
}

/**
 * Get edit agent roles
 */
function wpsc_get_edit_agent_role(id, nonce) {

	jQuery( '.wpsc-setting-section-body' ).html( supportcandy.loader_html );
	var data = {
		action: 'wpsc_get_edit_agent_role',
		role_id: id,
		_ajax_nonce: nonce
	};
	jQuery.post(
		supportcandy.ajax_url,
		data,
		function (response) {

			jQuery( '.wpsc-setting-section-body' ).html( response );
		}
	);
}

/**
 * Submit changes made to an agent
 */
function wpsc_set_edit_agent_role(el) {

	var label = jQuery( '.frm-edit-agent-role #label' ).val().trim();
	if ( ! label) {
		alert( supportcandy.translations.req_fields_missing );
		return;
	}
	jQuery('#wpsc_edit_agent_role').DataTable().search('').draw();
	var form     = jQuery( '.frm-edit-agent-role' )[0];
	var dataform = new FormData( form );
	jQuery( '.wpsc-modal-footer button' ).attr( 'disabled', true );
	jQuery( el ).text( supportcandy.translations.please_wait );
	jQuery.ajax(
		{
			url: supportcandy.ajax_url,
			type: 'POST',
			data: dataform,
			processData: false,
			contentType: false
		}
	).done(
		function (res) {
			wpsc_get_agent_roles();
		}
	);
}

/**
 * Delete agent role
 */
function wpsc_delete_agent_role(id, nonce) {

	if ( ! confirm( supportcandy.translations.confirm )) {
		return;
	}

	var data = {
		action: 'wpsc_delete_agent_role',
		role_id: id,
		_ajax_nonce: nonce
	};
	jQuery.post(
		supportcandy.ajax_url,
		data,
		function (res) {
			wpsc_get_agent_roles();
		}
	).fail(
		function (xhr) {
			var response = JSON.parse( xhr.responseText );
			alert( response.data );
		}
	);
}

/**
 * Get general settings
 */
function wpsc_get_general_settings(is_humbargar = false) {

	if (is_humbargar) {
		wpsc_toggle_humbargar();
	}

	jQuery( '.wpsc-setting-nav, .wpsc-humbargar-menu-item' ).removeClass( 'active' );
	jQuery( '.wpsc-setting-nav.general-settings, .wpsc-humbargar-menu-item.general-settings' ).addClass( 'active' );
	jQuery( '.wpsc-humbargar-title' ).html( supportcandy.humbargar_titles.general_settings );

	if (supportcandy.current_section !== 'general-settings') {
		supportcandy.current_section = 'general-settings';
		supportcandy.current_tab     = 'general';
	}

	window.history.replaceState( {}, null, 'admin.php?page=wpsc-settings&section=' + supportcandy.current_section + '&tab=' + supportcandy.current_tab );
	jQuery( '.wpsc-setting-body' ).html( supportcandy.loader_html );

	wpsc_scroll_top();

	var data = {
		action: 'wpsc_get_general_settings',
		tab: supportcandy.current_tab
	};
	jQuery.post(
		supportcandy.ajax_url,
		data,
		function (response) {
			jQuery( '.wpsc-setting-body' ).html( response );
			wpsc_reset_responsive_style();
			jQuery( '.wpsc-setting-tab-container button.' + supportcandy.current_tab ).trigger( "click" );
		}
	);
}

/**
 * Load general tab ui
 */
function wpsc_get_gs_general() {

	supportcandy.current_tab = 'general';
	jQuery( '.wpsc-setting-tab-container button' ).removeClass( 'active' );
	jQuery( '.wpsc-setting-tab-container button.' + supportcandy.current_tab ).addClass( 'active' );

	window.history.replaceState( {}, null, 'admin.php?page=wpsc-settings&section=' + supportcandy.current_section + '&tab=' + supportcandy.current_tab );
	jQuery( '.wpsc-setting-section-body' ).html( supportcandy.loader_html );

	wpsc_scroll_top();

	var data = { action: 'wpsc_get_gs_general' };
	jQuery.post(
		supportcandy.ajax_url,
		data,
		function (response) {
			jQuery( '.wpsc-setting-section-body' ).html( response );
			wpsc_reset_responsive_style();
		}
	);
}

/**
 * Save general settings
 */
function wpsc_set_gs_general(el) {

	var form     = jQuery( '.wpsc-frm-gs-general' )[0];
	var dataform = new FormData( form );
	jQuery( el ).text( supportcandy.translations.please_wait );
	jQuery.ajax(
		{
			url: supportcandy.ajax_url,
			type: 'POST',
			data: dataform,
			processData: false,
			contentType: false
		}
	).done(
		function (res) {
			wpsc_get_gs_general();
		}
	);
}

/**
 * Reset general settings
 */
function wpsc_reset_gs_general(el, nonce) {

	jQuery( '.wpsc-modal-footer button' ).attr( 'disabled', true );
	jQuery( el ).text( supportcandy.translations.please_wait );
	var data = { action: 'wpsc_reset_gs_general', _ajax_nonce: nonce };
	jQuery.post(
		supportcandy.ajax_url,
		data,
		function (response) {
			wpsc_get_gs_general();
		}
	);
}

/**
 * Load page settings tab ui
 */
function wpsc_get_gs_page_settings() {

	supportcandy.current_tab = 'page-settings';
	jQuery( '.wpsc-setting-tab-container button' ).removeClass( 'active' );
	jQuery( '.wpsc-setting-tab-container button.' + supportcandy.current_tab ).addClass( 'active' );

	window.history.replaceState( {}, null, 'admin.php?page=wpsc-settings&section=' + supportcandy.current_section + '&tab=' + supportcandy.current_tab );
	jQuery( '.wpsc-setting-section-body' ).html( supportcandy.loader_html );

	wpsc_scroll_top();

	var data = { action: 'wpsc_get_gs_page_settings' };
	jQuery.post(
		supportcandy.ajax_url,
		data,
		function (response) {
			jQuery( '.wpsc-setting-section-body' ).html( response );
			wpsc_reset_responsive_style();
		}
	);
}

/**
 * Save page settings
 */
function wpsc_set_gs_page_settings(el) {

	var form     = jQuery( '.wpsc-frm-gs-ps' )[0];
	var dataform = new FormData( form );
	jQuery( '.wpsc-modal-footer button' ).attr( 'disabled', true );
	jQuery( el ).text( supportcandy.translations.please_wait );
	jQuery.ajax(
		{
			url: supportcandy.ajax_url,
			type: 'POST',
			data: dataform,
			processData: false,
			contentType: false
		}
	).done(
		function (res) {
			wpsc_get_gs_page_settings();
		}
	);
}

/**
 * Reset page settings
 */
function wpsc_reset_gs_page_settings(el, nonce) {

	jQuery( '.wpsc-modal-footer button' ).attr( 'disabled', true );
	jQuery( el ).text( supportcandy.translations.please_wait );
	var data = { action: 'wpsc_reset_gs_page_settings', _ajax_nonce: nonce };
	jQuery.post(
		supportcandy.ajax_url,
		data,
		function (response) {
			wpsc_get_gs_page_settings();
		}
	);
}

/**
 * Get category settings
 */
function wpsc_get_ticket_categories(is_humbargar = false) {

	if (is_humbargar) {
		wpsc_toggle_humbargar();
	}

	jQuery( '.wpsc-setting-nav, .wpsc-humbargar-menu-item' ).removeClass( 'active' );
	jQuery( '.wpsc-setting-nav.ticket-categories, .wpsc-humbargar-menu-item.ticket-categories' ).addClass( 'active' );
	jQuery( '.wpsc-humbargar-title' ).html( supportcandy.humbargar_titles.ticket_categories );

	window.history.replaceState( {}, null, 'admin.php?page=wpsc-settings&section=ticket-categories' );
	jQuery( '.wpsc-setting-body' ).html( supportcandy.loader_html );

	wpsc_scroll_top();

	var data = { action: 'wpsc_get_ticket_categories' };
	jQuery.post(
		supportcandy.ajax_url,
		data,
		function (response) {
			jQuery( '.wpsc-setting-body' ).html( response );
			wpsc_reset_responsive_style();
		}
	);
}

/**
 * Get working hrs settings
 */
function wpsc_get_working_hrs_settings(is_humbargar = false) {

	if (is_humbargar) {
		wpsc_toggle_humbargar();
	}

	jQuery( '.wpsc-setting-nav, .wpsc-humbargar-menu-item' ).removeClass( 'active' );
	jQuery( '.wpsc-setting-nav.working-hrs, .wpsc-humbargar-menu-item.working-hrs' ).addClass( 'active' );
	jQuery( '.wpsc-humbargar-title' ).html( supportcandy.humbargar_titles.working_hrs );

	if (supportcandy.current_section !== 'working-hrs') {
		supportcandy.current_section = 'working-hrs';
		supportcandy.current_tab     = 'working-hrs';
	}

	window.history.replaceState( {}, null, 'admin.php?page=wpsc-settings&section=' + supportcandy.current_section + '&tab=' + supportcandy.current_tab );
	jQuery( '.wpsc-setting-body' ).html( supportcandy.loader_html );

	wpsc_scroll_top();

	var data = {
		action: 'wpsc_get_working_hrs_settings',
		tab: supportcandy.current_tab
	};
	jQuery.post(
		supportcandy.ajax_url,
		data,
		function (response) {
			jQuery( '.wpsc-setting-body' ).html( response );
			wpsc_reset_responsive_style();
			jQuery( '.wpsc-setting-tab-container button.' + supportcandy.current_tab ).trigger( "click" );
		}
	);
}

/**
 * Get working hrs
 */
function wpsc_get_working_hrs() {

	supportcandy.current_tab = 'working-hrs';
	jQuery( '.wpsc-setting-tab-container button' ).removeClass( 'active' );
	jQuery( '.wpsc-setting-tab-container button.' + supportcandy.current_tab ).addClass( 'active' );

	window.history.replaceState( {}, null, 'admin.php?page=wpsc-settings&section=' + supportcandy.current_section + '&tab=' + supportcandy.current_tab );
	jQuery( '.wpsc-setting-section-body' ).html( supportcandy.loader_html );

	wpsc_scroll_top();
	var data = { action: 'wpsc_get_working_hrs' };
	jQuery.post(
		supportcandy.ajax_url,
		data,
		function (response) {
			jQuery( '.wpsc-setting-section-body' ).html( response );
			wpsc_reset_responsive_style();
		}
	);
}

/**
 * Get working hrs
 */
function wpsc_set_working_hrs() {

	var form     = jQuery( 'form.wpsc-wh-settings' )[0];
	var dataform = new FormData( form );
	jQuery( '.wpsc-setting-section-body' ).html( supportcandy.loader_html );
	jQuery.ajax(
		{
			url: supportcandy.ajax_url,
			type: 'POST',
			data: dataform,
			processData: false,
			contentType: false
		}
	).done(
		function (res) {
			wpsc_get_working_hrs();
		}
	);
}

/**
 * Get holidays
 */
function wpsc_get_holidays() {

	supportcandy.current_tab = 'holidays';
	jQuery( '.wpsc-setting-tab-container button' ).removeClass( 'active' );
	jQuery( '.wpsc-setting-tab-container button.' + supportcandy.current_tab ).addClass( 'active' );

	window.history.replaceState( {}, null, 'admin.php?page=wpsc-settings&section=' + supportcandy.current_section + '&tab=' + supportcandy.current_tab );
	jQuery( '.wpsc-setting-section-body' ).html( supportcandy.loader_html );

	wpsc_scroll_top();
	var data = { action: 'wpsc_get_holidays' };
	jQuery.post(
		supportcandy.ajax_url,
		data,
		function (response) {
			jQuery( '.wpsc-setting-section-body' ).html( response );
			wpsc_reset_responsive_style();
		}
	);
}

/**
 * Get company holiday actions
 */
function wpsc_get_company_holiday_actions(dateSelected, nonce) {

	supportcandy.temp.dateSelected = dateSelected;
	wpsc_show_modal();
	var data = {
		action: 'wpsc_get_company_holiday_actions',
		dateSelected,
		_ajax_nonce: nonce
	};
	jQuery.post(
		supportcandy.ajax_url,
		data,
		function (response) {

			// Set to modal.
			jQuery( '.wpsc-modal-header' ).text( response.title );
			jQuery( '.wpsc-modal-body' ).html( response.body );
			jQuery( '.wpsc-modal-footer' ).html( response.footer );
			// Display modal.
			wpsc_show_modal_inner_container();
		}
	);
}

/**
 * Set company holiday actions
 */
function wpsc_set_company_holiday_actions(el) {

	const form     = jQuery( '.wpsc-frm-comp-holiday-actions' )[0];
	const dataform = new FormData( form );
	dataform.append( 'dateSelected', supportcandy.temp.dateSelected );
	jQuery( el ).text( supportcandy.translations.please_wait );
	jQuery.ajax(
		{
			url: supportcandy.ajax_url,
			type: 'POST',
			data: dataform,
			processData: false,
			contentType: false
		}
	).done(
		function (response) {
			jQuery.each(
				supportcandy.temp.dateSelected,
				function (index, value) {
					if (response.action == 'add' && response.is_recurring == 1) {
						jQuery( 'td' ).find( '[data-date=' + value + ']' ).css( { 'background-color': '#eb4d4b' } );
					} else if (response.action == 'add' && response.is_recurring == 0) {
						jQuery( 'td' ).find( '[data-date=' + value + ']' ).css( { 'background-color': '#f0932b' } );
					} else {
						jQuery( 'td' ).find( '[data-date=' + value + ']' ).css( 'background-color', 'unset' );
					}
				}
			);
			supportcandy.temp.holidayList = response.holidayList;
			wpsc_close_modal();
		}
	);
}

/**
 * Get working hrs exceptions
 */
function wpsc_get_wh_exceptions() {

	supportcandy.current_tab = 'exceptions';
	jQuery( '.wpsc-setting-tab-container button' ).removeClass( 'active' );
	jQuery( '.wpsc-setting-tab-container button.' + supportcandy.current_tab ).addClass( 'active' );

	window.history.replaceState( {}, null, 'admin.php?page=wpsc-settings&section=' + supportcandy.current_section + '&tab=' + supportcandy.current_tab );
	jQuery( '.wpsc-setting-section-body' ).html( supportcandy.loader_html );

	wpsc_scroll_top();
	var data = { action: 'wpsc_get_wh_exceptions' };
	jQuery.post(
		supportcandy.ajax_url,
		data,
		function (response) {
			jQuery( '.wpsc-setting-section-body' ).html( response );
			wpsc_reset_responsive_style();
		}
	);
}

/**
 * Add company holiday
 */
function wpsc_set_add_holiday(el) {

	var title = jQuery( 'input[name=title]' ).val().trim();
	if (title.length === 0) {
		return;
	}

	var startDate = jQuery( 'input.start_date' ).val().trim();
	if (startDate.length === 0) {
		return;
	}

	jQuery( '.wpsc-modal-footer button' ).attr( 'disabled', true );
	jQuery( el ).text( supportcandy.translations.please_wait );
	var form     = jQuery( '.wpsc-frm-add-holiday' )[0];
	var dataform = new FormData( form );
	jQuery.ajax(
		{
			url: supportcandy.ajax_url,
			type: 'POST',
			data: dataform,
			processData: false,
			contentType: false
		}
	).done(
		function (res) {
			wpsc_close_modal();
			wpsc_get_holidays();
		}
	);
}

/**
 * Add company holiday
 */
function wpsc_set_edit_holiday(el) {

	var title = jQuery( 'input[name=title]' ).val().trim();
	if (title.length === 0) {
		return;
	}

	var startDate = jQuery( 'input.start_date' ).val().trim();
	if (startDate.length === 0) {
		return;
	}

	jQuery( '.wpsc-modal-footer button' ).attr( 'disabled', true );
	jQuery( el ).text( supportcandy.translations.please_wait );
	var form     = jQuery( '.wpsc-frm-edit-holiday' )[0];
	var dataform = new FormData( form );
	jQuery.ajax(
		{
			url: supportcandy.ajax_url,
			type: 'POST',
			data: dataform,
			processData: false,
			contentType: false
		}
	).done(
		function (res) {
			wpsc_close_modal();
			wpsc_get_holidays();
		}
	);
}

/**
 * Add company holiday
 */
function wpsc_set_add_wh_exception(el) {

	var title = jQuery( 'input[name=title]' ).val().trim();
	if (title.length === 0) {
		return;
	}

	var startDate = jQuery( 'input.exception_date' ).val().trim();
	if (startDate.length === 0) {
		return;
	}

	jQuery( '.wpsc-modal-footer button' ).attr( 'disabled', true );
	jQuery( el ).text( supportcandy.translations.please_wait );
	var form     = jQuery( '.wpsc-frm-add-exception' )[0];
	var dataform = new FormData( form );
	jQuery.ajax(
		{
			url: supportcandy.ajax_url,
			type: 'POST',
			data: dataform,
			processData: false,
			contentType: false
		}
	).done(
		function (res) {
			wpsc_close_modal();
			wpsc_get_wh_exceptions();
		}
	);
}

/**
 * Add company holiday
 */
function wpsc_set_edit_wh_exception(el) {

	var title = jQuery( 'input[name=title]' ).val().trim();
	if (title.length === 0) {
		return;
	}

	jQuery( '.wpsc-modal-footer button' ).attr( 'disabled', true );
	jQuery( el ).text( supportcandy.translations.please_wait );
	var form     = jQuery( '.wpsc-frm-edit-exception' )[0];
	var dataform = new FormData( form );
	jQuery.ajax(
		{
			url: supportcandy.ajax_url,
			type: 'POST',
			data: dataform,
			processData: false,
			contentType: false
		}
	).done(
		function (res) {
			wpsc_close_modal();
			wpsc_get_wh_exceptions();
		}
	);
}

/**
 * Get working hrs settings
 */
function wpsc_get_wh_settings() {

	supportcandy.current_tab = 'settings';
	jQuery( '.wpsc-setting-tab-container button' ).removeClass( 'active' );
	jQuery( '.wpsc-setting-tab-container button.' + supportcandy.current_tab ).addClass( 'active' );

	window.history.replaceState( {}, null, 'admin.php?page=wpsc-settings&section=' + supportcandy.current_section + '&tab=' + supportcandy.current_tab );
	jQuery( '.wpsc-setting-section-body' ).html( supportcandy.loader_html );

	wpsc_scroll_top();
	var data = { action: 'wpsc_get_wh_settings' };
	jQuery.post(
		supportcandy.ajax_url,
		data,
		function (response) {
			jQuery( '.wpsc-setting-section-body' ).html( response );
			wpsc_reset_responsive_style();
		}
	);
}

/**
 * Set working hrs setting
 */
function wpsc_set_wh_settings(el) {

	var form     = jQuery( '.wpsc-frm-wh-settings' )[0];
	var dataform = new FormData( form );
	jQuery( el ).text( supportcandy.translations.please_wait );
	jQuery.ajax(
		{
			url: supportcandy.ajax_url,
			type: 'POST',
			data: dataform,
			processData: false,
			contentType: false
		}
	).done(
		function (res) {
			wpsc_get_wh_settings();
		}
	);
}

/**
 * Set working hrs setting
 */
function wpsc_reset_wh_settings(el, nonce) {

	jQuery( el ).text( supportcandy.translations.please_wait );
	var data = { action: 'wpsc_reset_wh_settings', _ajax_nonce: nonce };
	jQuery.post(
		supportcandy.ajax_url,
		data,
		function (res) {
			wpsc_get_wh_settings();
		}
	);
}

/**
 * Get appearence settings
 */
function wpsc_get_appearence_settings(is_humbargar = false) {

	if (is_humbargar) {
		wpsc_toggle_humbargar();
	}

	jQuery( '.wpsc-setting-nav, .wpsc-humbargar-menu-item' ).removeClass( 'active' );
	jQuery( '.wpsc-setting-nav.appearence, .wpsc-humbargar-menu-item.appearence' ).addClass( 'active' );
	jQuery( '.wpsc-humbargar-title' ).html( supportcandy.humbargar_titles.appearence );

	if (supportcandy.current_section !== 'appearence') {
		supportcandy.current_section = 'appearence';
		supportcandy.current_tab     = 'general';
	}

	window.history.replaceState( {}, null, 'admin.php?page=wpsc-settings&section=' + supportcandy.current_section + '&tab=' + supportcandy.current_tab );
	jQuery( '.wpsc-setting-body' ).html( supportcandy.loader_html );

	wpsc_scroll_top();

	var data = {
		action: 'wpsc_get_appearence_settings',
		tab: supportcandy.current_tab
	};
	jQuery.post(
		supportcandy.ajax_url,
		data,
		function (response) {
			jQuery( '.wpsc-setting-body' ).html( response );
			wpsc_reset_responsive_style();
			jQuery( '.wpsc-setting-tab-container button.' + supportcandy.current_tab ).trigger( "click" );
		}
	);
}

/**
 * Get general appearence settings
 */
function wpsc_get_ap_general() {

	supportcandy.current_tab = 'general';
	jQuery( '.wpsc-setting-tab-container button' ).removeClass( 'active' );
	jQuery( '.wpsc-setting-tab-container button.' + supportcandy.current_tab ).addClass( 'active' );

	window.history.replaceState( {}, null, 'admin.php?page=wpsc-settings&section=' + supportcandy.current_section + '&tab=' + supportcandy.current_tab );
	jQuery( '.wpsc-setting-section-body' ).html( supportcandy.loader_html );

	wpsc_scroll_top();
	var data = { action: 'wpsc_get_ap_general' };
	jQuery.post(
		supportcandy.ajax_url,
		data,
		function (response) {
			jQuery( '.wpsc-setting-section-body' ).html( response );
			wpsc_reset_responsive_style();
		}
	);
}

/**
 * Set general appearence settings
 */
function wpsc_set_ap_general(el) {

	var form     = jQuery( '.wpsc-frm-ap-general' )[0];
	var dataform = new FormData( form );
	jQuery( el ).text( supportcandy.translations.please_wait );
	jQuery.ajax(
		{
			url: supportcandy.ajax_url,
			type: 'POST',
			data: dataform,
			processData: false,
			contentType: false
		}
	).done(
		function (res) {
			wpsc_get_ap_general();
		}
	);
}

/**
 * Reset appearence general settings
 */
function wpsc_reset_ap_general(el, nonce) {

	jQuery( el ).text( supportcandy.translations.please_wait );
	var data = { action: 'wpsc_reset_ap_general', _ajax_nonce: nonce };
	jQuery.post(
		supportcandy.ajax_url,
		data,
		function (res) {
			wpsc_get_ap_general();
		}
	);
}

/**
 * Get appearence ticket list settings
 */
function wpsc_get_ap_ticket_list() {

	supportcandy.current_tab = 'ticket-list';
	jQuery( '.wpsc-setting-tab-container button' ).removeClass( 'active' );
	jQuery( '.wpsc-setting-tab-container button.' + supportcandy.current_tab ).addClass( 'active' );

	window.history.replaceState( {}, null, 'admin.php?page=wpsc-settings&section=' + supportcandy.current_section + '&tab=' + supportcandy.current_tab );
	jQuery( '.wpsc-setting-section-body' ).html( supportcandy.loader_html );

	wpsc_scroll_top();
	var data = { action: 'wpsc_get_ap_ticket_list' };
	jQuery.post(
		supportcandy.ajax_url,
		data,
		function (response) {
			jQuery( '.wpsc-setting-section-body' ).html( response );
			wpsc_reset_responsive_style();
		}
	);
}

/**
 * Set appearence ticket list settings
 */
function wpsc_set_ap_ticket_list(el) {

	var form     = jQuery( '.wpsc-frm-ap-tl' )[0];
	var dataform = new FormData( form );
	jQuery( el ).text( supportcandy.translations.please_wait );
	jQuery.ajax(
		{
			url: supportcandy.ajax_url,
			type: 'POST',
			data: dataform,
			processData: false,
			contentType: false
		}
	).done(
		function (res) {
			wpsc_get_ap_ticket_list();
		}
	);
}

/**
 * Reset appearence ticket list settings
 */
function wpsc_reset_ap_ticket_list(el, nonce) {

	jQuery( el ).text( supportcandy.translations.please_wait );
	var data = { action: 'wpsc_reset_ap_ticket_list', _ajax_nonce: nonce };
	jQuery.post(
		supportcandy.ajax_url,
		data,
		function (res) {
			wpsc_get_ap_ticket_list();
		}
	);
}

/**
 * Get appearence individual ticket settings
 */
function wpsc_get_ap_individual_ticket() {

	supportcandy.current_tab = 'individual-ticket';
	jQuery( '.wpsc-setting-tab-container button' ).removeClass( 'active' );
	jQuery( '.wpsc-setting-tab-container button.' + supportcandy.current_tab ).addClass( 'active' );

	window.history.replaceState( {}, null, 'admin.php?page=wpsc-settings&section=' + supportcandy.current_section + '&tab=' + supportcandy.current_tab );
	jQuery( '.wpsc-setting-section-body' ).html( supportcandy.loader_html );

	wpsc_scroll_top();
	var data = { action: 'wpsc_get_ap_individual_ticket' };
	jQuery.post(
		supportcandy.ajax_url,
		data,
		function (response) {
			jQuery( '.wpsc-setting-section-body' ).html( response );
			wpsc_reset_responsive_style();
		}
	);
}

/**
 * Set appearence individual ticket settings
 */
function wpsc_set_ap_individual_ticket(el) {

	var form     = jQuery( '.wpsc-frm-ap-it' )[0];
	var dataform = new FormData( form );
	jQuery( el ).text( supportcandy.translations.please_wait );
	jQuery.ajax(
		{
			url: supportcandy.ajax_url,
			type: 'POST',
			data: dataform,
			processData: false,
			contentType: false
		}
	).done(
		function (res) {
			wpsc_get_ap_individual_ticket();
		}
	);
}

/**
 * Reset appearence individual ticket settings
 */
function wpsc_reset_ap_individual_ticket(el, nonce) {

	jQuery( el ).text( supportcandy.translations.please_wait );
	var data = { action: 'wpsc_reset_ap_individual_ticket', _ajax_nonce: nonce };
	jQuery.post(
		supportcandy.ajax_url,
		data,
		function (res) {
			wpsc_get_ap_individual_ticket();
		}
	);
}

/**
 * Get appearence modal popup settigns
 */
function wpsc_get_ap_modal_popup() {

	supportcandy.current_tab = 'modal-popup';
	jQuery( '.wpsc-setting-tab-container button' ).removeClass( 'active' );
	jQuery( '.wpsc-setting-tab-container button.' + supportcandy.current_tab ).addClass( 'active' );

	window.history.replaceState( {}, null, 'admin.php?page=wpsc-settings&section=' + supportcandy.current_section + '&tab=' + supportcandy.current_tab );
	jQuery( '.wpsc-setting-section-body' ).html( supportcandy.loader_html );

	wpsc_scroll_top();
	var data = { action: 'wpsc_get_ap_modal_popup' };
	jQuery.post(
		supportcandy.ajax_url,
		data,
		function (response) {
			jQuery( '.wpsc-setting-section-body' ).html( response );
			wpsc_reset_responsive_style();
		}
	);
}

/**
 * Set appearence modal settings
 */
function wpsc_set_ap_modal_popup(el) {

	var form     = jQuery( '.wpsc-frm-ap-modal' )[0];
	var dataform = new FormData( form );
	jQuery( el ).text( supportcandy.translations.please_wait );
	jQuery.ajax(
		{
			url: supportcandy.ajax_url,
			type: 'POST',
			data: dataform,
			processData: false,
			contentType: false
		}
	).done(
		function (res) {
			wpsc_get_ap_modal_popup();
		}
	);
}

/**
 * Reset appearence modal settings
 */
function wpsc_reset_ap_modal_popup(el, nonce) {

	jQuery( el ).text( supportcandy.translations.please_wait );
	var data = { action: 'wpsc_reset_ap_modal_popup', _ajax_nonce: nonce };
	jQuery.post(
		supportcandy.ajax_url,
		data,
		function (res) {
			wpsc_get_ap_modal_popup();
		}
	);
}

/**
 * Get appearence agent collision settigns
 */
function wpsc_get_ap_agent_collision() {

	supportcandy.current_tab = 'agent-collision';
	jQuery( '.wpsc-setting-tab-container button' ).removeClass( 'active' );
	jQuery( '.wpsc-setting-tab-container button.' + supportcandy.current_tab ).addClass( 'active' );

	window.history.replaceState( {}, null, 'admin.php?page=wpsc-settings&section=' + supportcandy.current_section + '&tab=' + supportcandy.current_tab );
	jQuery( '.wpsc-setting-section-body' ).html( supportcandy.loader_html );

	wpsc_scroll_top();
	var data = { action: 'wpsc_get_ap_agent_collision' };
	jQuery.post(
		supportcandy.ajax_url,
		data,
		function (response) {
			jQuery( '.wpsc-setting-section-body' ).html( response );
			wpsc_reset_responsive_style();
		}
	);
}

/**
 * Set appearence agent collision settings
 */
function wpsc_set_ap_agent_collision(el) {

	var form     = jQuery( '.wpsc-frm-ap-agent-collision' )[0];
	var dataform = new FormData( form );
	jQuery( el ).text( supportcandy.translations.please_wait );
	jQuery.ajax(
		{
			url: supportcandy.ajax_url,
			type: 'POST',
			data: dataform,
			processData: false,
			contentType: false
		}
	).done(
		function (res) {
			wpsc_get_ap_agent_collision();
		}
	);
}

/**
 * Reset appearence agent collision settings
 */
function wpsc_reset_ap_agent_collision(el, nonce) {

	jQuery( el ).text( supportcandy.translations.please_wait );
	var data = { action: 'wpsc_reset_ap_agent_collision', _ajax_nonce: nonce };
	jQuery.post(
		supportcandy.ajax_url,
		data,
		function (res) {
			wpsc_get_ap_agent_collision();
		}
	);
}

/**
 *  Set add new category
 */
function wpsc_set_add_category(el) {

	var form     = jQuery( '.wpsc-frm-add-category' )[0];
	var dataform = new FormData( form );

	if (dataform.get( 'label' ).trim() == '') {
		alert( supportcandy.translations.req_fields_missing );
		return;
	}

	jQuery( '.wpsc-modal-footer button' ).attr( 'disabled', true );
	jQuery( el ).text( supportcandy.translations.please_wait );

	jQuery.ajax(
		{
			url: supportcandy.ajax_url,
			type: 'POST',
			data: dataform,
			processData: false,
			contentType: false
		}
	).done(
		function (res) {
			wpsc_close_modal();
			wpsc_get_ticket_categories();
		}
	);
}

/**
 * Get edit category modal
 */
function wpsc_get_edit_category(id, nonce) {

	wpsc_show_modal();
	var data = { action: 'wpsc_get_edit_category', id, _ajax_nonce: nonce };
	jQuery.post(
		supportcandy.ajax_url,
		data,
		function (response) {

			// Set to modal.
			jQuery( '.wpsc-modal-header' ).text( response.title );
			jQuery( '.wpsc-modal-body' ).html( response.body );
			jQuery( '.wpsc-modal-footer' ).html( response.footer );
			// Display modal.
			wpsc_show_modal_inner_container();
		}
	);
}

/**
 * Update category
 */
function wpsc_set_edit_category(el) {
	
	var form     = jQuery( '.wpsc-frm-edit-category' )[0];
	var dataform = new FormData( form );

	if (dataform.get( 'label' ).trim() == '') {
		alert( supportcandy.translations.req_fields_missing );
		return;
	}

	jQuery( '.wpsc-modal-footer button' ).attr( 'disabled', true );
	jQuery( el ).text( supportcandy.translations.please_wait );

	jQuery.ajax(
		{
			url: supportcandy.ajax_url,
			type: 'POST',
			data: dataform,
			processData: false,
			contentType: false
		}
	).done(
		function (res) {
			wpsc_close_modal();
			wpsc_get_ticket_categories();
		}
	);
}

/**
 * Delete category modal
 */
function wpsc_get_delete_category(id, nonce) {

	var flag = confirm( supportcandy.translations.confirm );
	if ( ! flag) {
		return;
	}

	wpsc_show_modal();
	var data = { action: 'wpsc_get_delete_category', id, _ajax_nonce: nonce };
	jQuery.post(
		supportcandy.ajax_url,
		data,
		function (response) {
			// Set to modal.
			jQuery( '.wpsc-modal-header' ).text( response.title );
			jQuery( '.wpsc-modal-body' ).html( response.body );
			jQuery( '.wpsc-modal-footer' ).html( response.footer );
			// Display modal.
			wpsc_show_modal_inner_container();
		}
	);
}

/**
 * Delete category
 */
function wpsc_set_delete_category(el) {

	jQuery( '.wpsc-modal-footer button' ).attr( 'disabled', true );
	jQuery( el ).text( supportcandy.translations.please_wait );

	var form     = jQuery( '.wpsc-frm-delete-category' )[0];
	var dataform = new FormData( form );
	jQuery.ajax(
		{
			url: supportcandy.ajax_url,
			type: 'POST',
			data: dataform,
			processData: false,
			contentType: false
		}
	).done(
		function (res) {
			wpsc_close_modal();
			wpsc_get_ticket_categories();
		}
	);
}

/**
 *  Get status settings
 */
function wpsc_get_ticket_statuses(is_humbargar = false) {

	if (is_humbargar) {
		wpsc_toggle_humbargar();
	}

	jQuery( '.wpsc-setting-nav, .wpsc-humbargar-menu-item' ).removeClass( 'active' );
	jQuery( '.wpsc-setting-nav.ticket-statuses, .wpsc-humbargar-menu-item.ticket-statuses' ).addClass( 'active' );
	jQuery( '.wpsc-humbargar-title' ).html( supportcandy.humbargar_titles.ticket_statuses );

	window.history.replaceState( {}, null, 'admin.php?page=wpsc-settings&section=ticket-statuses' );
	jQuery( '.wpsc-setting-body' ).html( supportcandy.loader_html );

	wpsc_scroll_top();

	var data = { action: 'wpsc_get_ticket_statuses' };
	jQuery.post(
		supportcandy.ajax_url,
		data,
		function (response) {
			jQuery( '.wpsc-setting-body' ).html( response );
			wpsc_reset_responsive_style();
		}
	);

}

/**
 *  Set add new status
 */
function wpsc_set_add_status(el) {

	var form     = jQuery( '.wpsc-frm-add-status' )[0];
	var dataform = new FormData( form );

	if (dataform.get( 'name' ).trim() == '') {
		alert( supportcandy.translations.req_fields_missing );
		return;
	}

	jQuery( '.wpsc-modal-footer button' ).attr( 'disabled', true );
	jQuery( el ).text( supportcandy.translations.please_wait );

	jQuery.ajax(
		{
			url: supportcandy.ajax_url,
			type: 'POST',
			data: dataform,
			processData: false,
			contentType: false
		}
	).done(
		function (res) {
			wpsc_close_modal();
			wpsc_get_ticket_statuses();
		}
	);
}

/**
 * Get edit status modal
 */
function wpsc_get_edit_status(id, nonce) {

	wpsc_show_modal();
	var data = { action: 'wpsc_get_edit_status', id, _ajax_nonce: nonce };
	jQuery.post(
		supportcandy.ajax_url,
		data,
		function (response) {

			// Set to modal.
			jQuery( '.wpsc-modal-header' ).text( response.title );
			jQuery( '.wpsc-modal-body' ).html( response.body );
			jQuery( '.wpsc-modal-footer' ).html( response.footer );
			// Display modal.
			wpsc_show_modal_inner_container();
		}
	);
}

/**
 * Update status
 */
function wpsc_set_edit_status(el) {

	var form     = jQuery( '.wpsc-frm-edit-status' )[0];
	var dataform = new FormData( form );

	if (dataform.get( 'name' ).trim() == '') {
		alert( supportcandy.translations.req_fields_missing );
		return;
	}

	jQuery( '.wpsc-modal-footer button' ).attr( 'disabled', true );
	jQuery( el ).text( supportcandy.translations.please_wait );

	jQuery.ajax(
		{
			url: supportcandy.ajax_url,
			type: 'POST',
			data: dataform,
			processData: false,
			contentType: false
		}
	).done(
		function (res) {
			wpsc_close_modal();
			wpsc_get_ticket_statuses();
		}
	);
}

/**
 * Delete status modal
 */
function wpsc_get_delete_status(id, nonce) {

	var flag = confirm( supportcandy.translations.confirm );
	if ( ! flag) {
		return;
	}

	wpsc_show_modal();
	var data = { action: 'wpsc_get_delete_status', id, _ajax_nonce: nonce };
	jQuery.post(
		supportcandy.ajax_url,
		data,
		function (response) {
			// Set to modal.
			jQuery( '.wpsc-modal-header' ).text( response.title );
			jQuery( '.wpsc-modal-body' ).html( response.body );
			jQuery( '.wpsc-modal-footer' ).html( response.footer );
			// Display modal.
			wpsc_show_modal_inner_container();
		}
	);
}

/**
 * Delete status
 */
function wpsc_set_delete_status(el) {

	jQuery( '.wpsc-modal-footer button' ).attr( 'disabled', true );
	jQuery( el ).text( supportcandy.translations.please_wait );

	var form     = jQuery( '.wpsc-frm-delete-status' )[0];
	var dataform = new FormData( form );
	jQuery.ajax(
		{
			url: supportcandy.ajax_url,
			type: 'POST',
			data: dataform,
			processData: false,
			contentType: false
		}
	).done(
		function (res) {
			wpsc_close_modal();
			wpsc_get_ticket_statuses();
		}
	);
}

/**
 *  Get priority settings
 */
function wpsc_get_ticket_priorities(is_humbargar = false) {

	if (is_humbargar) {
		wpsc_toggle_humbargar();
	}

	jQuery( '.wpsc-setting-nav, .wpsc-humbargar-menu-item' ).removeClass( 'active' );
	jQuery( '.wpsc-setting-nav.ticket-priorities, .wpsc-humbargar-menu-item.ticket-priorities' ).addClass( 'active' );
	jQuery( '.wpsc-humbargar-title' ).html( supportcandy.humbargar_titles.ticket_priorities );

	window.history.replaceState( {}, null, 'admin.php?page=wpsc-settings&section=ticket-priorities' );
	jQuery( '.wpsc-setting-body' ).html( supportcandy.loader_html );

	wpsc_scroll_top();

	var data = { action: 'wpsc_get_ticket_priorities' };
	jQuery.post(
		supportcandy.ajax_url,
		data,
		function (response) {
			jQuery( '.wpsc-setting-body' ).html( response );
			wpsc_reset_responsive_style();
		}
	);
}

/**
 *  Set add new priority
 */
function wpsc_set_add_priority(el) {

	var form     = jQuery( '.wpsc-frm-add-priority' )[0];
	var dataform = new FormData( form );

	if (dataform.get( 'name' ).trim() == '') {
		alert( supportcandy.translations.req_fields_missing );
		return;
	}

	jQuery( '.wpsc-modal-footer button' ).attr( 'disabled', true );
	jQuery( el ).text( supportcandy.translations.please_wait );

	jQuery.ajax(
		{
			url: supportcandy.ajax_url,
			type: 'POST',
			data: dataform,
			processData: false,
			contentType: false
		}
	).done(
		function (res) {
			wpsc_close_modal();
			wpsc_get_ticket_priorities();
		}
	);
}

/**
 * Get edit priority modal
 */
function wpsc_get_edit_priority(id, nonce) {

	wpsc_show_modal();
	var data = { action: 'wpsc_get_edit_priority', id, _ajax_nonce: nonce };
	jQuery.post(
		supportcandy.ajax_url,
		data,
		function (response) {

			// Set to modal.
			jQuery( '.wpsc-modal-header' ).text( response.title );
			jQuery( '.wpsc-modal-body' ).html( response.body );
			jQuery( '.wpsc-modal-footer' ).html( response.footer );
			// Display modal.
			wpsc_show_modal_inner_container();
		}
	);
}

/**
 * Update an priority
 */
function wpsc_set_edit_priority(el) {

	var form     = jQuery( '.wpsc-frm-edit-priority' )[0];
	var dataform = new FormData( form );

	if (dataform.get( 'name' ).trim() == '') {
		alert( supportcandy.translations.req_fields_missing );
		return;
	}

	jQuery( '.wpsc-modal-footer button' ).attr( 'disabled', true );
	jQuery( el ).text( supportcandy.translations.please_wait );

	jQuery.ajax(
		{
			url: supportcandy.ajax_url,
			type: 'POST',
			data: dataform,
			processData: false,
			contentType: false
		}
	).done(
		function (res) {
			wpsc_close_modal();
			wpsc_get_ticket_priorities();
		}
	);
}

/**
 * Delete priority modal
 */
function wpsc_get_delete_priority(id, nonce) {

	var flag = confirm( supportcandy.translations.confirm );
	if ( ! flag) {
		return;
	}

	wpsc_show_modal();
	var data = { action: 'wpsc_get_delete_priority', id, _ajax_nonce: nonce };
	jQuery.post(
		supportcandy.ajax_url,
		data,
		function (response) {
			// Set to modal.
			jQuery( '.wpsc-modal-header' ).text( response.title );
			jQuery( '.wpsc-modal-body' ).html( response.body );
			jQuery( '.wpsc-modal-footer' ).html( response.footer );
			// Display modal.
			wpsc_show_modal_inner_container();
		}
	);
}

/**
 * Delete priority
 */
function wpsc_set_delete_priority(el) {

	jQuery( '.wpsc-modal-footer button' ).attr( 'disabled', true );
	jQuery( el ).text( supportcandy.translations.please_wait );

	var form     = jQuery( '.wpsc-frm-delete-priority' )[0];
	var dataform = new FormData( form );
	jQuery.ajax(
		{
			url: supportcandy.ajax_url,
			type: 'POST',
			data: dataform,
			processData: false,
			contentType: false
		}
	).done(
		function (res) {
			wpsc_close_modal();
			wpsc_get_ticket_priorities();
		}
	);
}

/**
 * Get ticket form fields
 */
function wpsc_get_tff(is_humbargar = false) {

	if (is_humbargar) {
		wpsc_toggle_humbargar();
	}

	jQuery( '.wpsc-setting-nav, .wpsc-humbargar-menu-item' ).removeClass( 'active' );
	jQuery( '.wpsc-setting-nav.ticket-form-fields, .wpsc-humbargar-menu-item.ticket-form-fields' ).addClass( 'active' );
	jQuery( '.wpsc-humbargar-title' ).html( supportcandy.humbargar_titles.ticket_form_fields );

	window.history.replaceState( {}, null, 'admin.php?page=wpsc-ticket-form&section=ticket-form-fields' );
	jQuery( '.wpsc-setting-body' ).html( supportcandy.loader_html );

	wpsc_scroll_top();

	var data = { action: 'wpsc_get_tff' };
	jQuery.post(
		supportcandy.ajax_url,
		data,
		function (response) {
			jQuery( '.wpsc-setting-body' ).html( response );
			wpsc_reset_responsive_style();
		}
	);
}

/**
 * Set add new ticket form field
 */
function wpsc_set_add_new_tff(el) {

	if ( ! jQuery( '#wpsc-select-ticket-form-field' ).val()) {
		alert( supportcandy.translations.req_fields_missing );
		return;
	}

	jQuery( '.wpsc-modal-footer button' ).attr( 'disabled', true );
	jQuery( el ).text( supportcandy.translations.please_wait );

	var form     = jQuery( '.frm-add-new-ticket-form-field' )[0];
	var dataform = new FormData( form );

	dataform.append( 'visibility', JSON.stringify( wpsc_get_condition_json( 'visibility' ) ) );

	jQuery.ajax(
		{
			url: supportcandy.ajax_url,
			type: 'POST',
			data: dataform,
			processData: false,
			contentType: false
		}
	).done(
		function (res) {
			wpsc_close_modal();
			wpsc_get_tff();
		}
	);
}

/**
 *  Get edit ticket form field modal
 */
function wpsc_get_edit_tff(id, nonce) {

	wpsc_show_modal();
	var data = { action: 'wpsc_get_edit_tff', id, _ajax_nonce: nonce };
	jQuery.post(
		supportcandy.ajax_url,
		data,
		function (response) {

			// Set to modal.
			jQuery( '.wpsc-modal-header' ).text( response.title );
			jQuery( '.wpsc-modal-body' ).html( response.body );
			jQuery( '.wpsc-modal-footer' ).html( response.footer );
			// Display modal.
			wpsc_show_modal_inner_container();
		}
	);
}

/**
 * Update ticket form field
 */
function wpsc_set_edit_tff(el) {

	jQuery( '.wpsc-modal-footer button' ).attr( 'disabled', true );
	jQuery( el ).text( supportcandy.translations.please_wait );

	var form     = jQuery( '.frm-edit-ticket-form-field' )[0];
	var dataform = new FormData( form );

	dataform.append( 'visibility', JSON.stringify( wpsc_get_condition_json( 'visibility' ) ) );

	jQuery.ajax(
		{
			url: supportcandy.ajax_url,
			type: 'POST',
			data: dataform,
			processData: false,
			contentType: false
		}
	).done(
		function (res) {
			wpsc_close_modal();
			wpsc_get_tff();
		}
	);
}

/**
 *  Delete ticket form field
 */
function wpsc_delete_tff(id, nonce) {

	var flag = confirm( supportcandy.translations.confirm );
	if ( ! flag) {
		return;
	}

	var data = { action: 'wpsc_delete_tff', id, _ajax_nonce: nonce };
	jQuery.post(
		supportcandy.ajax_url,
		data,
		function (res) {
			wpsc_get_tff();
		}
	);
}

/**
 *  Get ticket fields
 */
function wpsc_get_ticket_fields(is_humbargar = false) {

	if (is_humbargar) {
		wpsc_toggle_humbargar();
	}

	jQuery( '.wpsc-setting-nav, .wpsc-humbargar-menu-item' ).removeClass( 'active' );
	jQuery( '.wpsc-setting-nav.ticket-fields, .wpsc-humbargar-menu-item.ticket-fields' ).addClass( 'active' );
	jQuery( '.wpsc-humbargar-title' ).html( supportcandy.humbargar_titles.ticket_fields );

	window.history.replaceState( {}, null, 'admin.php?page=wpsc-ticket-form&section=ticket-fields' );
	jQuery( '.wpsc-setting-body' ).html( supportcandy.loader_html );

	wpsc_scroll_top();

	var data = { action: 'wpsc_get_ticket_fields' };
	jQuery.post(
		supportcandy.ajax_url,
		data,
		function (response) {
			jQuery( '.wpsc-setting-body' ).html( response );
			wpsc_reset_responsive_style();
		}
	);

}

/**
 *  Get customer fields
 */
function wpsc_get_customer_fields(is_humbargar = false) {

	if (is_humbargar) {
		wpsc_toggle_humbargar();
	}

	jQuery( '.wpsc-setting-nav, .wpsc-humbargar-menu-item' ).removeClass( 'active' );
	jQuery( '.wpsc-setting-nav.customer-fields, .wpsc-humbargar-menu-item.customer-fields' ).addClass( 'active' );
	jQuery( '.wpsc-humbargar-title' ).html( supportcandy.humbargar_titles.customer_fields );

	window.history.replaceState( {}, null, 'admin.php?page=wpsc-ticket-form&section=customer-fields' );
	jQuery( '.wpsc-setting-body' ).html( supportcandy.loader_html );

	wpsc_scroll_top();

	var data = { action: 'wpsc_get_customer_fields' };
	jQuery.post(
		supportcandy.ajax_url,
		data,
		function (response) {
			jQuery( '.wpsc-setting-body' ).html( response );
			wpsc_reset_responsive_style();
		}
	);
}

/**
 *  Get agent only fields
 */
function wpsc_get_agent_only_fields(is_humbargar = false) {

	if (is_humbargar) {
		wpsc_toggle_humbargar();
	}

	jQuery( '.wpsc-setting-nav, .wpsc-humbargar-menu-item' ).removeClass( 'active' );
	jQuery( '.wpsc-setting-nav.agent-only-fields, .wpsc-humbargar-menu-item.agent-only-fields' ).addClass( 'active' );
	jQuery( '.wpsc-humbargar-title' ).html( supportcandy.humbargar_titles.agent_only_fields );

	window.history.replaceState( {}, null, 'admin.php?page=wpsc-ticket-form&section=agent-only-fields' );
	jQuery( '.wpsc-setting-body' ).html( supportcandy.loader_html );

	wpsc_scroll_top();

	var data = { action: 'wpsc_get_agent_only_fields' };
	jQuery.post(
		supportcandy.ajax_url,
		data,
		function (response) {
			jQuery( '.wpsc-setting-body' ).html( response );
			wpsc_reset_responsive_style();
		}
	);
}

/**
 * Get add new ticket field
 */
function wpsc_get_add_new_custom_field(field, nonce) {

	jQuery( '.wpsc-setting-section-body' ).html( supportcandy.loader_html );
	var data = { action: 'wpsc_get_add_new_custom_field', field, _ajax_nonce: nonce };
	jQuery.post(
		supportcandy.ajax_url,
		data,
		function (response) {
			jQuery( '.wpsc-setting-section-body' ).html( response );
		}
	);
}

/**
 *  Get edit ticket field modal
 */
function wpsc_get_edit_custom_field(id, nonce) {

	jQuery( '.wpsc-setting-section-body' ).html( supportcandy.loader_html );
	var data = {
		action: 'wpsc_get_edit_custom_field',
		cf_id: id,
		_ajax_nonce: nonce
	};
	jQuery.post(
		supportcandy.ajax_url,
		data,
		function (response) {
			jQuery( '.wpsc-setting-section-body' ).html( response );
		}
	);
}

/**
 * Submit delete an ticket form field
 */
function wpsc_delete_custom_field(id, nonce) {

	var flag = confirm( supportcandy.translations.confirm );
	if ( ! flag) {
		return;
	}

	jQuery( '.wpsc-setting-body' ).html( supportcandy.loader_html );

	var data = {
		action: 'wpsc_delete_custom_field',
		cf_id: id,
		_ajax_nonce: nonce
	};
	jQuery.post(
		supportcandy.ajax_url,
		data,
		function (res) {
			jQuery( '.wpsc-setting-nav.active' ).trigger( 'click' );
		}
	);
}

/**
 * Get agent ticket list settings
 */
function wpsc_get_agent_tl_settings(is_humbargar = false) {

	if (is_humbargar) {
		wpsc_toggle_humbargar();
	}

	jQuery( '.wpsc-setting-nav, .wpsc-humbargar-menu-item' ).removeClass( 'active' );
	jQuery( '.wpsc-setting-nav.agent-ticket-list, .wpsc-humbargar-menu-item.agent-ticket-list' ).addClass( 'active' );
	jQuery( '.wpsc-humbargar-title' ).html( supportcandy.humbargar_titles.agent_ticket_list );

	if (supportcandy.current_section !== 'agent-ticket-list') {
		supportcandy.current_section = 'agent-ticket-list';
		supportcandy.current_tab     = 'list-items';
	}

	window.history.replaceState( {}, null, 'admin.php?page=wpsc-ticket-list&section=' + supportcandy.current_section + '&tab=' + supportcandy.current_tab );
	jQuery( '.wpsc-setting-body' ).html( supportcandy.loader_html );

	wpsc_scroll_top();

	var data = {
		action: 'wpsc_get_agent_tl_settings',
		tab: supportcandy.current_tab
	};
	jQuery.post(
		supportcandy.ajax_url,
		data,
		function (response) {
			jQuery( '.wpsc-setting-body' ).html( response );
			wpsc_reset_responsive_style();
			jQuery( '.wpsc-setting-tab-container button.' + supportcandy.current_tab ).trigger( "click" );
		}
	);
}

/**
 * Get customer ticket list
 */
function wpsc_get_customer_tl_settings(is_humbargar = false) {

	if (is_humbargar) {
		wpsc_toggle_humbargar();
	}

	jQuery( '.wpsc-setting-nav, .wpsc-humbargar-menu-item' ).removeClass( 'active' );
	jQuery( '.wpsc-setting-nav.customer-ticket-list, .wpsc-humbargar-menu-item.customer-ticket-list' ).addClass( 'active' );
	jQuery( '.wpsc-humbargar-title' ).html( supportcandy.humbargar_titles.customer_ticket_list );

	if (supportcandy.current_section !== 'customer-ticket-list') {
		supportcandy.current_section = 'customer-ticket-list';
		supportcandy.current_tab     = 'list-items';
	}

	window.history.replaceState( {}, null, 'admin.php?page=wpsc-ticket-list&section=' + supportcandy.current_section + '&tab=' + supportcandy.current_tab );
	jQuery( '.wpsc-setting-body' ).html( supportcandy.loader_html );

	wpsc_scroll_top();

	var data = {
		action: 'wpsc_get_customer_tl_settings',
		tab: supportcandy.current_tab
	};
	jQuery.post(
		supportcandy.ajax_url,
		data,
		function (response) {
			jQuery( '.wpsc-setting-body' ).html( response );
			wpsc_reset_responsive_style();
			jQuery( '.wpsc-setting-tab-container button.' + supportcandy.current_tab ).trigger( "click" );
		}
	);
}

/**
 * Get ticket list more settings
 */
function wpsc_get_tl_more_settigns(is_humbargar = false) {

	if (is_humbargar) {
		wpsc_toggle_humbargar();
	}

	jQuery( '.wpsc-setting-nav, .wpsc-humbargar-menu-item' ).removeClass( 'active' );
	jQuery( '.wpsc-setting-nav.more-settings, .wpsc-humbargar-menu-item.more-settings' ).addClass( 'active' );
	jQuery( '.wpsc-humbargar-title' ).html( supportcandy.humbargar_titles.more_settings );

	if (supportcandy.current_section !== 'more-settings') {
		supportcandy.current_section = 'more-settings';
		supportcandy.current_tab     = 'agent-view';
	}

	window.history.replaceState( {}, null, 'admin.php?page=wpsc-ticket-list&section=more-settings' );
	jQuery( '.wpsc-setting-body' ).html( supportcandy.loader_html );

	wpsc_scroll_top();

	var data = { action: 'wpsc_get_tl_more_settigns' };
	jQuery.post(
		supportcandy.ajax_url,
		data,
		function (response) {
			jQuery( '.wpsc-setting-body' ).html( response );
			wpsc_reset_responsive_style();
			jQuery( '.wpsc-setting-tab-container button.' + supportcandy.current_tab ).trigger( "click" );
		}
	);
}

/**
 * Get agent ticket list items
 */
function wpsc_get_agent_tl_items() {

	supportcandy.current_tab = 'list-items';
	jQuery( '.wpsc-setting-tab-container button' ).removeClass( 'active' );
	jQuery( '.wpsc-setting-tab-container button.' + supportcandy.current_tab ).addClass( 'active' );

	window.history.replaceState( {}, null, 'admin.php?page=wpsc-ticket-list&section=' + supportcandy.current_section + '&tab=' + supportcandy.current_tab );
	jQuery( '.wpsc-setting-section-body' ).html( supportcandy.loader_html );

	wpsc_scroll_top();

	var data = { action: 'wpsc_get_agent_tl_items' };
	jQuery.post(
		supportcandy.ajax_url,
		data,
		function (response) {
			jQuery( '.wpsc-setting-section-body' ).html( response );
			wpsc_reset_responsive_style();
		}
	);
}

/**
 * Set add new agent list items
 */
function wpsc_set_add_agent_tl_item(el) {

	var form     = jQuery( '.frm-add-agent-tl-items' )[0];
	var dataform = new FormData( form );

	var cf_id = dataform.getAll( 'cf_id[]' );
	if ( ! cf_id.length ) {
		alert( supportcandy.translations.req_fields_missing );
		return;
	}

	jQuery( '.wpsc-modal-footer button' ).attr( 'disabled', true );
	jQuery( el ).text( supportcandy.translations.please_wait );

	jQuery.ajax(
		{
			url: supportcandy.ajax_url,
			type: 'POST',
			data: dataform,
			processData: false,
			contentType: false
		}
	).done(
		function (res) {
			wpsc_close_modal();
			wpsc_get_agent_tl_items();
		}
	);
}

/**
 * Set delete agent ticket list item
 */
function wpsc_delete_agent_tl_item(slug, nonce) {

	if (confirm( supportcandy.translations.confirm )) {
		var data = {
			action: 'wpsc_delete_agent_tl_item',
			slug: slug,
			_ajax_nonce: nonce
		};

		jQuery( '.wpsc-modal-footer button' ).attr( 'disabled', true );
		jQuery( slug ).text( supportcandy.translations.please_wait );
		jQuery.post(
			supportcandy.ajax_url,
			data,
			function (res) {
				wpsc_get_agent_tl_items();
			}
		);
	} else {
		return false;
	}
}

/**
 * Get agent filter items
 */
function wpsc_get_agent_filter_items() {

	supportcandy.current_tab = 'filter-items';
	jQuery( '.wpsc-setting-tab-container button' ).removeClass( 'active' );
	jQuery( '.wpsc-setting-tab-container button.' + supportcandy.current_tab ).addClass( 'active' );

	window.history.replaceState( {}, null, 'admin.php?page=wpsc-ticket-list&section=' + supportcandy.current_section + '&tab=' + supportcandy.current_tab );
	jQuery( '.wpsc-setting-section-body' ).html( supportcandy.loader_html );

	wpsc_scroll_top();

	var data = { action: 'wpsc_get_agent_filter_items' };
	jQuery.post(
		supportcandy.ajax_url,
		data,
		function (response) {
			jQuery( '.wpsc-setting-section-body' ).html( response );
			wpsc_reset_responsive_style();
		}
	);
}

/**
 * Set add new agent list items
 */
function wpsc_set_add_atl_filter_item(el) {

	var form     = jQuery( '.frm-add-agent-tl-filter-items' )[0];
	var dataform = new FormData( form );

	var cf_id = dataform.getAll( 'agent-tl-filter-id[]' );
	if ( ! cf_id.length ) {
		alert( supportcandy.translations.req_fields_missing );
		return;
	}

	jQuery( '.wpsc-modal-footer button' ).attr( 'disabled', true );
	jQuery( el ).text( supportcandy.translations.please_wait );
	jQuery.ajax(
		{
			url: supportcandy.ajax_url,
			type: 'POST',
			data: dataform,
			processData: false,
			contentType: false
		}
	).done(
		function (res) {
			wpsc_close_modal();
			wpsc_get_agent_filter_items();
		}
	);
}

/**
 * Get delete agent ticket list item
 *
 * @param {*} slug
 */
function wpsc_delete_atl_filter_item(slug, nonce) {

	if (confirm( supportcandy.translations.confirm )) {
		var data = {
			action: 'wpsc_delete_atl_filter_item',
			slug: slug,
			_ajax_nonce: nonce
		};
		jQuery.post(
			supportcandy.ajax_url,
			data,
			function (res) {
				wpsc_get_agent_filter_items();
			}
		);
	}
}

/**
 * Get agent default filters
 */
function wpsc_get_atl_default_filters() {

	supportcandy.current_tab = 'default-filters';
	jQuery( '.wpsc-setting-tab-container button' ).removeClass( 'active' );
	jQuery( '.wpsc-setting-tab-container button.' + supportcandy.current_tab ).addClass( 'active' );

	window.history.replaceState( {}, null, 'admin.php?page=wpsc-ticket-list&section=' + supportcandy.current_section + '&tab=' + supportcandy.current_tab );
	jQuery( '.wpsc-setting-section-body' ).html( supportcandy.loader_html );

	wpsc_scroll_top();

	var data = { action: 'wpsc_get_atl_default_filters' };
	jQuery.post(
		supportcandy.ajax_url,
		data,
		function (response) {
			jQuery( '.wpsc-setting-section-body' ).html( response );
			wpsc_reset_responsive_style();
		}
	);
}

/**
 * Add agent default filter
 */
function wpsc_get_add_atl_default_filter() {

	wpsc_show_modal();
	var data = { action: 'wpsc_get_add_atl_default_filter' };
	jQuery.post(
		supportcandy.ajax_url,
		data,
		function (response) {

			jQuery( '.wpsc-modal-header' ).text( response.title );
			jQuery( '.wpsc-modal-body' ).html( response.body );
			jQuery( '.wpsc-modal-footer' ).html( response.footer );
			jQuery( '.wpsc-form-filter-container' ).append( jQuery( '.wpsc-form-filter-snippet' ).html() );
			jQuery( '.wpsc-form-filter-container select' ).selectWoo();
			wpsc_show_modal_inner_container();
		}
	);
}

/**
 * Set add agent default filters
 */
function wpsc_set_add_atl_default_filter(el) {

	var label = jQuery( '#wpsc-atl-df-label' ).val().trim();
	if ( ! label) {
		alert( supportcandy.translations.req_fields_missing );
		return;
	}

	var filters = wpsc_get_condition_json( 'default_filters' );
	if ( filters.length === 0 || ( filters.length === 1 && filters[0].length === 0 )  ) {
		alert( supportcandy.translations.req_fields_missing );
		return;
	}

	jQuery( '.wpsc-modal-footer button' ).attr( 'disabled', true );
	jQuery( el ).text( supportcandy.translations.please_wait );

	var form     = jQuery( '.wpsc-frm-add-atl-default-filter' )[0];
	var dataform = new FormData( form );
	dataform.append( 'filters', JSON.stringify( filters ) );

	jQuery.ajax(
		{
			url: supportcandy.ajax_url,
			type: 'POST',
			data: dataform,
			processData: false,
			contentType: false
		}
	).done(
		function (res) {
			wpsc_close_modal();
			wpsc_get_atl_default_filters();
		}
	);
}

/**
 * Get edit agent defualt filter
 */
function wpsc_get_edit_atl_default_filter(slug, nonce) {

	wpsc_show_modal();
	var data = { action: 'wpsc_get_edit_atl_default_filter', slug, _ajax_nonce: nonce };
	jQuery.post(
		supportcandy.ajax_url,
		data,
		function (response) {

			jQuery( '.wpsc-modal-header' ).text( response.title );
			jQuery( '.wpsc-modal-body' ).html( response.body );
			jQuery( '.wpsc-modal-footer' ).html( response.footer );
			wpsc_show_modal_inner_container();
		}
	);
}

/**
 * Set edit agent default filter
 */
function wpsc_set_edit_atl_default_filter(el, flag) {

	var label = jQuery( '#wpsc-atl-df-label' ).val().trim();
	if ( ! label ) {
		alert( supportcandy.translations.req_fields_missing );
		return;
	}

	var form     = jQuery( '.wpsc-frm-edit-atl-default-filter' )[0];
	var dataform = new FormData( form );

	if ( flag ) {
		var filters = wpsc_get_condition_json( 'default_filters' );
		if ( filters.length === 0 || ( filters.length === 1 && filters[0].length === 0 )  ) {
			alert( supportcandy.translations.req_fields_missing );
			return;
		}
		dataform.append( 'filters', JSON.stringify( filters ) );
	}

	jQuery( '.wpsc-modal-footer button' ).attr( 'disabled', true );
	jQuery( el ).text( supportcandy.translations.please_wait );

	jQuery.ajax(
		{
			url: supportcandy.ajax_url,
			type: 'POST',
			data: dataform,
			processData: false,
			contentType: false
		}
	).done(
		function (res) {
			wpsc_close_modal();
			wpsc_get_atl_default_filters();
		}
	);
}

/**
 * Delete agent default filter
 */
function wpsc_delete_atl_default_filter(slug, nonce) {

	var flag = confirm( supportcandy.translations.confirm );
	if ( ! flag) {
		return;
	}

	jQuery( '.wpsc-setting-section-body' ).html( supportcandy.loader_html );

	var data = { action: 'wpsc_delete_atl_default_filter', slug, _ajax_nonce: nonce };
	jQuery.post(
		supportcandy.ajax_url,
		data,
		function (response) {
			wpsc_get_atl_default_filters();
		}
	);
}

/**
 * Get customer ticket list items
 */
function wpsc_get_customer_tl_items() {

	supportcandy.current_tab = 'list-items';
	jQuery( '.wpsc-setting-tab-container button' ).removeClass( 'active' );
	jQuery( '.wpsc-setting-tab-container button.' + supportcandy.current_tab ).addClass( 'active' );

	window.history.replaceState( {}, null, 'admin.php?page=wpsc-ticket-list&section=' + supportcandy.current_section + '&tab=' + supportcandy.current_tab );
	jQuery( '.wpsc-setting-section-body' ).html( supportcandy.loader_html );

	wpsc_scroll_top();

	var data = { action: 'wpsc_get_customer_tl_items' };
	jQuery.post(
		supportcandy.ajax_url,
		data,
		function (response) {
			jQuery( '.wpsc-setting-section-body' ).html( response );
			wpsc_reset_responsive_style();
		}
	);
}

/**
 * Get customer default filters
 */
function wpsc_get_ctl_default_filters() {

	supportcandy.current_tab = 'default-filters';
	jQuery( '.wpsc-setting-tab-container button' ).removeClass( 'active' );
	jQuery( '.wpsc-setting-tab-container button.' + supportcandy.current_tab ).addClass( 'active' );

	window.history.replaceState( {}, null, 'admin.php?page=wpsc-ticket-list&section=' + supportcandy.current_section + '&tab=' + supportcandy.current_tab );
	jQuery( '.wpsc-setting-section-body' ).html( supportcandy.loader_html );

	wpsc_scroll_top();

	var data = { action: 'wpsc_get_ctl_default_filters' };
	jQuery.post(
		supportcandy.ajax_url,
		data,
		function (response) {
			jQuery( '.wpsc-setting-section-body' ).html( response );
			wpsc_reset_responsive_style();
		}
	);
}

/**
 * Add customer default filter
 */
function wpsc_get_add_ctl_default_filter() {

	wpsc_show_modal();
	var data = { action: 'wpsc_get_add_ctl_default_filter' };
	jQuery.post(
		supportcandy.ajax_url,
		data,
		function (response) {

			jQuery( '.wpsc-modal-header' ).text( response.title );
			jQuery( '.wpsc-modal-body' ).html( response.body );
			jQuery( '.wpsc-modal-footer' ).html( response.footer );
			jQuery( '.wpsc-form-filter-container' ).append( jQuery( '.wpsc-form-filter-snippet' ).html() );
			jQuery( '.wpsc-form-filter-container select' ).selectWoo();
			wpsc_show_modal_inner_container();
		}
	);
}

/**
 * Set add customer default filters
 */
function wpsc_set_add_ctl_default_filter(el) {

	var label = jQuery( '#wpsc-ctl-df-label' ).val().trim();
	if ( ! label) {
		alert( supportcandy.translations.req_fields_missing );
		return;
	}

	var filters = wpsc_get_condition_json( 'default_filters' );
	if ( filters.length === 0 || ( filters.length === 1 && filters[0].length === 0 )  ) {
		alert( supportcandy.translations.req_fields_missing );
		return;
	}

	jQuery( '.wpsc-modal-footer button' ).attr( 'disabled', true );
	jQuery( el ).text( supportcandy.translations.please_wait );

	var form     = jQuery( '.wpsc-frm-add-ctl-default-filter' )[0];
	var dataform = new FormData( form );
	dataform.append( 'filters', JSON.stringify( filters ) );

	jQuery.ajax(
		{
			url: supportcandy.ajax_url,
			type: 'POST',
			data: dataform,
			processData: false,
			contentType: false
		}
	).done(
		function (res) {
			wpsc_close_modal();
			wpsc_get_ctl_default_filters();
		}
	);
}

/**
 * Get edit customer defualt filter
 */
function wpsc_get_edit_ctl_default_filter(slug, nonce) {

	wpsc_show_modal();
	var data = { action: 'wpsc_get_edit_ctl_default_filter', slug, _ajax_nonce: nonce };
	jQuery.post(
		supportcandy.ajax_url,
		data,
		function (response) {

			jQuery( '.wpsc-modal-header' ).text( response.title );
			jQuery( '.wpsc-modal-body' ).html( response.body );
			jQuery( '.wpsc-modal-footer' ).html( response.footer );
			wpsc_show_modal_inner_container();
		}
	);
}

/**
 * Set edit customer default filter
 */
function wpsc_set_edit_ctl_default_filter(el, flag) {

	var label = jQuery( '#wpsc-ctl-df-label' ).val().trim();
	if ( ! label) {
		alert( supportcandy.translations.req_fields_missing );
		return;
	}

	var form = jQuery( '.wpsc-frm-edit-ctl-default-filter' )[0];
	var dataform = new FormData( form );

	if ( flag ) {
		var filters = wpsc_get_condition_json( 'default_filters' );
		if ( filters.length === 0 || ( filters.length === 1 && filters[0].length === 0 )  ) {
			alert( supportcandy.translations.req_fields_missing );
			return;
		}
		dataform.append( 'filters', JSON.stringify( filters ) );
	}

	jQuery( '.wpsc-modal-footer button' ).attr( 'disabled', true );
	jQuery( el ).text( supportcandy.translations.please_wait );

	jQuery.ajax(
		{
			url: supportcandy.ajax_url,
			type: 'POST',
			data: dataform,
			processData: false,
			contentType: false
		}
	).done(
		function (res) {
			wpsc_close_modal();
			wpsc_get_ctl_default_filters();
		}
	);
}

/**
 * Delete customer default filter
 */
function wpsc_delete_ctl_default_filter(slug, nonce) {

	var flag = confirm( supportcandy.translations.confirm );
	if ( ! flag) {
		return;
	}

	jQuery( '.wpsc-setting-section-body' ).html( supportcandy.loader_html );

	var data = { action: 'wpsc_delete_ctl_default_filter', slug, _ajax_nonce: nonce };
	jQuery.post(
		supportcandy.ajax_url,
		data,
		function (response) {
			wpsc_get_ctl_default_filters();
		}
	);
}

/**
 * Set add new customer list items
 */
function wpsc_set_add_customer_tl_item(el) {

	var form     = jQuery( '.frm-add-customer-tl-items' )[0];
	var dataform = new FormData( form );

	var cf_id = dataform.getAll( 'cf_id[]' );
	if ( ! cf_id.length ) {
		alert( supportcandy.translations.req_fields_missing );
		return;
	}

	jQuery( '.wpsc-modal-footer button' ).attr( 'disabled', true );
	jQuery( el ).text( supportcandy.translations.please_wait );
	jQuery.ajax(
		{
			url: supportcandy.ajax_url,
			type: 'POST',
			data: dataform,
			processData: false,
			contentType: false
		}
	).done(
		function (res) {
			wpsc_close_modal();
			wpsc_get_customer_tl_items();
		}
	);
}

/**
 * Set delete customer ticket list item
 */
function wpsc_delete_customer_tl_item(slug, nonce) {

	if (confirm( supportcandy.translations.confirm )) {
		var data = {
			action: 'wpsc_delete_customer_tl_item',
			slug: slug,
			_ajax_nonce: nonce
		};

		jQuery( '.wpsc-modal-footer button' ).attr( 'disabled', true );
		jQuery( slug ).text( supportcandy.translations.please_wait );
		jQuery.post(
			supportcandy.ajax_url,
			data,
			function (res) {
				wpsc_get_customer_tl_items();
			}
		);
	} else {
		return false;
	}
}

/**
 * Get customer filter items
 */
function wpsc_get_customer_filter_items() {

	supportcandy.current_tab = 'filter-items';
	jQuery( '.wpsc-setting-tab-container button' ).removeClass( 'active' );
	jQuery( '.wpsc-setting-tab-container button.' + supportcandy.current_tab ).addClass( 'active' );

	window.history.replaceState( {}, null, 'admin.php?page=wpsc-ticket-list&section=' + supportcandy.current_section + '&tab=' + supportcandy.current_tab );
	jQuery( '.wpsc-setting-section-body' ).html( supportcandy.loader_html );

	wpsc_scroll_top();

	var data = { action: 'wpsc_get_customer_filter_items' };
	jQuery.post(
		supportcandy.ajax_url,
		data,
		function (response) {
			jQuery( '.wpsc-setting-section-body' ).html( response );
			wpsc_reset_responsive_style();
		}
	);
}

/**
 * Get add customer ticket list filter items modal UI
 */
function wpsc_get_add_ctl_filter_item() {

	wpsc_show_modal();
	var data = { action: 'wpsc_get_add_ctl_filter_item' };
	jQuery.post(
		supportcandy.ajax_url,
		data,
		function (response) {

			// Set to modal.
			jQuery( '.wpsc-modal-header' ).text( response.title );
			jQuery( '.wpsc-modal-body' ).html( response.body );
			jQuery( '.wpsc-modal-footer' ).html( response.footer );
			// Display modal.
			wpsc_show_modal_inner_container();
		}
	);
}

/**
 * Set add new customer list items
 */
function wpsc_set_add_ctl_filter_item(el) {

	var form     = jQuery( '.frm-add-customer-tl-filter-items' )[0];
	var dataform = new FormData( form );

	var cf_id = dataform.getAll( 'customer-tl-filter-id[]' );
	if ( ! cf_id.length ) {
		alert( supportcandy.translations.req_fields_missing );
		return;
	}

	jQuery( '.wpsc-modal-footer button' ).attr( 'disabled', true );
	jQuery( el ).text( supportcandy.translations.please_wait );
	jQuery.ajax(
		{
			url: supportcandy.ajax_url,
			type: 'POST',
			data: dataform,
			processData: false,
			contentType: false
		}
	).done(
		function (res) {
			wpsc_close_modal();
			wpsc_get_customer_filter_items();
		}
	);
}

/**
 * Get delete customer ticket list item
 *
 * @param {*} slug
 */
function wpsc_delete_ctl_filter_item(slug, nonce) {

	if (confirm( supportcandy.translations.confirm )) {
		var data = {
			action: 'wpsc_delete_ctl_filter_item',
			slug: slug,
			_ajax_nonce: nonce
		};
		jQuery.post(
			supportcandy.ajax_url,
			data,
			function (res) {
				wpsc_get_customer_filter_items();
			}
		);
	}
}

/**
 *  Get Thank You Page Settings
 */
function wpsc_get_gs_thankyou() {

	supportcandy.current_tab = 'thank-you-page';
	jQuery( '.wpsc-setting-tab-container button' ).removeClass( 'active' );
	jQuery( '.wpsc-setting-tab-container button.' + supportcandy.current_tab ).addClass( 'active' );

	window.history.replaceState( {}, null, 'admin.php?page=wpsc-settings&section=' + supportcandy.current_section + '&tab=' + supportcandy.current_tab );
	jQuery( '.wpsc-setting-section-body' ).html( supportcandy.loader_html );

	wpsc_scroll_top();

	var data = { action: 'wpsc_get_gs_thankyou' };
	jQuery.post(
		supportcandy.ajax_url,
		data,
		function (response) {
			jQuery( '.wpsc-setting-section-body' ).html( response );
			wpsc_reset_responsive_style();
		}
	);
}

/**
 * Reset Thankyou page settings
 */
function wpsc_reset_gs_thankyou(el, nonce) {

	jQuery( '.wpsc-modal-footer button' ).attr( 'disabled', true );
	jQuery( el ).text( supportcandy.translations.please_wait );
	var data = { action: 'wpsc_reset_gs_thankyou', _ajax_nonce: nonce };
	jQuery.post(
		supportcandy.ajax_url,
		data,
		function (response) {
			wpsc_get_gs_thankyou();
		}
	);
}

/**
 * Set Thankyou Page Settings
 */
function wpsc_set_gs_thankyou(el) {
	var form        = jQuery( '.wpsc-frm-gs-thankyoupage' )[0];
	var dataform    = new FormData( form );
	
	var is_tinymce = (typeof tinyMCE !== "undefined") && tinyMCE.get('wpsc-html-agent') && !tinyMCE.get('wpsc-html-agent').isHidden();
	var description = is_tinymce ? tinyMCE.get('wpsc-html-agent').getContent().trim() : jQuery( '#wpsc-html-agent' ).val();
	dataform.append( 'html-agent', description );
	
	var is_tinymce = (typeof tinyMCE !== "undefined") && tinyMCE.get('wpsc-html-customer') && !tinyMCE.get('wpsc-html-customer').isHidden();
	description = is_tinymce ? tinyMCE.get('wpsc-html-customer').getContent().trim() : jQuery( '#wpsc-html-customer' ).val();
	dataform.append( 'html-customer', description );

	jQuery( '.wpsc-modal-footer button' ).attr( 'disabled', true );
	jQuery( el ).text( supportcandy.translations.please_wait );
	jQuery.ajax(
		{
			url: supportcandy.ajax_url,
			type: 'POST',
			data: dataform,
			processData: false,
			contentType: false
		}
	).done(
		function (res) {
			wpsc_get_gs_thankyou();
		}
	);
}

/**
 * File attachment settings
 */
function wpsc_get_gs_file_attachments() {

	supportcandy.current_tab = 'file-attachments';
	jQuery( '.wpsc-setting-tab-container button' ).removeClass( 'active' );
	jQuery( '.wpsc-setting-tab-container button.' + supportcandy.current_tab ).addClass( 'active' );

	window.history.replaceState( {}, null, 'admin.php?page=wpsc-settings&section=' + supportcandy.current_section + '&tab=' + supportcandy.current_tab );
	jQuery( '.wpsc-setting-section-body' ).html( supportcandy.loader_html );

	wpsc_scroll_top();

	var data = { action: 'wpsc_get_gs_file_attachments' };
	jQuery.post(
		supportcandy.ajax_url,
		data,
		function (response) {
			jQuery( '.wpsc-setting-section-body' ).html( response );
			wpsc_reset_responsive_style();
		}
	);
}

/**
 * Save page settings
 */
function wpsc_set_gs_file_attachments(el) {

	var form     = jQuery( '.wpsc-frm-gs-fa' )[0];
	var dataform = new FormData( form );
	jQuery( '.wpsc-modal-footer button' ).attr( 'disabled', true );
	jQuery( el ).text( supportcandy.translations.please_wait );
	jQuery.ajax(
		{
			url: supportcandy.ajax_url,
			type: 'POST',
			data: dataform,
			processData: false,
			contentType: false
		}
	).done(
		function (res) {
			wpsc_get_gs_file_attachments();
		}
	);
}

/**
 * Reset file attachments
 */
function wpsc_reset_gs_file_attachments(el, nonce) {

	jQuery( '.wpsc-modal-footer button' ).attr( 'disabled', true );
	jQuery( el ).text( supportcandy.translations.please_wait );
	var data = { action: 'wpsc_reset_gs_file_attachments', _ajax_nonce: nonce };
	jQuery.post(
		supportcandy.ajax_url,
		data,
		function (response) {
			wpsc_get_gs_file_attachments();
		}
	);
}

/**
 * Get email notifications general setting
 */
function wpsc_get_en_general_setting(is_humbargar = false) {

	if (is_humbargar) {
		wpsc_toggle_humbargar();
	}

	jQuery( '.wpsc-setting-nav, .wpsc-humbargar-menu-item' ).removeClass( 'active' );
	jQuery( '.wpsc-setting-nav.general-settings, .wpsc-humbargar-menu-item.general-settings' ).addClass( 'active' );
	jQuery( '.wpsc-humbargar-title' ).html( supportcandy.humbargar_titles.general_settings );

	window.history.replaceState( {}, null, 'admin.php?page=wpsc-email-notifications&section=general-settings' );
	jQuery( '.wpsc-setting-body' ).html( supportcandy.loader_html );

	wpsc_scroll_top();

	var data = { action: 'wpsc_get_en_general_setting' };
	jQuery.post(
		supportcandy.ajax_url,
		data,
		function (response) {
			jQuery( '.wpsc-setting-body' ).html( response );
			wpsc_reset_responsive_style();
		}
	);
}

/**
 * Save email notification general settings
 */
function wpsc_set_en_general(el) {

	var form     = jQuery( '.wpsc-frm-en-general' )[0];
	var dataform = new FormData( form );
	jQuery( '.wpsc-modal-footer button' ).attr( 'disabled', true );
	jQuery( el ).text( supportcandy.translations.please_wait );
	jQuery.ajax(
		{
			url: supportcandy.ajax_url,
			type: 'POST',
			data: dataform,
			processData: false,
			contentType: false
		}
	).done(
		function (res) {
			wpsc_get_en_general_setting();
		}
	);
}

/**
 * Reset email notification general settings
 */
function wpsc_reset_en_general(el) {

	jQuery( '.wpsc-modal-footer button' ).attr( 'disabled', true );
	jQuery( el ).text( supportcandy.translations.please_wait );
	var data = { action: 'wpsc_reset_en_general' };
	jQuery.post(
		supportcandy.ajax_url,
		data,
		function (response) {
			wpsc_get_en_general_setting();
		}
	);
}

/**
 * Get ticket notifications setting
 */
function wpsc_get_ticket_notifications(is_humbargar = false) {

	if (is_humbargar) {
		wpsc_toggle_humbargar();
	}

	jQuery( '.wpsc-setting-nav, .wpsc-humbargar-menu-item' ).removeClass( 'active' );
	jQuery( '.wpsc-setting-nav.ticket-notifications, .wpsc-humbargar-menu-item.ticket-notifications' ).addClass( 'active' );
	jQuery( '.wpsc-humbargar-title' ).html( supportcandy.humbargar_titles.ticket_notifications );

	window.history.replaceState( {}, null, 'admin.php?page=wpsc-email-notifications&section=ticket-notifications' );
	jQuery( '.wpsc-setting-body' ).html( supportcandy.loader_html );

	wpsc_scroll_top();

	var data = { action: 'wpsc_get_ticket_notifications' };
	jQuery.post(
		supportcandy.ajax_url,
		data,
		function (response) {
			jQuery( '.wpsc-setting-body' ).html( response );
			wpsc_reset_responsive_style();
		}
	);
}

/**
 * Set add ticket notification
 */
function wpsc_en_set_add_ticket_notification(el) {

	var title = jQuery( el ).closest( '.modal' ).find( 'input[name=title]' ).val().trim();
	if ( ! title) {
		alert( supportcandy.translations.req_fields_missing );
		return;
	}

	jQuery( '.wpsc-modal-footer button' ).attr( 'disabled', true );
	jQuery( el ).text( supportcandy.translations.please_wait );

	var form     = jQuery( '.wpsc-frm-add-en' )[0];
	var dataform = new FormData( form );
	jQuery.ajax(
		{
			url: supportcandy.ajax_url,
			type: 'POST',
			data: dataform,
			processData: false,
			contentType: false
		}
	).done(
		function (response) {
			wpsc_close_modal();
			wpsc_en_get_edit_ticket_notification( response.index, response.nonce );
		}
	);
}

/**
 * Get edit email notification
 *
 * @param {int} id
 */
function wpsc_en_get_edit_ticket_notification(template_id, nonce) {

	jQuery( '.wpsc-setting-section-body' ).html( supportcandy.loader_html );
	var data = { action: 'wpsc_en_get_edit_ticket_notification', template_id, _ajax_nonce: nonce };
	jQuery.post(
		supportcandy.ajax_url,
		data,
		function (res) {
			jQuery( '.wpsc-setting-section-body' ).html( res );
		}
	);
}

/**
 * Set edit email notification
 */
function wpsc_en_set_edit_ticket_notification() {

	var form     = jQuery( '.wpsc-frm-edit-en' )[0];
	var dataform = new FormData( form );

	var title = dataform.get( 'title' ).trim();
	if ( ! title) {
		alert( supportcandy.translations.req_fields_missing );
		return;
	}

	var subject = dataform.get( 'subject' ).trim();
	if ( ! subject) {
		alert( supportcandy.translations.req_fields_missing );
		return;
	}

	var is_tinymce = (typeof tinyMCE != "undefined") && tinyMCE.activeEditor && ! tinyMCE.activeEditor.isHidden();
	var body       = is_tinymce ? tinyMCE.get( 'wpsc-en-body' ).getContent().trim() : dataform.get( 'body' ).trim();
	dataform.append( 'body', body );

	if ( ! body) {
		alert( supportcandy.translations.req_fields_missing );
		return;
	}

	// to.
	var toGeneral = dataform.getAll( 'to[general-recipients][]' );
	var toRoles   = dataform.getAll( 'to[agent-roles][]' );
	var toCustom  = dataform.get( 'to[custom]' ).trim();
	if ( ! (toGeneral.length || toRoles.length || toCustom)) {
		alert( supportcandy.translations.req_fields_missing );
		return;
	}

	dataform.append( 'conditions', JSON.stringify( wpsc_get_condition_json( 'conditions' ) ) );

	jQuery( '.wpsc-setting-section-body' ).html( supportcandy.loader_html );
	jQuery.ajax(
		{
			url: supportcandy.ajax_url,
			type: 'POST',
			data: dataform,
			processData: false,
			contentType: false
		}
	).done(
		function (res) {
			wpsc_get_ticket_notifications();
		}
	);
}

/**
 * Get clone ticket notification
 *
 * @param {INT} template_id
 */
function wpsc_en_get_clone_ticket_notification(template_id, nonce) {

	wpsc_show_modal();
	var data = {
		action: 'wpsc_en_get_clone_ticket_notification',
		template_id,
		_ajax_nonce: nonce
	};
	
	jQuery.post(
		supportcandy.ajax_url,
		data,
		function (response) {

			jQuery( '.wpsc-modal-header' ).text( response.title );
			jQuery( '.wpsc-modal-body' ).html( response.body );
			jQuery( '.wpsc-modal-footer' ).html( response.footer );

			wpsc_show_modal_inner_container();
		}
	);
}

/**
 * Set clone ticket notification
 */
function wpsc_en_set_clone_ticket_notification(el) {

	var form     = jQuery( '.wpsc-en-add-clone' )[0];
	var dataform = new FormData( form );

	if (dataform.get( 'title' ).trim() == '') {
		alert( supportcandy.translations.req_fields_missing );
		return;
	}

	jQuery( el ).text( supportcandy.translations.please_wait );
	jQuery.ajax(
		{
			url: supportcandy.ajax_url,
			type: 'POST',
			data: dataform,
			processData: false,
			contentType: false
		}
	).done(
		function (response) {
			wpsc_close_modal();
			wpsc_en_get_edit_ticket_notification( response.index, response.nonce );
		}
	);
}

/**
 * Disable  ticket notification
 */
function wpsc_en_enable_disable_template(template_id, status, nonce) {

	var data = { 
			action: 'wpsc_en_enable_disable_template', 
			template_id, 
			status, 
			_ajax_nonce: nonce 
		};

	jQuery.post(
		supportcandy.ajax_url,
		data,
		function (response) {
			wpsc_get_ticket_notifications();
		}
	);
}

/**
 * Delete email notification
 *
 * @param {int} template_id
 */
function wpsc_en_delete_ticket_notification(template_id, nonce) {

	var flag = confirm( supportcandy.translations.confirm );
	if ( ! flag) {
		return;
	}

	var data = {
		action: 'wpsc_en_delete_ticket_notification',
		template_id,
		_ajax_nonce: nonce
	};
	jQuery.post(
		supportcandy.ajax_url,
		data,
		function (res) {
			wpsc_get_ticket_notifications();
		}
	);
}

/**
 * Get miscellaneous settings
 *
 * @param {*} is_humbargar
 */
function wpsc_get_miscellaneous_settings(is_humbargar = false) {
	if (is_humbargar) {
		wpsc_toggle_humbargar();
	}

	jQuery( '.wpsc-setting-nav, .wpsc-humbargar-menu-item' ).removeClass( 'active' );
	jQuery( '.wpsc-setting-nav.miscellaneous-settings, .wpsc-humbargar-menu-item.miscellaneous-settings' ).addClass( 'active' );
	jQuery( '.wpsc-humbargar-title' ).html( supportcandy.humbargar_titles.miscellaneous_settings );

	if (supportcandy.current_section !== 'miscellaneous-settings') {
		supportcandy.current_section = 'miscellaneous-settings';
		supportcandy.current_tab     = 'term-and-conditions';
	}

	window.history.replaceState( {}, null, 'admin.php?page=wpsc-settings&section=miscellaneous-settings' );
	jQuery( '.wpsc-setting-body' ).html( supportcandy.loader_html );

	wpsc_scroll_top();

	var data = { action: 'wpsc_get_miscellaneous_settings' };
	jQuery.post(
		supportcandy.ajax_url,
		data,
		function (response) {
			jQuery( '.wpsc-setting-body' ).html( response );
			wpsc_reset_responsive_style();
			jQuery( '.wpsc-setting-tab-container button.' + supportcandy.current_tab ).trigger( "click" );
		}
	);
}

/**
 * Get ticket widget
 */
function wpsc_get_ticket_widget(is_humbargar) {

	if (is_humbargar) {
		wpsc_toggle_humbargar();
	}

	jQuery( '.wpsc-setting-nav, .wpsc-humbargar-menu-item' ).removeClass( 'active' );
	jQuery( '.wpsc-setting-nav.ticket-widgets, .wpsc-humbargar-menu-item.ticket-widgets' ).addClass( 'active' );
	jQuery( '.wpsc-humbargar-title' ).html( supportcandy.humbargar_titles.ticket_widget );

	window.history.replaceState( {}, null, 'admin.php?page=wpsc-settings&section=ticket-widgets' );
	jQuery( '.wpsc-setting-body' ).html( supportcandy.loader_html );

	wpsc_scroll_top();

	var data = { action: 'wpsc_get_ticket_widget' };
	jQuery.post(
		supportcandy.ajax_url,
		data,
		function (response) {
			jQuery( '.wpsc-setting-body' ).html( response );
			wpsc_reset_responsive_style();
		}
	);
}

/**
 * Get Terms and conditions settings
 */
function wpsc_get_ms_term_and_conditions() {

	supportcandy.current_tab = 'term-and-conditions';
	jQuery( '.wpsc-setting-tab-container button' ).removeClass( 'active' );
	jQuery( '.wpsc-setting-tab-container button.' + supportcandy.current_tab ).addClass( 'active' );

	window.history.replaceState( {}, null, 'admin.php?page=wpsc-settings&section=' + supportcandy.current_section + '&tab=' + supportcandy.current_tab );
	jQuery( '.wpsc-setting-section-body' ).html( supportcandy.loader_html );

	wpsc_scroll_top();
	var data = { action: 'wpsc_get_ms_term_and_conditions' };
	jQuery.post(
		supportcandy.ajax_url,
		data,
		function (response) {
			jQuery( '.wpsc-setting-section-body' ).html( response );
			wpsc_reset_responsive_style();
		}
	);
}

/**
 * Get agent view settings
 */
function wpsc_tl_ms_get_agent_view() {

	supportcandy.current_tab = 'agent-view';
	jQuery( '.wpsc-setting-tab-container button' ).removeClass( 'active' );
	jQuery( '.wpsc-setting-tab-container button.' + supportcandy.current_tab ).addClass( 'active' );

	window.history.replaceState( {}, null, 'admin.php?page=wpsc-ticket-list&section=' + supportcandy.current_section + '&tab=' + supportcandy.current_tab );
	jQuery( '.wpsc-setting-section-body' ).html( supportcandy.loader_html );

	wpsc_scroll_top();

	var data = { action: 'wpsc_tl_ms_get_agent_view' };
	jQuery.post(
		supportcandy.ajax_url,
		data,
		function (response) {
			jQuery( '.wpsc-setting-section-body' ).html( response );
			wpsc_reset_responsive_style();
		}
	);
}

/**
 * Save terms and conditions
 *
 * @param {*} el
 */
function wpsc_set_ms_term_and_conditions(el) {

	var form     = jQuery( '.wpsc-frm-ms-tandc' )[0];
	var dataform = new FormData( form );
	jQuery( '.wpsc-modal-footer button' ).attr( 'disabled', true );
	var is_tinymce = (typeof tinyMCE != "undefined") && tinyMCE.activeEditor && ! tinyMCE.activeEditor.isHidden();

	if (is_tinymce) {
		var description = tinyMCE.activeEditor.getContent().trim();
	} else {
		var description = jQuery( '#tandc-text' ).val();
	}
	dataform.append( 'tandc-text', description );
	jQuery( el ).text( supportcandy.translations.please_wait );
	jQuery.ajax(
		{
			url: supportcandy.ajax_url,
			type: 'POST',
			data: dataform,
			processData: false,
			contentType: false
		}
	).done(
		function (res) {
			wpsc_get_ms_term_and_conditions();
		}
	);
}

/**
 * Sort ticket widget
 */
function wpsc_set_tw_load_order(slugs, nonce) {

	jQuery( '.wpsc-setting-section-body' ).html( supportcandy.loader_html );
	var data = { action: 'wpsc_set_tw_load_order', slugs, _ajax_nonce: nonce };
	jQuery.post(
		supportcandy.ajax_url,
		data,
		function (res) {
			wpsc_get_ticket_widget();
		}
	);
}

/**
 * Ticket widget status
 */
function wpsc_get_tw_ticket_status() {

	wpsc_show_modal();
	var data = { action: 'wpsc_get_tw_ticket_status' };
	jQuery.post(
		supportcandy.ajax_url,
		data,
		function (response) {

			// Set to modal.
			jQuery( '.wpsc-modal-header' ).text( response.title );
			jQuery( '.wpsc-modal-body' ).html( response.body );
			jQuery( '.wpsc-modal-footer' ).html( response.footer );
			// Display modal.
			wpsc_show_modal_inner_container();
		}
	);
}

/**
 * Set ticket widget status
 */
function wpsc_set_tw_ticket_status(el) {

	var form     = jQuery( '.wpsc-frm-edit-ticket-status' )[0];
	var dataform = new FormData( form );
	
	if (dataform.get( 'label' ).trim() == '') {
		alert( supportcandy.translations.req_fields_missing );
		return;
	}
	
	jQuery( '.wpsc-modal-footer button' ).attr( 'disabled', true );
	jQuery( el ).text( supportcandy.translations.please_wait );

	jQuery.ajax(
		{
			url: supportcandy.ajax_url,
			type: 'POST',
			data: dataform,
			processData: false,
			contentType: false
		}
	).done(
		function (res) {
			wpsc_close_modal();
			wpsc_get_ticket_widget();
		}
	);
}

/**
 * Set agent view settings
 *
 * @param {*} el
 */
function wpsc_tl_ms_set_agent_view(el) {

	var form     = jQuery( '.wpsc-frm-tl-ms-agent-view' )[0];
	var dataform = new FormData( form );
	jQuery( '.wpsc-modal-footer button' ).attr( 'disabled', true );
	jQuery( el ).text( supportcandy.translations.please_wait );
	jQuery.ajax(
		{
			url: supportcandy.ajax_url,
			type: 'POST',
			data: dataform,
			processData: false,
			contentType: false
		}
	).done(
		function (res) {
			wpsc_tl_ms_get_agent_view();
		}
	);
}

/**
 * Reset terms and conditions
 *
 * @param {*} el
 */
function wpsc_reset_ms_term_and_conditions(el, nonce) {

	jQuery( '.wpsc-modal-footer button' ).attr( 'disabled', true );
	jQuery( el ).text( supportcandy.translations.please_wait );
	var data = { action: 'wpsc_reset_ms_term_and_conditions', _ajax_nonce: nonce };
	jQuery.post(
		supportcandy.ajax_url,
		data,
		function (response) {
			wpsc_get_ms_term_and_conditions();
		}
	);
}

/**
 * Get ticket widget raised by
 */
function wpsc_get_tw_raised_by() {

	wpsc_show_modal();
	var data = { action: 'wpsc_get_tw_raised_by' };
	jQuery.post(
		supportcandy.ajax_url,
		data,
		function (response) {

			// Set to modal.
			jQuery( '.wpsc-modal-header' ).text( response.title );
			jQuery( '.wpsc-modal-body' ).html( response.body );
			jQuery( '.wpsc-modal-footer' ).html( response.footer );
			// Display modal.
			wpsc_show_modal_inner_container();
		}
	);
}

/**
 * Reset agent view settings
 *
 * @param {*} el
 */
function wpsc_tl_ms_reset_agent_view(el, nonce) {

	jQuery( '.wpsc-modal-footer button' ).attr( 'disabled', true );
	jQuery( el ).text( supportcandy.translations.please_wait );
	var data = { action: 'wpsc_tl_ms_reset_agent_view', _ajax_nonce: nonce };
	jQuery.post(
		supportcandy.ajax_url,
		data,
		function (response) {
			wpsc_tl_ms_get_agent_view();
		}
	);
}

/**
 * Get reCaptcha settings
 */
function wpsc_get_ms_recaptcha() {

	supportcandy.current_tab = 'recaptcha';
	jQuery( '.wpsc-setting-tab-container button' ).removeClass( 'active' );
	jQuery( '.wpsc-setting-tab-container button.' + supportcandy.current_tab ).addClass( 'active' );

	window.history.replaceState( {}, null, 'admin.php?page=wpsc-settings&section=' + supportcandy.current_section + '&tab=' + supportcandy.current_tab );
	jQuery( '.wpsc-setting-section-body' ).html( supportcandy.loader_html );

	wpsc_scroll_top();

	var data = { action: 'wpsc_get_ms_recaptcha' };
	jQuery.post(
		supportcandy.ajax_url,
		data,
		function (response) {
			jQuery( '.wpsc-setting-section-body' ).html( response );
			wpsc_reset_responsive_style();
		}
	);
}

/**
 * Set ticket widget raised by
 */
function wpsc_set_tw_raised_by(el) {

	var form     = jQuery( '.wpsc-frm-edit-ticket-raised-by' )[0];
	var dataform = new FormData( form );
	
	if (dataform.get( 'label' ).trim() == '') {
		alert( supportcandy.translations.req_fields_missing );
		return;
	}

	jQuery( '.wpsc-modal-footer button' ).attr( 'disabled', true );
	jQuery( el ).text( supportcandy.translations.please_wait );

	jQuery.ajax(
		{
			url: supportcandy.ajax_url,
			type: 'POST',
			data: dataform,
			processData: false,
			contentType: false
		}
	).done(
		function (res) {
			wpsc_close_modal();
			wpsc_get_ticket_widget();
		}
	);
}

/**
 * Get customer view settings
 */
function wpsc_tl_ms_get_customer_view() {

	supportcandy.current_tab = 'customer-view';
	jQuery( '.wpsc-setting-tab-container button' ).removeClass( 'active' );
	jQuery( '.wpsc-setting-tab-container button.' + supportcandy.current_tab ).addClass( 'active' );

	window.history.replaceState( {}, null, 'admin.php?page=wpsc-ticket-list&section=' + supportcandy.current_section + '&tab=' + supportcandy.current_tab );
	jQuery( '.wpsc-setting-section-body' ).html( supportcandy.loader_html );

	wpsc_scroll_top();

	var data = { action: 'wpsc_tl_ms_get_customer_view' };
	jQuery.post(
		supportcandy.ajax_url,
		data,
		function (response) {
			jQuery( '.wpsc-setting-section-body' ).html( response );
			wpsc_reset_responsive_style();
		}
	);
}

/**
 * Save reCaptcha
 *
 * @param {*} el
 */
function wpsc_set_ms_recaptcha(el) {

	var form     = jQuery( '.wpsc-frm-ms-recaptcha' )[0];
	var dataform = new FormData( form );
	jQuery( '.wpsc-modal-footer button' ).attr( 'disabled', true );
	jQuery( el ).text( supportcandy.translations.please_wait );
	jQuery.ajax(
		{
			url: supportcandy.ajax_url,
			type: 'POST',
			data: dataform,
			processData: false,
			contentType: false
		}
	).done(
		function (res) {
			wpsc_get_ms_recaptcha();
		}
	);
}

/**
 * Get ticket widget ticket info
 */
function wpsc_get_tw_ticket_info() {

	wpsc_show_modal();
	var data = { action: 'wpsc_get_tw_ticket_info' };
	jQuery.post(
		supportcandy.ajax_url,
		data,
		function (response) {

			// Set to modal.
			jQuery( '.wpsc-modal-header' ).text( response.title );
			jQuery( '.wpsc-modal-body' ).html( response.body );
			jQuery( '.wpsc-modal-footer' ).html( response.footer );
			// Display modal.
			wpsc_show_modal_inner_container();
		}
	);
}

/**
 * Add new option snippet
 */
function wpsc_get_add_new_option() {

	jQuery( '.wpsc-options-container' ).append( jQuery( '.wpsc-add-option' ).html() );
	jQuery( '.wpsc-options-container' ).children().last().find( '.wpsc-add-option-container input:first' ).focus();
}

/**
 * Add new option for custom field
 */
function wpsc_add_new_option(el, nonce) {

	var submitBtn  = jQuery( el );
	var inputField = jQuery( el ).parent().find( 'input' ).first();

	var name = inputField.val().trim();
	if ( ! name) {
		return;
	}

	submitBtn.attr( 'disabled', true );
	inputField.attr( 'disabled', true );
	submitBtn.html( supportcandy.inline_loader );

	var data = { action: 'wpsc_add_new_option', name, _ajax_nonce: nonce };
	jQuery.post(
		supportcandy.ajax_url,
		data,
		function (response) {

			submitBtn.closest( '.wpsc-option-item' ).find( '.wpsc-option-listing-container .text' ).text( response.name );
			submitBtn.closest( '.wpsc-option-item' ).append( '<input type="hidden" class="option_id" name="options[]" value="' + response.id + '">' );
			submitBtn.parent().hide();
			submitBtn.closest( '.content' ).find( '.wpsc-option-listing-container' ).show();
			wpsc_change_def_val_option_single();
		}
	);
}

/**
 * Set ticket widget ticket info
 */
function wpsc_set_tw_ticket_info(el) {

	var form     = jQuery( '.wpsc-frm-edit-ticket-info' )[0];
	var dataform = new FormData( form );
	
	if (dataform.get( 'label' ).trim() == '') {
		alert( supportcandy.translations.req_fields_missing );
		return;
	}

	jQuery( '.wpsc-modal-footer button' ).attr( 'disabled', true );
	jQuery( el ).text( supportcandy.translations.please_wait );

	jQuery.ajax(
		{
			url: supportcandy.ajax_url,
			type: 'POST',
			data: dataform,
			processData: false,
			contentType: false
		}
	).done(
		function (res) {
			wpsc_close_modal();
			wpsc_get_ticket_widget();
		}
	);
}

/**
 * Reset reCaptcha
 *
 * @param {*} el
 */
function wpsc_reset_ms_recaptcha(el, nonce) {

	jQuery( '.wpsc-modal-footer button' ).attr( 'disabled', true );
	jQuery( el ).text( supportcandy.translations.please_wait );
	var data = { action: 'wpsc_reset_ms_recaptcha', _ajax_nonce: nonce };
	jQuery.post(
		supportcandy.ajax_url,
		data,
		function (response) {
			wpsc_get_ms_recaptcha();
		}
	);
}

/**
 * Set customer view settings
 *
 * @param {*} el
 */
function wpsc_tl_ms_set_customer_view(el) {

	var form     = jQuery( '.wpsc-frm-tl-ms-customer-view' )[0];
	var dataform = new FormData( form );
	jQuery( '.wpsc-modal-footer button' ).attr( 'disabled', true );
	jQuery( el ).text( supportcandy.translations.please_wait );
	jQuery.ajax(
		{
			url: supportcandy.ajax_url,
			type: 'POST',
			data: dataform,
			processData: false,
			contentType: false
		}
	).done(
		function (res) {
			wpsc_tl_ms_get_customer_view();
		}
	);
}

/**
 * Get ticket widget agents
 */
function wpsc_get_tw_agents() {

	wpsc_show_modal();
	var data = { action: 'wpsc_get_tw_agents' };
	jQuery.post(
		supportcandy.ajax_url,
		data,
		function (response) {

			// Set to modal.
			jQuery( '.wpsc-modal-header' ).text( response.title );
			jQuery( '.wpsc-modal-body' ).html( response.body );
			jQuery( '.wpsc-modal-footer' ).html( response.footer );
			// Display modal.
			wpsc_show_modal_inner_container();
		}
	);
}

/**
 * Reset customer view settings
 *
 * @param {*} el
 */
function wpsc_tl_ms_reset_customer_view(el, nonce) {

	jQuery( '.wpsc-modal-footer button' ).attr( 'disabled', true );
	jQuery( el ).text( supportcandy.translations.please_wait );
	var data = { action: 'wpsc_tl_ms_reset_customer_view', _ajax_nonce: nonce };
	jQuery.post(
		supportcandy.ajax_url,
		data,
		function (response) {
			wpsc_tl_ms_get_customer_view();
		}
	);
}

/**
 * Set ticket widget agents
 */
function wpsc_set_tw_agents(el) {

	var form     = jQuery( '.wpsc-frm-edit-agents' )[0];
	var dataform = new FormData( form );
	
	if (dataform.get( 'label' ).trim() == '') {
		alert( supportcandy.translations.req_fields_missing );
		return;
	}

	jQuery( '.wpsc-modal-footer button' ).attr( 'disabled', true );
	jQuery( el ).text( supportcandy.translations.please_wait );

	jQuery.ajax(
		{
			url: supportcandy.ajax_url,
			type: 'POST',
			data: dataform,
			processData: false,
			contentType: false
		}
	).done(
		function (res) {
			wpsc_close_modal();
			wpsc_get_ticket_widget();
		}
	);
}

/**
 * Get ticket widget ticket fields
 */
function wpsc_get_tw_ticket_fields() {

	wpsc_show_modal();
	var data = { action: 'wpsc_get_tw_ticket_fields' };
	jQuery.post(
		supportcandy.ajax_url,
		data,
		function (response) {

			// Set to modal.
			jQuery( '.wpsc-modal-header' ).text( response.title );
			jQuery( '.wpsc-modal-body' ).html( response.body );
			jQuery( '.wpsc-modal-footer' ).html( response.footer );
			// Display modal.
			wpsc_show_modal_inner_container();
		}
	);
}

/**
 * Load edit option view
 */
function wpsc_edit_option(el) {

	var editBtn = jQuery( el );
	var text    = editBtn.closest( '.wpsc-option-listing-container' ).find( '.text' ).text();
	editBtn.closest( '.content' ).find( '.edit-option-text' ).val( text );
	editBtn.closest( '.wpsc-option-listing-container' ).hide();
	editBtn.closest( '.content' ).find( '.wpsc-edit-option-container' ).show();
}

/**
 * Update option
 */
function wpsc_set_edit_option(el, nonce) {

	var submitBtn  = jQuery( el );
	var inputField = jQuery( el ).parent().find( 'input' ).first();

	var option_id = submitBtn.closest( '.wpsc-option-item' ).find( '.option_id' ).val().trim();
	if ( ! option_id) {
		return;
	}

	var name = inputField.val().trim();
	if ( ! name) {
		return;
	}

	submitBtn.attr( 'disabled', true );
	inputField.attr( 'disabled', true );

	var submitHtml = submitBtn.html();
	submitBtn.html( supportcandy.inline_loader );

	var data = {
		action: 'wpsc_set_edit_option',
		id: option_id,
		name,
		_ajax_nonce: nonce
	};
	jQuery.post(
		supportcandy.ajax_url,
		data,
		function (response) {

			submitBtn.closest( '.wpsc-option-item' ).find( '.wpsc-option-listing-container .text' ).text( response.name );
			submitBtn.attr( 'disabled', false );
			inputField.attr( 'disabled', false );
			submitBtn.html( submitHtml );
			submitBtn.parent().hide();
			submitBtn.closest( '.content' ).find( '.wpsc-option-listing-container' ).show();
			wpsc_change_def_val_option_single();
		}
	);
}

/**
 * Set ticket widget ticket fields
 */
function wpsc_set_tw_ticket_fields(el) {

	var form     = jQuery( '.wpsc-frm-edit-ticket-fields' )[0];
	var dataform = new FormData( form );
	
	if (dataform.get( 'label' ).trim() == '') {
		alert( supportcandy.translations.req_fields_missing );
		return;
	}

	jQuery( '.wpsc-modal-footer button' ).attr( 'disabled', true );
	jQuery( el ).text( supportcandy.translations.please_wait );

	jQuery.ajax(
		{
			url: supportcandy.ajax_url,
			type: 'POST',
			data: dataform,
			processData: false,
			contentType: false
		}
	).done(
		function (res) {
			wpsc_close_modal();
			wpsc_get_ticket_widget();
		}
	);
}

/**
 * Get gdpr settings
 */
function wpsc_get_ms_gdpr() {

	supportcandy.current_tab = 'gdpr';
	jQuery( '.wpsc-setting-tab-container button' ).removeClass( 'active' );
	jQuery( '.wpsc-setting-tab-container button.' + supportcandy.current_tab ).addClass( 'active' );

	window.history.replaceState( {}, null, 'admin.php?page=wpsc-settings&section=' + supportcandy.current_section + '&tab=' + supportcandy.current_tab );
	jQuery( '.wpsc-setting-section-body' ).html( supportcandy.loader_html );

	wpsc_scroll_top();

	var data = { action: 'wpsc_get_ms_gdpr' };
	jQuery.post(
		supportcandy.ajax_url,
		data,
		function (response) {
			jQuery( '.wpsc-setting-section-body' ).html( response );
			wpsc_reset_responsive_style();
		}
	);
}

/**
 * Get advanced settings of tl more settings
 */
function wpsc_tl_ms_get_advanced() {

	supportcandy.current_tab = 'advanced';
	jQuery( '.wpsc-setting-tab-container button' ).removeClass( 'active' );
	jQuery( '.wpsc-setting-tab-container button.' + supportcandy.current_tab ).addClass( 'active' );

	window.history.replaceState( {}, null, 'admin.php?page=wpsc-ticket-list&section=' + supportcandy.current_section + '&tab=' + supportcandy.current_tab );
	jQuery( '.wpsc-setting-section-body' ).html( supportcandy.loader_html );

	wpsc_scroll_top();

	var data = { action: 'wpsc_tl_ms_get_advanced' };
	jQuery.post(
		supportcandy.ajax_url,
		data,
		function (response) {
			jQuery( '.wpsc-setting-section-body' ).html( response );
			wpsc_reset_responsive_style();
		}
	);
}

/**
 * Save gdpr
 *
 * @param {*} el
 */
function wpsc_set_ms_gdpr(el) {

	var form     = jQuery( '.wpsc-frm-ms-gdpr' )[0];
	var dataform = new FormData( form );
	jQuery( '.wpsc-modal-footer button' ).attr( 'disabled', true );
	jQuery( '.wpsc-modal-footer button' ).attr( 'disabled', true );
	var is_tinymce = (typeof tinyMCE != "undefined") && tinyMCE.activeEditor && ! tinyMCE.activeEditor.isHidden();

	if (is_tinymce) {
		var description = tinyMCE.activeEditor.getContent().trim();
	} else {
		var description = jQuery( '#gdpr-text' ).val();
	}
	dataform.append( 'gdpr-text', description );
	jQuery( el ).text( supportcandy.translations.please_wait );
	jQuery.ajax(
		{
			url: supportcandy.ajax_url,
			type: 'POST',
			data: dataform,
			processData: false,
			contentType: false
		}
	).done(
		function (res) {
			wpsc_get_ms_gdpr();
		}
	);
}

/**
 * Set advanced settings of tl more settings
 *
 * @param {*} el
 */
function wpsc_tl_ms_set_advanced(el) {

	var form     = jQuery( '.wpsc-frm-advanced-settings' )[0];
	var dataform = new FormData( form );
	jQuery( '.wpsc-modal-footer button' ).attr( 'disabled', true );
	jQuery( el ).text( supportcandy.translations.please_wait );
	jQuery.ajax(
		{
			url: supportcandy.ajax_url,
			type: 'POST',
			data: dataform,
			processData: false,
			contentType: false
		}
	).done(
		function (res) {
			wpsc_tl_ms_get_advanced();
		}
	);
}

/**
 * Get ticket widget agentOnly fields
 */
function wpsc_get_tw_agentonly_fields() {

	wpsc_show_modal();
	var data = { action: 'wpsc_get_tw_agentonly_fields' };
	jQuery.post(
		supportcandy.ajax_url,
		data,
		function (response) {

			// Set to modal.
			jQuery( '.wpsc-modal-header' ).text( response.title );
			jQuery( '.wpsc-modal-body' ).html( response.body );
			jQuery( '.wpsc-modal-footer' ).html( response.footer );
			// Display modal.
			wpsc_show_modal_inner_container();
		}
	);
}

/**
 * Reset gdpr
 *
 * @param {*} el
 */
function wpsc_reset_ms_gdpr(el, nonce) {

	jQuery( '.wpsc-modal-footer button' ).attr( 'disabled', true );
	jQuery( el ).text( supportcandy.translations.please_wait );
	var data = { action: 'wpsc_reset_ms_gdpr', _ajax_nonce: nonce };
	jQuery.post(
		supportcandy.ajax_url,
		data,
		function (response) {
			wpsc_get_ms_gdpr();
		}
	);
}

/**
 * Reset advanced settings of tl more settings
 *
 * @param {*} el
 */
function wpsc_tl_ms_reset_advanced(el, nonce) {

	jQuery( '.wpsc-modal-footer button' ).attr( 'disabled', true );
	jQuery( el ).text( supportcandy.translations.please_wait );
	var data = { action: 'wpsc_tl_ms_reset_advanced', _ajax_nonce: nonce };
	jQuery.post(
		supportcandy.ajax_url,
		data,
		function (response) {
			wpsc_tl_ms_get_advanced();
		}
	);
}

/**
 * Get advanced miscellaneous settings
 */
function wpsc_get_ms_advanced() {

	supportcandy.current_tab = 'advanced';
	jQuery( '.wpsc-setting-tab-container button' ).removeClass( 'active' );
	jQuery( '.wpsc-setting-tab-container button.' + supportcandy.current_tab ).addClass( 'active' );

	window.history.replaceState( {}, null, 'admin.php?page=wpsc-settings&section=' + supportcandy.current_section + '&tab=' + supportcandy.current_tab );
	jQuery( '.wpsc-setting-section-body' ).html( supportcandy.loader_html );

	wpsc_scroll_top();

	var data = { action: 'wpsc_get_ms_advanced' };
	jQuery.post(
		supportcandy.ajax_url,
		data,
		function (response) {
			jQuery( '.wpsc-setting-section-body' ).html( response );
			wpsc_reset_responsive_style();
		}
	);
}

/**
 * Set ticket widget agentOnly fields
 */
function wpsc_set_tw_agentonly_fields(el) {

	var form     = jQuery( '.wpsc-frm-edit-agentonly-fields' )[0];
	var dataform = new FormData( form );
	
	if (dataform.get( 'label' ).trim() == '') {
		alert( supportcandy.translations.req_fields_missing );
		return;
	}

	jQuery( '.wpsc-modal-footer button' ).attr( 'disabled', true );
	jQuery( el ).text( supportcandy.translations.please_wait );

	jQuery.ajax(
		{
			url: supportcandy.ajax_url,
			type: 'POST',
			data: dataform,
			processData: false,
			contentType: false
		}
	).done(
		function (res) {
			wpsc_close_modal();
			wpsc_get_ticket_widget();
		}
	);
}

/**
 * Get ticket widget additional recipients
 */
function wpsc_get_tw_additional_recipients() {

	wpsc_show_modal();
	var data = { action: 'wpsc_get_tw_additional_recipients' };
	jQuery.post(
		supportcandy.ajax_url,
		data,
		function (response) {

			// Set to modal.
			jQuery( '.wpsc-modal-header' ).text( response.title );
			jQuery( '.wpsc-modal-body' ).html( response.body );
			jQuery( '.wpsc-modal-footer' ).html( response.footer );
			// Display modal.
			wpsc_show_modal_inner_container();
		}
	);
}

/**
 * Save advanced miscellaneous settings
 *
 * @param {*} el
 */
function wpsc_set_ms_advanced(el) {

	var form     = jQuery( '.wpsc-frm-ms-advanced' )[0];
	var dataform = new FormData( form );
	jQuery( '.wpsc-modal-footer button' ).attr( 'disabled', true );
	jQuery( el ).text( supportcandy.translations.please_wait );
	jQuery.ajax(
		{
			url: supportcandy.ajax_url,
			type: 'POST',
			data: dataform,
			processData: false,
			contentType: false
		}
	).done(
		function (res) {
			wpsc_get_ms_advanced();
		}
	);
}

/**
 * Set ticket widget additional recipients
 */
function wpsc_set_tw_additional_recipients(el) {

	var form     = jQuery( '.wpsc-frm-edit-additional-recipients' )[0];
	var dataform = new FormData( form );
	
	if (dataform.get( 'label' ).trim() == '') {
		alert( supportcandy.translations.req_fields_missing );
		return;
	}

	jQuery( '.wpsc-modal-footer button' ).attr( 'disabled', true );
	jQuery( el ).text( supportcandy.translations.please_wait );

	jQuery.ajax(
		{
			url: supportcandy.ajax_url,
			type: 'POST',
			data: dataform,
			processData: false,
			contentType: false
		}
	).done(
		function (res) {
			wpsc_close_modal();
			wpsc_get_ticket_widget();
		}
	);
}

/**
 * Reset advanced miscellaneous settings
 *
 * @param {*} el
 */
function wpsc_reset_ms_advanced(el, nonce) {

	jQuery( '.wpsc-modal-footer button' ).attr( 'disabled', true );
	jQuery( el ).text( supportcandy.translations.please_wait );
	var data = { action: 'wpsc_reset_ms_advanced', _ajax_nonce: nonce };
	jQuery.post(
		supportcandy.ajax_url,
		data,
		function (response) {
			wpsc_get_ms_advanced();
		}
	);
}

/**
 * Cancel edit
 */
function wpsc_edit_option_cancel(el) {

	var cancelBtn = jQuery( el );
	cancelBtn.closest( '.wpsc-edit-option-container' ).hide();
	cancelBtn.closest( '.content' ).find( '.wpsc-option-listing-container' ).show();
}

/**
 * Remove an item
 */
function wpsc_remove_option_item(el) {

	jQuery( el ).closest( '.wpsc-option-item' ).remove();
	wpsc_change_def_val_option_single();
}

/**
 * Change default value options
 */
function wpsc_change_def_val_option_single() {

	var options = [];
	jQuery( '.wpsc-options-container .option_id' ).each(
		function (index, el) {

			var key   = jQuery( el ).val().trim();
			var value = jQuery( el ).closest( '.wpsc-option-item' ).find( '.wpsc-option-listing-container .text' ).text();
			options.push( { key, value } );
		}
	);
	var optionStr = '<option value=""></option>';
	jQuery( options ).each(
		function (index, el) {
			optionStr += '<option value="' + el.key + '">' + el.value + '</option>';
		}
	);

	var fieldType = jQuery( '#wpsc-select-ticket-field' ).val();

	var optionSingleTypes = ['cf_single_select', 'cf_radio_button'];
	if (jQuery.inArray( fieldType, optionSingleTypes ) != -1) {
		jQuery( '#wpsc-default-val-option-single' ).html( optionStr );
		jQuery( '#wpsc-default-val-option-single' ).selectWoo();
	}

	var optionMultiTypes = ['cf_multi_select', 'cf_checkbox'];
	if (jQuery.inArray( fieldType, optionMultiTypes ) != -1) {

		var preValue = jQuery( '#wpsc-default-val-option-multi' ).val();

		jQuery( '#wpsc-default-val-option-multi' ).html( optionStr );
		jQuery( '#wpsc-default-val-option-multi' ).selectWoo();

		if (preValue) {
			jQuery( '#wpsc-default-val-option-multi' ).val( preValue );
			jQuery( '#wpsc-default-val-option-multi' ).trigger( 'change' );
		}
	}
}

/**
 * Get rich text settings
 */
function wpsc_get_rich_text_editor(is_humbargar = false) {

	if (is_humbargar) {
		wpsc_toggle_humbargar();
	}

	jQuery( '.wpsc-setting-nav, .wpsc-humbargar-menu-item' ).removeClass( 'active' );
	jQuery( '.wpsc-setting-nav.rich-text-editor, .wpsc-humbargar-menu-item.rich-text-editor' ).addClass( 'active' );
	jQuery( '.wpsc-humbargar-title' ).html( supportcandy.humbargar_titles.rich_text_editor );

	if (supportcandy.current_section !== 'rich-text-editor') {
		supportcandy.current_section = 'rich-text-editor';
		supportcandy.current_tab     = 'agent';
	}

	window.history.replaceState( {}, null, 'admin.php?page=wpsc-settings&section=' + supportcandy.current_section + '&tab=' + supportcandy.current_tab );
	jQuery( '.wpsc-setting-body' ).html( supportcandy.loader_html );

	wpsc_scroll_top();

	var data = {
		action: 'wpsc_get_rich_text_editor',
		tab: supportcandy.current_tab
	};
	jQuery.post(
		supportcandy.ajax_url,
		data,
		function (response) {
			jQuery( '.wpsc-setting-body' ).html( response );
			wpsc_reset_responsive_style();
			jQuery( '.wpsc-setting-tab-container button.' + supportcandy.current_tab ).trigger( "click" );
		}
	);
}

/**
 * Load agent tab ui
 */
function wpsc_get_te_agent() {

	supportcandy.current_tab = 'agent';
	jQuery( '.wpsc-setting-tab-container button' ).removeClass( 'active' );
	jQuery( '.wpsc-setting-tab-container button.' + supportcandy.current_tab ).addClass( 'active' );

	window.history.replaceState( {}, null, 'admin.php?page=wpsc-settings&section=' + supportcandy.current_section + '&tab=' + supportcandy.current_tab );
	jQuery( '.wpsc-setting-section-body' ).html( supportcandy.loader_html );

	wpsc_scroll_top();

	const data = { action: 'wpsc_get_te_agent' };
	jQuery.post(
		supportcandy.ajax_url,
		data,
		function (response) {
			jQuery( '.wpsc-setting-section-body' ).html( response );
			wpsc_reset_responsive_style();
		}
	);
}

/**
 * Save agent settings
 */
function wpsc_set_te_agent(el) {

	const form     = jQuery( '.wpsc-frm-te-agent' )[0];
	const dataform = new FormData( form );
	jQuery( '.wpsc-modal-footer button' ).attr( 'disabled', true );
	jQuery( el ).text( supportcandy.translations.please_wait );
	jQuery.ajax(
		{
			url: supportcandy.ajax_url,
			type: 'POST',
			data: dataform,
			processData: false,
			contentType: false
		}
	).done(
		function (res) {
			wpsc_get_te_agent();
		}
	);
}

/**
 * Reset agent settings
 */
function wpsc_reset_te_agent(el, nonce) {

	jQuery( '.wpsc-modal-footer button' ).attr( 'disabled', true );
	jQuery( el ).text( supportcandy.translations.please_wait );
	const data = { action: 'wpsc_reset_te_agent', _ajax_nonce: nonce };
	jQuery.post(
		supportcandy.ajax_url,
		data,
		function (response) {
			wpsc_get_te_agent();
		}
	);
}

/**
 * Load registered user tab ui
 */
function wpsc_get_te_registered_user() {

	supportcandy.current_tab = 'registered-user';
	jQuery( '.wpsc-setting-tab-container button' ).removeClass( 'active' );
	jQuery( '.wpsc-setting-tab-container button.' + supportcandy.current_tab ).addClass( 'active' );

	window.history.replaceState( {}, null, 'admin.php?page=wpsc-settings&section=' + supportcandy.current_section + '&tab=' + supportcandy.current_tab );
	jQuery( '.wpsc-setting-section-body' ).html( supportcandy.loader_html );

	wpsc_scroll_top();

	const data = { action: 'wpsc_get_te_registered_user' };
	jQuery.post(
		supportcandy.ajax_url,
		data,
		function (response) {
			jQuery( '.wpsc-setting-section-body' ).html( response );
			wpsc_reset_responsive_style();
		}
	);
}

/**
 * Save registered user settings
 */
function wpsc_set_te_registered_user(el) {

	const form     = jQuery( '.wpsc-frm-te-registered-user' )[0];
	const dataform = new FormData( form );
	jQuery( '.wpsc-modal-footer button' ).attr( 'disabled', true );
	jQuery( el ).text( supportcandy.translations.please_wait );
	jQuery.ajax(
		{
			url: supportcandy.ajax_url,
			type: 'POST',
			data: dataform,
			processData: false,
			contentType: false
		}
	).done(
		function (res) {
			wpsc_get_te_registered_user();
		}
	);
}

/**
 * Reset registered user settings
 */
function wpsc_reset_te_registered_user(el, nonce) {

	jQuery( '.wpsc-modal-footer button' ).attr( 'disabled', true );
	jQuery( el ).text( supportcandy.translations.please_wait );
	const data = { action: 'wpsc_reset_te_registered_user', _ajax_nonce: nonce };
	jQuery.post(
		supportcandy.ajax_url,
		data,
		function (response) {
			wpsc_get_te_registered_user();
		}
	);
}

/**
 * Load guest user tab ui
 */
function wpsc_get_te_guest_user() {

	supportcandy.current_tab = 'guest-user';
	jQuery( '.wpsc-setting-tab-container button' ).removeClass( 'active' );
	jQuery( '.wpsc-setting-tab-container button.' + supportcandy.current_tab ).addClass( 'active' );

	window.history.replaceState( {}, null, 'admin.php?page=wpsc-settings&section=' + supportcandy.current_section + '&tab=' + supportcandy.current_tab );
	jQuery( '.wpsc-setting-section-body' ).html( supportcandy.loader_html );

	wpsc_scroll_top();

	const data = { action: 'wpsc_get_te_guest_user' };
	jQuery.post(
		supportcandy.ajax_url,
		data,
		function (response) {
			jQuery( '.wpsc-setting-section-body' ).html( response );
			wpsc_reset_responsive_style();
		}
	);
}

/**
 * Save guest user settings
 */
function wpsc_set_te_guest_user(el) {

	const form     = jQuery( '.wpsc-frm-te-guest-user' )[0];
	const dataform = new FormData( form );
	jQuery( '.wpsc-modal-footer button' ).attr( 'disabled', true );
	jQuery( el ).text( supportcandy.translations.please_wait );
	jQuery.ajax(
		{
			url: supportcandy.ajax_url,
			type: 'POST',
			data: dataform,
			processData: false,
			contentType: false
		}
	).done(
		function (res) {
			wpsc_get_te_guest_user();
		}
	);
}

/**
 * Reset guest user settings
 */
function wpsc_reset_te_guest_user(el, nonce) {

	jQuery( '.wpsc-modal-footer button' ).attr( 'disabled', true );
	jQuery( el ).text( supportcandy.translations.please_wait );
	const data = { action: 'wpsc_reset_te_guest_user', _ajax_nonce: nonce };
	jQuery.post(
		supportcandy.ajax_url,
		data,
		function (response) {
			wpsc_get_te_guest_user();
		}
	);
}

/**
 * Load advanced tab ui
 */
function wpsc_get_te_advanced() {

	supportcandy.current_tab = 'advanced';
	jQuery( '.wpsc-setting-tab-container button' ).removeClass( 'active' );
	jQuery( '.wpsc-setting-tab-container button.' + supportcandy.current_tab ).addClass( 'active' );

	window.history.replaceState( {}, null, 'admin.php?page=wpsc-settings&section=' + supportcandy.current_section + '&tab=' + supportcandy.current_tab );
	jQuery( '.wpsc-setting-section-body' ).html( supportcandy.loader_html );

	wpsc_scroll_top();

	const data = { action: 'wpsc_get_te_advanced' };
	jQuery.post(
		supportcandy.ajax_url,
		data,
		function (response) {
			jQuery( '.wpsc-setting-section-body' ).html( response );
			wpsc_reset_responsive_style();
		}
	);
}

/**
 * Save advanced settings
 */
function wpsc_set_te_advanced(el) {

	const form     = jQuery( '.wpsc-frm-te-advanced' )[0];
	const dataform = new FormData( form );
	jQuery( '.wpsc-modal-footer button' ).attr( 'disabled', true );
	jQuery( el ).text( supportcandy.translations.please_wait );
	jQuery.ajax(
		{
			url: supportcandy.ajax_url,
			type: 'POST',
			data: dataform,
			processData: false,
			contentType: false
		}
	).done(
		function (res) {
			wpsc_get_te_advanced();
		}
	);
}

/**
 * Reset advanced settings
 */
function wpsc_reset_te_advanced(el, nonce) {

	jQuery( '.wpsc-modal-footer button' ).attr( 'disabled', true );
	jQuery( el ).text( supportcandy.translations.please_wait );
	const data = { action: 'wpsc_reset_te_advanced', _ajax_nonce: nonce };
	jQuery.post(
		supportcandy.ajax_url,
		data,
		function (response) {
			wpsc_get_te_advanced();
		}
	);
}

/**
 * Get agent working hrs settings
 */
function wpsc_get_agent_working_hrs(is_humbargar = false) {

	if (is_humbargar) {
		wpsc_toggle_humbargar();
	}

	jQuery( '.wpsc-setting-nav, .wpsc-humbargar-menu-item' ).removeClass( 'active' );
	jQuery( '.wpsc-setting-nav.working-hrs, .wpsc-humbargar-menu-item.working-hrs' ).addClass( 'active' );
	jQuery( '.wpsc-humbargar-title' ).html( supportcandy.humbargar_titles.working_hrs );

	window.history.replaceState( {}, null, 'admin.php?page=wpsc-support-agents&section=working-hrs' );
	jQuery( '.wpsc-setting-body' ).html( supportcandy.loader_html );

	wpsc_scroll_top();

	var data = { action: 'wpsc_get_agent_working_hrs' };
	jQuery.post(
		supportcandy.ajax_url,
		data,
		function (response) {
			jQuery( '.wpsc-setting-body' ).html( response );
			wpsc_reset_responsive_style();
			wpsc_get_agent_wh_hrs();
		}
	);
}

/**
 * Get all agent leaves
 */
function wpsc_get_agent_leaves(is_humbargar = false) {

	if (is_humbargar) {
		wpsc_toggle_humbargar();
	}

	jQuery( '.wpsc-setting-nav, .wpsc-humbargar-menu-item' ).removeClass( 'active' );
	jQuery( '.wpsc-setting-nav.leaves, .wpsc-humbargar-menu-item.leaves' ).addClass( 'active' );
	jQuery( '.wpsc-humbargar-title' ).html( supportcandy.humbargar_titles.leaves );

	window.history.replaceState( {}, null, 'admin.php?page=wpsc-support-agents&section=leaves' );
	jQuery( '.wpsc-setting-body' ).html( supportcandy.loader_html );

	wpsc_scroll_top();

	var data = { action: 'wpsc_get_agent_leaves' };
	jQuery.post(
		supportcandy.ajax_url,
		data,
		function (response) {
			jQuery( '.wpsc-setting-body' ).html( response );
			wpsc_reset_responsive_style();
		}
	);
}

/**
 * Get add agent leaves
 */
function wpsc_get_add_agent_leaves(dateSelected, _ajax_nonce) {

	supportcandy.temp.dateSelected = dateSelected;
	wpsc_show_modal();
	var data = {
		action: 'wpsc_get_add_agent_leaves',
		dateSelected,
		_ajax_nonce
	};
	jQuery.post(
		supportcandy.ajax_url,
		data,
		function (response) {
			// Set to modal.
			jQuery( '.wpsc-modal-header' ).text( response.title );
			jQuery( '.wpsc-modal-body' ).html( response.body );
			jQuery( '.wpsc-modal-footer' ).html( response.footer );
			// Display modal.
			wpsc_show_modal_inner_container();
		}
	);
}

/**
 * Set add agent leaves
 */
function wpsc_set_add_agent_leaves(el) {

	const form     = jQuery( '.wpsc-frm-agent-holiday-actions' )[0];
	const dataform = new FormData( form );

	var agents = dataform.getAll( 'agents[]' );
	if ( ! (agents.length)) {
		alert( supportcandy.translations.req_fields_missing );
		return;
	}

	dataform.append( 'dateSelected', supportcandy.temp.dateSelected );
	jQuery( el ).text( supportcandy.translations.please_wait );
	jQuery.ajax(
		{
			url: supportcandy.ajax_url,
			type: 'POST',
			data: dataform,
			processData: false,
			contentType: false
		}
	).done(
		function (response) {
			calendar.refetchEvents();
			wpsc_close_modal();
		}
	);
}

/**
 * Delete agent leave
 */
function wpsc_delete_agent_leave(id, nonce) {

	var flag = confirm( supportcandy.translations.deleteLeaveConfirmation );
	if ( ! flag) {
		return;
	}

	var data = { action: 'wpsc_delete_agent_leave', holidayId: id, _ajax_nonce: nonce };
	jQuery.post(
		supportcandy.ajax_url,
		data,
		function (response) {
			calendar.refetchEvents();
		}
	);
}

/**
 * User registration otp email template
 */
function wpsc_get_en_user_reg_otp(is_humbargar = false) {

	if (is_humbargar) {
		wpsc_toggle_humbargar();
	}

	jQuery( '.wpsc-setting-nav, .wpsc-humbargar-menu-item' ).removeClass( 'active' );
	jQuery( '.wpsc-setting-nav.registration-otp, .wpsc-humbargar-menu-item.registration-otp' ).addClass( 'active' );
	jQuery( '.wpsc-humbargar-title' ).html( supportcandy.humbargar_titles.registration_otp );

	window.history.replaceState( {}, null, 'admin.php?page=wpsc-email-notifications&section=registration-otp' );
	jQuery( '.wpsc-setting-body' ).html( supportcandy.loader_html );

	wpsc_scroll_top();

	var data = { action: 'wpsc_get_en_user_reg_otp' };
	jQuery.post(
		supportcandy.ajax_url,
		data,
		function (response) {
			jQuery( '.wpsc-setting-body' ).html( response );
			wpsc_reset_responsive_style();
		}
	);
}

/**
 * Set user registration otp email template
 */
function wpsc_set_en_user_reg_otp(el) {

	jQuery( '.wpsc-modal-footer button' ).attr( 'disabled', true );
	jQuery( el ).text( supportcandy.translations.please_wait );
	var form     = jQuery( '.wpsc-frm-en-user-reg-otp' )[0];
	var dataform = new FormData( form );
	if (dataform.get( 'editor' ) == 'html') {
		dataform.append( 'body', tinyMCE.get( 'wpsc-en-body' ).getContent() );
	}
	jQuery.ajax(
		{
			url: supportcandy.ajax_url,
			type: 'POST',
			data: dataform,
			processData: false,
			contentType: false
		}
	).done(
		function (res) {
			wpsc_get_en_user_reg_otp();
		}
	);
}

/**
 * Reset user registration otp email template
 */
function wpsc_reset_en_user_reg_otp(el, nonce) {

	jQuery( '.wpsc-modal-footer button' ).attr( 'disabled', true );
	jQuery( el ).text( supportcandy.translations.please_wait );
	const data = { action: 'wpsc_reset_en_user_reg_otp', _ajax_nonce: nonce };
	jQuery.post(
		supportcandy.ajax_url,
		data,
		function (response) {
			wpsc_get_en_user_reg_otp();
		}
	);
}

/**
 * Get guest login otp email template
 *
 * @param {boolean} is_humbargar
 */
function wpsc_get_en_guest_login_otp(is_humbargar = false) {

	if (is_humbargar) {
		wpsc_toggle_humbargar();
	}

	jQuery( '.wpsc-setting-nav, .wpsc-humbargar-menu-item' ).removeClass( 'active' );
	jQuery( '.wpsc-setting-nav.guest-login-otp, .wpsc-humbargar-menu-item.guest-login-otp' ).addClass( 'active' );
	jQuery( '.wpsc-humbargar-title' ).html( supportcandy.humbargar_titles.registration_otp );

	window.history.replaceState( {}, null, 'admin.php?page=wpsc-email-notifications&section=guest-login-otp' );
	jQuery( '.wpsc-setting-body' ).html( supportcandy.loader_html );

	wpsc_scroll_top();

	var data = { action: 'wpsc_get_en_guest_login_otp' };
	jQuery.post(
		supportcandy.ajax_url,
		data,
		function (response) {
			jQuery( '.wpsc-setting-body' ).html( response );
			wpsc_reset_responsive_style();
		}
	);
}

/**
 * Set guest login otp email template
 */
function wpsc_set_en_guest_login_otp(el) {

	jQuery( '.wpsc-modal-footer button' ).attr( 'disabled', true );
	jQuery( el ).text( supportcandy.translations.please_wait );
	var form     = jQuery( '.wpsc-frm-en-guest-login-otp' )[0];
	var dataform = new FormData( form );
	if (dataform.get( 'editor' ) == 'html') {
		dataform.append( 'body', tinyMCE.get( 'wpsc-en-body' ).getContent() );
	}
	jQuery.ajax(
		{
			url: supportcandy.ajax_url,
			type: 'POST',
			data: dataform,
			processData: false,
			contentType: false
		}
	).done(
		function (res) {
			wpsc_get_en_guest_login_otp();
		}
	);
}

/**
 * Reset guest login otp email template
 */
function wpsc_reset_en_guest_login_otp(el, nonce) {

	jQuery( '.wpsc-modal-footer button' ).attr( 'disabled', true );
	jQuery( el ).text( supportcandy.translations.please_wait );
	const data = { action: 'wpsc_reset_en_guest_login_otp', _ajax_nonce: nonce };
	jQuery.post(
		supportcandy.ajax_url,
		data,
		function (response) {
			wpsc_get_en_guest_login_otp();
		}
	);
}

/**
 * Get ticket widget biographical info
 */
function wpsc_get_tw_biographical_info() {

	wpsc_show_modal();
	var data = { action: 'wpsc_get_tw_biographical_info' };
	jQuery.post(
		supportcandy.ajax_url,
		data,
		function (response) {

			// Set to modal.
			jQuery( '.wpsc-modal-header' ).text( response.title );
			jQuery( '.wpsc-modal-body' ).html( response.body );
			jQuery( '.wpsc-modal-footer' ).html( response.footer );
			// Display modal.
			wpsc_show_modal_inner_container();
		}
	);
}

/**
 * Set ticket widget biographical info
 */
function wpsc_set_tw_biographical_info(el) {

	var form     = jQuery( '.wpsc-frm-edit-biographical-info' )[0];
	var dataform = new FormData( form );
	
	if (dataform.get( 'label' ).trim() == '') {
		alert( supportcandy.translations.req_fields_missing );
		return;
	}

	jQuery( '.wpsc-modal-footer button' ).attr( 'disabled', true );
	jQuery( el ).text( supportcandy.translations.please_wait );
	
	jQuery.ajax(
		{
			url: supportcandy.ajax_url,
			type: 'POST',
			data: dataform,
			processData: false,
			contentType: false
		}
	).done(
		function (res) {
			wpsc_close_modal();
			wpsc_get_ticket_widget();
		}
	);
}

/**
 * Get edit agent ticket list items
 */
function wpsc_get_edit_agent_tl_item(slug, _ajax_nonce) {

	wpsc_show_modal();
	var data = { action: 'wpsc_get_edit_agent_tl_item', slug, _ajax_nonce };
	jQuery.post(
		supportcandy.ajax_url,
		data,
		function (response) {

			// Set to modal.
			jQuery( '.wpsc-modal-header' ).text( response.title );
			jQuery( '.wpsc-modal-body' ).html( response.body );
			jQuery( '.wpsc-modal-footer' ).html( response.footer );
			// Display modal.
			wpsc_show_modal_inner_container();
		}
	);
}

/**
 * Set edit agent ticket list items
 */
function wpsc_set_edit_agent_tl_item(el) {

	jQuery( '.wpsc-modal-footer button' ).attr( 'disabled', true );
	jQuery( el ).text( supportcandy.translations.please_wait );

	var form     = jQuery( '.frm-edit-agent-tl-items' )[0];
	var dataform = new FormData( form );
	jQuery.ajax(
		{
			url: supportcandy.ajax_url,
			type: 'POST',
			data: dataform,
			processData: false,
			contentType: false
		}
	).done(
		function (res) {
			wpsc_close_modal();
			wpsc_get_agent_tl_items();
		}
	);
}

/**
 * Get edit agent filter list items
 */
function wpsc_get_edit_agent_filter_item(slug, _ajax_nonce) {

	wpsc_show_modal();
	var data = { action: 'wpsc_get_edit_agent_filter_item', slug, _ajax_nonce };
	jQuery.post(
		supportcandy.ajax_url,
		data,
		function (response) {

			// Set to modal.
			jQuery( '.wpsc-modal-header' ).text( response.title );
			jQuery( '.wpsc-modal-body' ).html( response.body );
			jQuery( '.wpsc-modal-footer' ).html( response.footer );
			// Display modal.
			wpsc_show_modal_inner_container();
		}
	);
}

/**
 * Set edit agent filter list items
 */
function wpsc_set_edit_agent_filter_item(el) {

	jQuery( '.wpsc-modal-footer button' ).attr( 'disabled', true );
	jQuery( el ).text( supportcandy.translations.please_wait );

	var form     = jQuery( '.frm-edit-agent-fl-items' )[0];
	var dataform = new FormData( form );
	jQuery.ajax(
		{
			url: supportcandy.ajax_url,
			type: 'POST',
			data: dataform,
			processData: false,
			contentType: false
		}
	).done(
		function (res) {
			wpsc_close_modal();
			wpsc_get_agent_filter_items();
		}
	);
}

/**
 * Get edit company working hrs exception
 */
function wpsc_get_edit_wh_exception(exception_id, _ajax_nonce) {

	wpsc_show_modal();
	var data = { action: 'wpsc_get_edit_wh_exception', exception_id, _ajax_nonce };
	jQuery.post(
		supportcandy.ajax_url,
		data,
		function (response) {

			// Set to modal.
			jQuery( '.wpsc-modal-header' ).text( response.title );
			jQuery( '.wpsc-modal-body' ).html( response.body );
			jQuery( '.wpsc-modal-footer' ).html( response.footer );
			// Display modal.
			wpsc_show_modal_inner_container();
		}
	);
}

/**
 * Delete company working hrs exception
 */
function wpsc_delete_wh_exception(exception_id, _ajax_nonce) {

	var flag = confirm( supportcandy.translations.confirm );
	if ( ! flag) {
		return;
	}
	var data = { action: 'wpsc_delete_wh_exception', exception_id, _ajax_nonce };
	jQuery.post(
		supportcandy.ajax_url,
		data,
		function (res) {
			wpsc_get_wh_exceptions();
		}
	);
}

/**
 * Get edit customer ticket list items
 */
function wpsc_get_edit_customer_tl_item(slug, _ajax_nonce) {

	wpsc_show_modal();
	var data = { action: 'wpsc_get_edit_customer_tl_item', slug, _ajax_nonce };
	jQuery.post(
		supportcandy.ajax_url,
		data,
		function (response) {

			// Set to modal.
			jQuery( '.wpsc-modal-header' ).text( response.title );
			jQuery( '.wpsc-modal-body' ).html( response.body );
			jQuery( '.wpsc-modal-footer' ).html( response.footer );
			// Display modal.
			wpsc_show_modal_inner_container();
		}
	);
}

/**
 * Set edit customer ticket list items
 */
function wpsc_set_edit_customer_tl_item(el) {

	jQuery( '.wpsc-modal-footer button' ).attr( 'disabled', true );
	jQuery( el ).text( supportcandy.translations.please_wait );

	var form     = jQuery( '.frm-edit-customer-tl-items' )[0];
	var dataform = new FormData( form );
	jQuery.ajax(
		{
			url: supportcandy.ajax_url,
			type: 'POST',
			data: dataform,
			processData: false,
			contentType: false
		}
	).done(
		function (res) {
			wpsc_close_modal();
			wpsc_get_customer_tl_items();
		}
	);
}

/**
 * Get edit coustomer ticket filter items
 */
function wpsc_get_edit_ctl_filter_item(slug, _ajax_nonce) {

	wpsc_show_modal();
	var data = { action: 'wpsc_get_edit_ctl_filter_item', slug, _ajax_nonce };
	jQuery.post(
		supportcandy.ajax_url,
		data,
		function (response) {

			// Set to modal.
			jQuery( '.wpsc-modal-header' ).text( response.title );
			jQuery( '.wpsc-modal-body' ).html( response.body );
			jQuery( '.wpsc-modal-footer' ).html( response.footer );
			// Display modal.
			wpsc_show_modal_inner_container();
		}
	);
}

/**
 * Set edit coustomer ticket filter items
 */
function wpsc_set_edit_ctl_filter_item(el) {

	jQuery( '.wpsc-modal-footer button' ).attr( 'disabled', true );
	jQuery( el ).text( supportcandy.translations.please_wait );

	var form     = jQuery( '.frm-edit-customer-fl-items' )[0];
	var dataform = new FormData( form );
	jQuery.ajax(
		{
			url: supportcandy.ajax_url,
			type: 'POST',
			data: dataform,
			processData: false,
			contentType: false
		}
	).done(
		function (res) {
			wpsc_close_modal();
			wpsc_get_customer_filter_items();
		}
	);
}

/**
 * Get ticket tag settings
 */
function wpsc_get_ticket_tags() {

	supportcandy.current_tab = 'ticket-tags-list';
	jQuery( '.wpsc-setting-tab-container button' ).removeClass( 'active' );
	jQuery( '.wpsc-setting-tab-container button.' + supportcandy.current_tab ).addClass( 'active' );

	window.history.replaceState( {}, null, 'admin.php?page=wpsc-settings&section=' + supportcandy.current_section + '&tab=' + supportcandy.current_tab );
	jQuery( '.wpsc-setting-section-body' ).html( supportcandy.loader_html );

	wpsc_scroll_top();

	var data = { action: 'wpsc_get_ticket_tags' };
	jQuery.post(
		supportcandy.ajax_url,
		data,
		function (response) {
			jQuery( '.wpsc-setting-section-body' ).html( response );
			wpsc_reset_responsive_style();
		}
	);
}

/**
 *  Set add ticket tag
 */
function wpsc_set_add_ticket_tags(el) {

	var form     = jQuery( '.wpsc-frm-add-ms-ticket-tags' )[0];
	var dataform = new FormData( form );

	if ( ! dataform.get( 'name' ) ) {
		alert( supportcandy.translations.req_fields_missing );
		return;
	}

	jQuery( '.wpsc-modal-footer button' ).attr( 'disabled', true );
	jQuery( el ).text( supportcandy.translations.please_wait );

	jQuery.ajax(
		{
			url: supportcandy.ajax_url,
			type: 'POST',
			data: dataform,
			processData: false,
			contentType: false
		}
	).done(
		function (res) {
			wpsc_close_modal();
			wpsc_get_ticket_tags();
		}
	);
}

/**
 * Update tag
 */
function wpsc_get_edit_ticket_tags(id, nonce) {

	wpsc_show_modal();
	var data = { action: 'wpsc_get_edit_ticket_tags', id, _ajax_nonce: nonce };
	jQuery.post(
		supportcandy.ajax_url,
		data,
		function (res) {

			// Set to modal.
			jQuery( '.wpsc-modal-header' ).text( res.title );
			jQuery( '.wpsc-modal-body' ).html( res.body );
			jQuery( '.wpsc-modal-footer' ).html( res.footer );
			// Display modal.
			wpsc_show_modal_inner_container();
		}
	);
}

function wpsc_set_edit_ticket_tags(el) {

	var form     = jQuery( '.wpsc-frm-edit-ticket-tag' )[0];
	var dataform = new FormData( form );

	if ( ! dataform.get( 'name' ) ) {
		alert( supportcandy.translations.req_fields_missing );
		return;
	}
	jQuery( '.wpsc-modal-footer button' ).attr( 'disabled', true );
	jQuery( el ).text( supportcandy.translations.please_wait );

	jQuery.ajax(
		{
			url: supportcandy.ajax_url,
			type: 'POST',
			data: dataform,
			processData: false,
			contentType: false
		}
	).done(
		function (res) {
			wpsc_close_modal();
			wpsc_get_ticket_tags();
		}
	);

}

/**
 * Delete ticket tag modal
 */
function wpsc_set_delete_ticket_tags(id, nonce) {

	var flag = confirm( supportcandy.translations.confirm );
	if ( ! flag) {
		return;
	}

	var data = { action: 'wpsc_set_delete_ticket_tags', id, _ajax_nonce: nonce };
	jQuery.post(
		supportcandy.ajax_url,
		data,
		function (response) {
			wpsc_get_ticket_tags();
		}
	);
}

/**
 * Get ticket widget ticket tags
 */
function wpsc_get_tw_ticket_tags() {

	wpsc_show_modal();
	var data = { action: 'wpsc_get_tw_ticket_tags' };
	jQuery.post(
		supportcandy.ajax_url,
		data,
		function (response) {

			// Set to modal.
			jQuery( '.wpsc-modal-header' ).text( response.title );
			jQuery( '.wpsc-modal-body' ).html( response.body );
			jQuery( '.wpsc-modal-footer' ).html( response.footer );
			// Display modal.
			wpsc_show_modal_inner_container();
		}
	);
}

/**
 * Set ticket widget ticket tags
 */
function wpsc_set_tw_ticket_tags(el) {

	var form     = jQuery( '.wpsc-frm-edit-tags' )[0];
	var dataform = new FormData( form );

	if (dataform.get( 'label' ).trim() == '') {
		alert( supportcandy.translations.req_fields_missing );
		return;
	}

	jQuery( '.wpsc-modal-footer button' ).attr( 'disabled', true );
	jQuery( el ).text( supportcandy.translations.please_wait );

	jQuery.ajax(
		{
			url: supportcandy.ajax_url,
			type: 'POST',
			data: dataform,
			processData: false,
			contentType: false
		}
	).done(
		function (res) {
			wpsc_close_modal();
			wpsc_get_ticket_widget();
		}
	);
}

/**
 * Get ticket tags modal UI
 */
function wpsc_it_get_edit_ticket_tags(ticket_id, nonce) {

	wpsc_show_modal();
	var data = {
		action: 'wpsc_it_get_edit_ticket_tags',
		ticket_id,
		_ajax_nonce: nonce
	};
	jQuery.post(
		supportcandy.ajax_url,
		data,
		function (response) {

			jQuery( '.wpsc-modal-header' ).text( response.title );
			jQuery( '.wpsc-modal-body' ).html( response.body );
			jQuery( '.wpsc-modal-footer' ).html( response.footer );
			wpsc_show_modal_inner_container();
		}
	);
}


/**
 * Get appearence settings
 */
function wpsc_get_ticket_tags_settings(is_humbargar = false) {

	if (is_humbargar) {
		wpsc_toggle_humbargar();
	}

	jQuery( '.wpsc-setting-nav, .wpsc-humbargar-menu-item' ).removeClass( 'active' );
	jQuery( '.wpsc-setting-nav.ticket-tags, .wpsc-humbargar-menu-item.ticket-tags' ).addClass( 'active' );
	jQuery( '.wpsc-humbargar-title' ).html( supportcandy.humbargar_titles.tickettags );

	if (supportcandy.current_section !== 'ticket-tags') {
		supportcandy.current_section = 'ticket-tags';
		supportcandy.current_tab     = 'general';
	}

	window.history.replaceState( {}, null, 'admin.php?page=wpsc-settings&section=' + supportcandy.current_section + '&tab=' + supportcandy.current_tab );
	jQuery( '.wpsc-setting-body' ).html( supportcandy.loader_html );

	wpsc_scroll_top();

	var data = {
		action: 'wpsc_get_ticket_tags_settings',
		tab: supportcandy.current_tab
	};
	jQuery.post(
		supportcandy.ajax_url,
		data,
		function (response) {
			jQuery( '.wpsc-setting-body' ).html( response );
			wpsc_reset_responsive_style();
			jQuery( '.wpsc-setting-tab-container button.' + supportcandy.current_tab ).trigger( "click" );
		}
	);
}

/**
 * Get general appearence settings
 */
function wpsc_ticket_tags_get_general_settings() {

	supportcandy.current_tab = 'general';
	jQuery( '.wpsc-setting-tab-container button' ).removeClass( 'active' );
	jQuery( '.wpsc-setting-tab-container button.' + supportcandy.current_tab ).addClass( 'active' );

	window.history.replaceState( {}, null, 'admin.php?page=wpsc-settings&section=' + supportcandy.current_section + '&tab=' + supportcandy.current_tab );
	jQuery( '.wpsc-setting-section-body' ).html( supportcandy.loader_html );

	wpsc_scroll_top();
	var data = { action: 'wpsc_ticket_tags_get_general_settings' };
	jQuery.post(
		supportcandy.ajax_url,
		data,
		function (response) {
			jQuery( '.wpsc-setting-section-body' ).html( response );
			wpsc_reset_responsive_style();
		}
	);
}

/**
 * Set general settings
 */
function wpsc_ticket_tags_set_general_settings(el) {

	var form     = jQuery( '.wpsc-ticket-tags-general-settings' )[0];
	var dataform = new FormData( form );
	jQuery( el ).text( supportcandy.translations.please_wait );
	jQuery.ajax(
		{
			url: supportcandy.ajax_url,
			type: 'POST',
			data: dataform,
			processData: false,
			contentType: false
		}
	).done(
		function (res) {
			wpsc_ticket_tags_get_general_settings();
		}
	);
}

/**
 * Reset general settings
 */
function wpsc_ticket_tags_reset_general_settings(el, nonce) {

	jQuery( el ).text( supportcandy.translations.please_wait );
	var data = { action: 'wpsc_ticket_tags_reset_general_settings', _ajax_nonce: nonce };
	jQuery.post(
		supportcandy.ajax_url,
		data,
		function (response) {
			wpsc_ticket_tags_get_general_settings();
		}
	);
}
