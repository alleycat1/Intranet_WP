<?php

namespace Barn2\Plugin\Document_Library_Pro\Dependencies\Lib;

/**
 * Allows a temporary hook environment to be created for a given timeframe (i.e. scope), where any hooks
 * added or removed will be recognised only during the specified scope. After the given scope, the
 * original WordPress hook environment is restored to its previous state.
 *
 * @package   Barn2\barn2-lib
 * @author    Barn2 Plugins <support@barn2.com>
 * @license   GPL-3.0
 * @copyright Barn2 Media Ltd
 * @version   1.3
 */
class WP_Scoped_Hooks
{
    private $hooks;
    private $start_hook;
    private $end_hook;
    public function __construct($start_hook = '', $end_hook = '')
    {
        $this->initialize();
        $this->set_scope($start_hook, $end_hook);
    }
    private function initialize()
    {
        $this->hooks = ['added' => [], 'removed' => []];
        if ($this->start_hook) {
            \remove_action($this->start_hook, [$this, 'register']);
        }
        if ($this->end_hook) {
            \remove_action($this->end_hook, [$this, 'reset']);
        }
    }
    public function add_action($tag, $function, $priority = 10, $accepted_args = 1)
    {
        $this->add_filter($tag, $function, $priority, $accepted_args);
    }
    public function add_filter($tag, $function, $priority = 10, $accepted_args = 1)
    {
        $this->hooks['added'][] = [$tag, $function, $priority, $accepted_args];
    }
    public function remove_action($tag, $function, $priority = 10, $accepted_args = 1)
    {
        $this->remove_filter($tag, $function, $priority, $accepted_args);
    }
    public function remove_filter($tag, $function, $priority = 10, $accepted_args = 1)
    {
        $this->hooks['removed'][] = [$tag, $function, $priority, $accepted_args];
    }
    public function set_scope($start_hook, $end_hook)
    {
        if ($start_hook && $end_hook) {
            $this->start_hook = $start_hook;
            $this->end_hook = $end_hook;
            \add_action($start_hook, [$this, 'register']);
            \add_action($end_hook, [$this, 'reset']);
        }
    }
    public function register()
    {
        \array_walk($this->hooks['added'], [$this, 'array_walk_add_filter']);
        $this->hooks['removed'] = \array_filter($this->hooks['removed'], [$this, 'array_walk_remove_filter']);
    }
    public function reset()
    {
        \array_walk($this->hooks['added'], [$this, 'array_walk_remove_filter']);
        \array_walk($this->hooks['removed'], [$this, 'array_walk_add_filter']);
        $this->initialize();
    }
    private function array_walk_add_filter($hook)
    {
        \add_filter($hook[0], $hook[1], $hook[2], $hook[3]);
    }
    private function array_walk_remove_filter($hook)
    {
        return \remove_filter($hook[0], $hook[1], $hook[2], $hook[3]);
    }
}
