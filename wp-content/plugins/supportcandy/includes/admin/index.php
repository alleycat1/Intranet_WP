<?php
// Load tickets.
foreach ( glob( dirname( __FILE__ ) . '/tickets/*.php' ) as $filename ) {
	include_once $filename;
}

// Load agent settings.
foreach ( glob( dirname( __FILE__ ) . '/agent-settings/*.php' ) as $filename ) {
	include_once $filename;
}

// Load ticket form settings.
foreach ( glob( dirname( __FILE__ ) . '/custom-fields/*.php' ) as $filename ) {
	include_once $filename;
}

// Load ticket list settings.
foreach ( glob( dirname( __FILE__ ) . '/ticket-list/*.php' ) as $filename ) {
	include_once $filename;
}

// Load email notification settings.
foreach ( glob( dirname( __FILE__ ) . '/email-notifications/*.php' ) as $filename ) {
	include_once $filename;
}

// Load settings.
foreach ( glob( dirname( __FILE__ ) . '/settings/*.php' ) as $filename ) {
	include_once $filename;
}

// Miscellaneous classes.
foreach ( glob( dirname( __FILE__ ) . '/misc/*.php' ) as $filename ) {
	include_once $filename;
}

// Customer classes.
foreach ( glob( dirname( __FILE__ ) . '/customers/*.php' ) as $filename ) {
	include_once $filename;
}
