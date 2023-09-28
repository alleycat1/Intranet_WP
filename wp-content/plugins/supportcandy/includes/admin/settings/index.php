<?php
// Load general settings.
foreach ( glob( dirname( __FILE__ ) . '/general-settings/*.php' ) as $filename ) {
	include_once $filename;
}

// Load text editor settings.
foreach ( glob( dirname( __FILE__ ) . '/text-editor-settings/*.php' ) as $filename ) {
	include_once $filename;
}

// Load miscellaneaous settings.
foreach ( glob( dirname( __FILE__ ) . '/miscellaneous-settings/*.php' ) as $filename ) {
	include_once $filename;
}

// Load working hrs.
foreach ( glob( dirname( __FILE__ ) . '/working-hrs/*.php' ) as $filename ) {
	include_once $filename;
}

// Load appearence.
foreach ( glob( dirname( __FILE__ ) . '/appearence/*.php' ) as $filename ) {
	include_once $filename;
}

// Load appearence.
foreach ( glob( dirname( __FILE__ ) . '/ticket-tags/*.php' ) as $filename ) {
	include_once $filename;
}

