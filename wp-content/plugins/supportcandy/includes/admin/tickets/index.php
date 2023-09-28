<?php
// Load widget classes.
foreach ( glob( dirname( __FILE__ ) . '/widgets/*.php' ) as $filename ) {
	include_once $filename;
}
