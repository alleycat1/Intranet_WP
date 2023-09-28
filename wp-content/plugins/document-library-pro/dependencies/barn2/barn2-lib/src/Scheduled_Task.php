<?php

namespace Barn2\Plugin\Document_Library_Pro\Dependencies\Lib;

/**
 * Abstract class which represents a single scheduled task using WordPress CRON.
 *
 * The task is automatically unscheduled on plugin deactivation.
 *
 * @package   Barn2\barn2-lib
 * @author    Barn2 Plugins <support@barn2.com>
 * @license   GPL-3.0
 * @copyright Barn2 Media Ltd
 * @version   1.0
 */
abstract class Scheduled_Task implements Schedulable
{
    private $plugin_file;
    public function __construct($plugin_file)
    {
        $this->plugin_file = $plugin_file;
    }
    public function schedule()
    {
        // Attach the action to run when the cron event is fired.
        \add_action($this->get_cron_hook(), [$this, 'run']);
        // Schedule the cron event if not already scheduled.
        if (!\wp_next_scheduled($this->get_cron_hook())) {
            \wp_schedule_event(\time(), $this->get_interval(), $this->get_cron_hook());
        }
        // Un-schedule the event on plugin deactivation.
        \register_deactivation_hook($this->plugin_file, [$this, 'unschedule']);
    }
    protected abstract function get_cron_hook();
    protected abstract function get_interval();
    public function unschedule()
    {
        $timestamp = \wp_next_scheduled($this->get_cron_hook());
        if ($timestamp) {
            \wp_unschedule_event($timestamp, $this->get_cron_hook());
        }
    }
    public abstract function run();
}
