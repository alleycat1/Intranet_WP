<?php
/**
 * AI Helper - a companion to write a post title, excerpt and content, generate images and support a chat with AI technologies
 *
 * @addon ai-helper
 * @version 1.0
 *
 * @package ThemeREX Addons
 * @since v2.20.0
 */

namespace TrxAddons\AiHelper;

// Register autoloader for the addon's classes
require TRX_ADDONS_PLUGIN_DIR . TRX_ADDONS_PLUGIN_ADDONS . 'ai-helper/autoloader.php';
Autoloader::run();

// Add options to the ThemeREX Addons Options
new Options();

// Load a Gutenberg support
if ( ( function_exists( 'trx_addons_is_preview' ) && trx_addons_is_preview( 'gutenberg' ) )
	|| ( wp_is_json_request() && strpos( trx_addons_get_current_url(), 'ai-helper/v1' ) !== false )
) { 
	new Gutenberg\Helper();
}

// Load a MediaSelector support
if ( is_admin() || ( wp_doing_ajax() && strpos( trx_addons_get_value_gp( 'action' ), 'trx_addons_ai_helper_' ) !== false ) ) {
	new MediaLibrary\Helper();
}

// Load a shortcode "Image Generator"
require_once TRX_ADDONS_PLUGIN_DIR . TRX_ADDONS_PLUGIN_ADDONS . 'ai-helper/shortcodes/igenerator/igenerator.php';

// Load a shortcode "Text Generator"
require_once TRX_ADDONS_PLUGIN_DIR . TRX_ADDONS_PLUGIN_ADDONS . 'ai-helper/shortcodes/tgenerator/tgenerator.php';

// Load a shortcode "Chat"
require_once TRX_ADDONS_PLUGIN_DIR . TRX_ADDONS_PLUGIN_ADDONS . 'ai-helper/shortcodes/chat/chat.php';

// Load a shortcode "Chat Topics"
require_once TRX_ADDONS_PLUGIN_DIR . TRX_ADDONS_PLUGIN_ADDONS . 'ai-helper/shortcodes/chat_topics/chat_topics.php';
