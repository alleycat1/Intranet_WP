<?php

namespace Barn2\Plugin\Document_Library_Pro\Dependencies\Lib\Table;

/**
 * Represents a HTML table. This class allows you to build a table by sequentially adding headings, rows, data, etc, and then
 * outputting to either HTML, and array or JSON.
 *
 * For example, the full HTML for the table can then be obtained by calling the <link>to_html()</link> method. This makes it a
 * much cleaner way of producing the HTML required for a table.
 *
 * @package   Barn2\datatables
 * @author    Barn2 Plugins <support@barn2.com>
 * @license   GPL-3.0
 * @copyright Barn2 Media Ltd
 */
class Html_Data_Table
{
    private $attributes = [];
    private $header;
    private $footer;
    private $data = [];
    private $current_row;
    private $above = [];
    // deprecated
    private $below = [];
    // deprecated
    public function __construct()
    {
        $this->header = new Html_Table_Row();
        $this->footer = new Html_Table_Row();
        $this->current_row = new Html_Table_Row();
    }
    public function add_attribute($name, $value)
    {
        $this->attributes[$name] = $value;
    }
    public function add_header($heading, $attributes = \false, $key = \false, $use_th = \true)
    {
        $this->header->add_cell($heading, $attributes, $key, $use_th);
    }
    public function add_footer($heading, $attributes = \false, $key = \false, $use_th = \true)
    {
        $this->footer->add_cell($heading, $attributes, $key, $use_th);
    }
    public function new_row($atts = \false)
    {
        if ($this->current_row && !$this->current_row->is_empty()) {
            if (!$this->current_row->has_data()) {
                $this->current_row->add_class('no-data');
            }
            $this->data[] = $this->current_row;
        }
        $this->current_row = new Html_Table_Row($atts);
    }
    public function add_data($data, $attributes = \false, $key = \false)
    {
        if (\is_array($data)) {
            $data = \implode('', $data);
        }
        $this->current_row->add_cell($data, $attributes, $key);
    }
    public function get_data()
    {
        $this->new_row();
        return $this->data;
    }
    public function set_data($data)
    {
        $this->data = (array) $data;
    }
    public function add_above($above)
    {
        if ($above) {
            $this->above[] = $above;
        }
    }
    public function add_below($below)
    {
        if ($below) {
            $this->below[] = $below;
        }
    }
    public function to_html($data_only = \false)
    {
        $data = '';
        foreach ($this->get_data() as $row) {
            $data .= $row->to_html();
        }
        if ($data_only) {
            return $data;
        } else {
            $thead = !$this->header->is_empty() ? '<thead>' . $this->header->to_html() . '</thead>' : '';
            $tfoot = !$this->footer->is_empty() ? '<tfoot>' . $this->footer->to_html() . '</tfoot>' : '';
            $tbody = $data ? '<tbody>' . $data . '</tbody>' : '';
            $above = $this->above ? \implode("\n", $this->above) : '';
            $below = $this->below ? \implode("\n", $this->below) : '';
        }
        return \sprintf('%5$s<table%1$s>%2$s%3$s%4$s</table>%6$s', Table_Util::format_attributes($this->attributes), $thead, $tbody, $tfoot, $above, $below);
    }
    public function to_array($data_only = \false)
    {
        $data = $this->get_data();
        $body = [];
        foreach ($data as $row) {
            $body[] = $row->to_array();
        }
        if ($data_only) {
            return $body;
        } else {
            return ['attributes' => $this->attributes, 'thead' => $this->header->to_array(), 'tbody' => $body, 'tfoot' => $this->footer->to_array(), 'above' => $this->above, 'below' => $this->below];
        }
    }
    public function to_json($data_only = \false)
    {
        return \wp_json_encode($this->to_array($data_only));
    }
    public function reset()
    {
        $this->attributes = [];
        $this->header = new Html_Table_Row();
        $this->footer = new Html_Table_Row();
        $this->above = [];
        $this->below = [];
        $this->reset_data();
    }
    public function reset_data()
    {
        $this->current_row = new Html_Table_Row();
        $this->data = [];
    }
}
