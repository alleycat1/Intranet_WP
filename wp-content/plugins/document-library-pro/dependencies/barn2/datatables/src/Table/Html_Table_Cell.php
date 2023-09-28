<?php

namespace Barn2\Plugin\Document_Library_Pro\Dependencies\Lib\Table;

/**
 * Represents a cell in a <code>Html_Table_Row</code>.
 *
 * @package   Barn2\datatables
 * @author    Barn2 Plugins <support@barn2.com>
 * @license   GPL-3.0
 * @copyright Barn2 Media Ltd
 */
class Html_Table_Cell
{
    public $data = '';
    private $attributes = [];
    private $is_heading = \false;
    public function __construct($data, $attributes = [], $is_heading = \false)
    {
        if (\is_array($data)) {
            $data = \implode('', $data);
        } elseif (\is_object($data)) {
            $data = \serialize($data);
        }
        $this->data = (string) $data;
        $this->attributes = $attributes ? (array) $attributes : [];
        $this->is_heading = (bool) $is_heading;
    }
    public function is_heading()
    {
        return $this->is_heading;
    }
    public function to_array()
    {
        return ['attributes' => $this->attributes, 'data' => $this->data];
    }
    public function to_html()
    {
        $format = $this->is_heading ? '<th%s>%s</th>' : '<td%s>%s</td>';
        return \sprintf($format, Table_Util::format_attributes($this->attributes), $this->data);
    }
}
