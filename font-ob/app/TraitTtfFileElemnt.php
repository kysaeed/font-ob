<?php

namespace FontObscure;

trait TraitTtfFileElement
{

    protected static function unpackBinData($format, $binData, $offset = 0, $lengthList = null)
    {
        $sizeList = [
            'n' => 2,
            'N' => 4,
            'J' => 8,
            'A' => 1,
        ];

        $unpacked = [];
        foreach ($format as $name => $param) {
            $length = $param[1];
            if (is_string($length)) {
                if (array_key_exists($length, $unpacked)) {
                    $length = $unpacked[$length];
                } else {
                    $length = $lengthList[$length];
                }
            }
            $u = array_values(unpack("@{$offset}/{$param[0]}{$length}", $binData));
            if (array_key_exists(2, $param)) {
                if ($param[2]) {
                    switch ($param[0]) {
                        case 'n':
                            foreach ($u as &$value) {
                                if ($value > 0x7FFF) {
                                    $value = -(0x8000 - ($value & 0x7fff));
                                }
                            }
                            unset($value);
                            break;

                        case 'N':
                            foreach ($u as &$value) {
                                if ($value > 0x7FFFFFFF) {
                                    $value = -(0x80000000 - ($value & 0x7FFFFFFF));
                                }
                            }
                            unset($value);
                            break;
                    }
                }
            }
            if (count($u) == 1) {
                $u = $u[0];
            }
            $unpacked[$name] = $u;
            $offset += $sizeList[$param[0]] * $param[1];
        }
        return $unpacked;
    }

    protected static function packAttributes($format, $attributes)
    {
        $binData = '';
        foreach ($attributes as $name => $value) {
            $binData .= pack("{$format[$name][0]}", $value);
        }
        return $binData;
    }
}
