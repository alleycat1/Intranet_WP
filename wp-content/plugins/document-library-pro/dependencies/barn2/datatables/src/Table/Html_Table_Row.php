<?php

namespace Barn2\Plugin\Document_Library_Pro\Dependencies\Lib\Table;

/**
 * Represents a row in a <code>Html_Data_Table</code>.
 *
 * @package   Barn2\datatables
 * @author    Barn2 Plugins <support@barn2.com>
 * @license   GPL-3.0
 * @copyright Barn2 Media Ltd
 */
class Html_Table_Row
{
    private $attributes = [];
    private $cells = [];
    public function __construct($atttributes = [])
    {
        $this->attributes = $atttributes ? (array) $atttributes : [];
    }
    public function add_cell($data, $attributes = [], $key = \false, $is_heading = \false)
    {
        if (\false === $key) {
            $this->cells[] = new Html_Table_Cell($data, $attributes, $is_heading);
        } else {
            $this->cells[$key] = new Html_Table_Cell($data, $attributes, $is_heading);
        }
    }
    public function add_class($class)
    {
        if (!empty($this->attributes['class'])) {
            $this->attributes['class'] = $this->attributes['class'] . ' ' . $class;
        } else {
            $this->attributes['class'] = $class;
        }
    }
    public function length()
    {
        return \count($this->cells);
    }
    public function is_empty()
    {
        return 0 === $this->length();
    }
    public function has_data()
    {
        return !$this->is_empty() && '' !== \trim(\implode('', \wp_list_pluck($this->cells, 'data')));
    }
    public function to_html()
    {
        if ($this->is_empty()) {
            return '';
        }
        $cells = '';
        foreach ($this->cells as $cell) {
            $cells .= $cell->to_html();
        }
        return \sprintf('<tr%s>%s</tr>', Table_Util::format_attributes($this->attributes), $cells);
    }
    public function to_array()
    {
        if ($this->is_empty()) {
            return [];
        }
        return ['attributes' => $this->attributes, 'cells' => \array_map([__CLASS__, 'cell_to_array'], $this->cells)];
    }
    private static function cell_to_array($cell)
    {
        return $cell->to_array();
    }
}
