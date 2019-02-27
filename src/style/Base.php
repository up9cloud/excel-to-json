<?php

namespace com\funto\Converter\style;

use com\funto\Converter\Encoder;

/**
 * now output type only support json format.
 */
abstract class Base
{
    protected $data = [];
    protected $output_type = 'json';
    public function __construct(array $columns, array $data, $offset_x = 0, $offset_y = 0)
    {
        $this->data = $this->format($columns, $data, $offset_x, $offset_y);
    }
    public function setOutputType($type)
    {
        $this->output_type = $type;
    }
    protected function format($columns, $data, $offset_x = 0, $offset_y = 0)
    {
        throw new \ErrorException('must override this method.');
    }
    public function __toString()
    {
        switch ($this->output_type) {
            case 'json':
            default:
                return Encoder::toJson($this->data);
                break;
        }
    }
}
