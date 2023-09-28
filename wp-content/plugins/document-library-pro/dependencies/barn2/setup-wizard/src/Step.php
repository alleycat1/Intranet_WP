<?php

/**
 * @package   Barn2\setup-wizard
 * @author    Barn2 Plugins <support@barn2.com>
 * @license   GPL-3.0
 * @copyright Barn2 Media Ltd
 */
namespace Barn2\Plugin\Document_Library_Pro\Dependencies\Setup_Wizard;

use Barn2\Plugin\Document_Library_Pro\Dependencies\Setup_Wizard\Interfaces\Pluggable;
/**
 * Handles configuration of a setup wizard step.
 */
abstract class Step implements Pluggable
{
    /**
     * Step ID (must be unique to each step)
     *
     * @var string
     */
    public $id;
    /**
     * Name of the step.
     *
     * @var string
     */
    public $name;
    /**
     * Heading title of the step.
     *
     * @var string
     */
    public $title;
    /**
     * Description of the step.
     *
     * @var string
     */
    public $description;
    /**
     * Tooltip displayed next to the description.
     *
     * @var string
     */
    public $tooltip;
    /**
     * The instance of the plugin making use of the setup wizard.
     *
     * @var object
     */
    private $plugin;
    /**
     * The wizard holding the step.
     *
     * @var Setup_Wizard
     */
    private $wizard;
    /**
     * Check whether the step is hidden by default.
     *
     * @var boolean
     */
    private $hidden = \false;
    /**
     * Attach a plugin to the step.
     *
     * @param object $plugin
     * @return Step
     */
    public function with_plugin($plugin)
    {
        $this->plugin = $plugin;
        return $this;
    }
    /**
     * Get the instance of the plugin attached to the step.
     *
     * @return object
     */
    public function get_plugin()
    {
        return $this->plugin;
    }
    /**
     * Assign a wizard to the step.
     *
     * @param Setup_Wizard $wizard
     * @return Step
     */
    public function with_wizard(Setup_Wizard $wizard)
    {
        $this->wizard = $wizard;
        return $this;
    }
    /**
     * Get the wizard assigned to the step.
     *
     * @return Setup_Wizard
     */
    public function get_wizard()
    {
        return $this->wizard;
    }
    /**
     * Define the list of fields for this step.
     *
     * @return array
     */
    public abstract function setup_fields();
    /**
     * Get the list of defined fields for the step.
     *
     * @return array
     */
    public function get_fields()
    {
        return $this->setup_fields();
    }
    /**
     * Get step name
     *
     * @return string
     */
    public function get_name()
    {
        return $this->name;
    }
    /**
     * Set step name
     *
     * @param string $name
     * @return Step
     */
    public function set_name(string $name)
    {
        $this->name = $name;
        return $this;
    }
    /**
     * Get step description
     *
     * @return string
     */
    public function get_description()
    {
        return $this->description;
    }
    /**
     * Set a description for the step.
     *
     * @param string $desc
     * @return Step
     */
    public function set_description(string $desc)
    {
        $this->description = $desc;
        return $this;
    }
    /**
     * Set an ID for the step.
     *
     * @param string $id
     * @return Step
     */
    public function set_id(string $id)
    {
        $this->id = $id;
        return $this;
    }
    /**
     * Get the ID of the step.
     *
     * @return string
     */
    public function get_id()
    {
        return $this->id;
    }
    /**
     * Set an heading title for the step.
     *
     * @param string $title
     * @return Step
     */
    public function set_title(string $title)
    {
        $this->title = $title;
        return $this;
    }
    /**
     * Get the heading title for the step.
     *
     * @return string
     */
    public function get_title()
    {
        return $this->title;
    }
    /**
     * Set tooltip for the step.
     *
     * @param string $tooltip
     * @return Step
     */
    public function set_tooltip(string $tooltip)
    {
        $this->tooltip = $tooltip;
        return $this;
    }
    /**
     * Get the tooltip for the step.
     *
     * @return string
     */
    public function get_tooltip()
    {
        return $this->tooltip;
    }
    /**
     * Mark the step as hidden or not.
     *
     * @param boolean $hidden
     * @return Step
     */
    public function set_hidden(bool $hidden)
    {
        $this->hidden = $hidden;
        return $this;
    }
    /**
     * Check if the step is hidden or not.
     *
     * @return boolean
     */
    public function is_hidden()
    {
        return $this->hidden;
    }
    /**
     * Send a json error back to the react app.
     *
     * @param string $message
     * @return void
     */
    public function send_error(string $message)
    {
        return Api::send_error_response(['message' => $message]);
    }
    /**
     * Get the values submitted through the ajax request.
     * But only return the values belonging to the step.
     *
     * @return array
     */
    protected function get_submitted_values()
    {
        $values = [];
        foreach ($this->get_fields() as $key => $field) {
            $disallowed = ['title', 'heading', 'list', 'image'];
            if (\in_array($field['type'], $disallowed)) {
                continue;
            }
            if (isset($_POST[$key]) && !empty($_POST[$key])) {
                $values[$key] = Util::clean($_POST[$key]);
            }
        }
        return $values;
    }
    /**
     * Handle the submission of the step via ajax.
     *
     * @return \WP_REST_Response
     */
    public abstract function submit(array $values);
}
