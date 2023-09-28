<?php
/**
 * The template's part to display the property's address
 *
 * @package ThemeREX Addons
 * @since v1.6.22
 */

$trx_addons_args = get_query_var('trx_addons_args_properties_address');
$trx_addons_meta = $trx_addons_args['meta'];

$title = __('Show all properties from %s', 'trx_addons');

?><span class="properties_address"><?php
	// Address
	if (!empty($trx_addons_meta['address'])) {
		?><span class="properties_address_item"><?php trx_addons_show_layout($trx_addons_meta['address']); ?></span><?php
	}
	// Neighborhood
	if (!empty($trx_addons_meta['neighborhood'])) {
		?><span class="properties_address_item"><?php
			trx_addons_get_term_link( (int)$trx_addons_meta['neighborhood'],
										TRX_ADDONS_CPT_PROPERTIES_TAXONOMY_NEIGHBORHOOD,
										array('echo'=>true, 'title'=>$title)
									);
		?></span><?php
	}
	// City
	if (!empty($trx_addons_meta['city'])) {
		?><span class="properties_address_item"><?php
			trx_addons_get_term_link( (int)$trx_addons_meta['city'],
										TRX_ADDONS_CPT_PROPERTIES_TAXONOMY_CITY,
										array('echo'=>true, 'title'=>$title)
									);
		?></span><?php
	}
	// ZIP
	if (!empty($trx_addons_meta['zip'])) {
		?><span class="properties_address_item"><?php trx_addons_show_layout($trx_addons_meta['zip']); ?></span><?php
	}
	// County / State
	if (!empty($trx_addons_meta['state'])) {
		?><span class="properties_address_item"><?php
			trx_addons_get_term_link( (int)$trx_addons_meta['state'], 
										TRX_ADDONS_CPT_PROPERTIES_TAXONOMY_STATE, 
										array('echo'=>true, 'title'=>$title)
									);
		?></span><?php
	}
	// Country
	if (!empty($trx_addons_meta['country'])) {
		?><span class="properties_address_item"><?php
			trx_addons_get_term_link( (int)$trx_addons_meta['country'], 
										TRX_ADDONS_CPT_PROPERTIES_TAXONOMY_COUNTRY, 
										array('echo'=>true, 'title'=>$title)
									); 
		?></span><?php
	}
?></span>