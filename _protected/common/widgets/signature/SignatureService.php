<?php

namespace common\widgets\signature;

/**
 * Class SignatureService
 * @package common\widgets\signature
 */
class SignatureService {

    // private $acceptedformat = 'image/jsignature;base30';
    private $chunkSeparator = '';
    private $charmap = array(); // {'1':'g','2':'h','3':'i','4':'j','5':'k','6':'l','7':'m','8':'n','9':'o','a':'p','b':'q','c':'r','d':'s','e':'t','f':'u','0':'v'}
    private $charmap_reverse = array(); // will be filled by 'uncompress*" function
    private $allchars = array();
    private $bitness = 0;
    private $minus = '';
    private $plus = '';

    function __construct() {
        $this->allchars = str_split('0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWX');
        $this->bitness = sizeof($this->allchars) / 2;
        $this->minus = 'Z';
        $this->plus = 'Y';
        $this->chunkSeparator = '_';

        for($i = $this->bitness-1; $i > -1; $i--){
            $this->charmap[$this->allchars[$i]] = $this->allchars[$i+$this->bitness];
            $this->charmap_reverse[$this->allchars[$i+$this->bitness]] = $this->allchars[$i];
        }
    }

    /**
     * Decompresses half of a stroke in a base30-encoded jSignature image.
     *
     * @param string $datastring
     * @return array Array of half decompressed stroke
     *
     * Example:
    $c = new SignatureBase30();
    $t = array(236, 233, 231, 229, 226, 224, 222, 216, 213, 210, 205, 202, 200, 198, 195, 193, 191, 189, 186, 183, 180, 178, 174, 172);
    $leg = '7UZ32232263353223222333242';
    $a = $c->uncompress_stroke_leg($leg);
    $t == $a
     *
     *  We convert half-stroke (only 'x' series or only 'y' series of numbers)
    data string like this:
    "5agm12100p1235584210m53"
    is converted into this:
    [517,516,514,513,513,513,514,516,519,524,529,537,541,543,544,544,539,536]
    each number in the chain is converted such:
    - digit char = start of new whole number. Alpha chars except "p","m" are numbers in hiding.
    These consecutive digist expressed as alphas mapped back to digit char.
    resurrected number is the diff between this point and prior coord.
    - running polarity is attached to the number.
    - we undiff (signed number + prior coord) the number.
    - if char 'm','p', flip running polarity
     */
    private function _uncompressedStrokeLeg($datastring)
    {
        $answer = array();
        $chars = str_split( $datastring );
        $l = sizeof( $chars );
        $ch = '';
        $polarity = 1;
        $partial = array();
        $preprewhole = 0;
        $prewhole = 0;

        for($i = 0; $i < $l; $i++){
            // echo "adding $i of $l to answer\n";
            $ch = $chars[$i];
            if (array_key_exists($ch, $this->charmap) || $ch == $this->minus || $ch == $this->plus){

                // this is new number - start of a new whole number.
                // before we can deal with it, we need to flush out what we already
                // parsed out from string, but keep in limbo, waiting for this sign
                // that prior number is done.
                // we deal with 3 numbers here:
                // 1. start of this number - a diff from previous number to
                //    whole, new number, which we cannot do anything with cause
                //    we don't know its ending yet.
                // 2. number that we now realize have just finished parsing = prewhole
                // 3. number we keep around that came before prewhole = preprewhole

                if (sizeof($partial) != 0) {
                    // yep, we have some number parts in there.
                    $prewhole = intval( implode('', $partial), $this->bitness) * $polarity + $preprewhole;
                    array_push( $answer, $prewhole );
                    $preprewhole = $prewhole;
                }

                if ($ch == $this->minus){
                    $polarity = -1;
                    $partial = array();
                } else if ($ch == $this->plus){
                    $polarity = 1;
                    $partial = array();
                } else {
                    // now, let's start collecting parts for the new number:
                    $partial = array($ch);
                }
            } else /* alphas replacing digits */ {
                // more parts for the new number
                array_push( $partial, $this->charmap_reverse[$ch]);
            }
        }
        // we always will have something stuck in partial
        // because we don't have closing delimiter
        array_push( $answer, intval( implode('',$partial), $this->bitness ) * $polarity + $preprewhole );

        return $answer;
    }

    /**
     * Converts base30 data to Native coordinate array
     *
     * @param string $data_string
     * @return array Array of native format of strokes
     *
     * Example:
     *
     * $c = new jSignature_base30();
     * $signature = "3E13Z5Y5_1O24Z66_1O1Z3_3E2Z4";
     * or
     * $signature = "image/jsignature;base30,3E13Z5Y5_1O24Z66_1O1Z3_3E2Z4";
     *
     * // This is exactly the same as "native" format within jSignature.
     * $t = array(
     *          array(
     *              'x'=>array(100,101,104,99,104),
     *              'y'=>array(50,52,56,50,44)
     *          ),
     *          array(
     *              'x'=>array(50,51,48),
     *              'y'=>array(100,102,98)
     *          )
     *      );
     *
     * $a = $c->Base64ToNative($signature);
     * $t == $a
     */
    public function base30ToNative($data_string)
    {
        $data = array();

        if(strpos($data_string, ',')) {
            $data_arr = explode(',', $data_string);
            $data_string = $data_arr[1];
        }

        $chunks = explode( $this->chunkSeparator, $data_string );
        $l = sizeof($chunks) / 2;
        for ($i = 0; $i < $l; $i++){
            array_push( $data, array(
                'x' => $this->_uncompressedStrokeLeg($chunks[$i*2])
            , 'y' => $this->_uncompressedStrokeLeg($chunks[$i*2+1])
            ));
        }
        return $data;
    }

    /**
     * This is a simple, points-to-lines (not curves) renderer.
     * Keeping it around so we can activate it from time to time and see if smoothing logic is off much.
     *
     * @param array $stroke Hash representing a single stroke, with two properties
     * 		('x' => array(), 'y' => array()) where 'array()' is an array of coordinates for that axis.
     * @param int $shiftX
     * @param int $shiftY
     * @returns string Like so 'M 53 7 l 1 2 3 4 -5 -6 5 -6' which is in format of SVG's Path.d argument.
     */
    private function _addStroke($stroke, $shiftX, $shiftY){
        $lastX = $stroke['x'][0];
        $lastY = $stroke['y'][0];
        $l = sizeof( $stroke['x'] );
        $answer = array('M', round( $lastX - $shiftX, 2) , round( $lastY - $shiftY, 2), 'l');

        if ($l == 1){
            // meaning this was just a DOT, not a stroke.
            // instead of creating a circle, we just create a short line "up and to the right" :)
            array_push($answer, 1);
            array_push($answer, -1);
        } else {
            for($i = 1; $i < $l; $i++){
                array_push( $answer, $stroke['x'][$i] - $lastX);
                array_push( $answer, $stroke['y'][$i] - $lastY);
                $lastX = $stroke['x'][$i];
                $lastY = $stroke['y'][$i];
            }
        }
        return implode(' ', $answer);
    }

    /**
     * Converts native format to SVG
     * @param array $data Array of native format
     * @param string $color String representing color of shapes
     * @param string $background String representing background color of element
     * @param int $lineSize String representing line size of shapes
     * @return string
     */
    public function nativeToSVG($data, $color='darkblue', $background='none', $lineSize=3){
        $answer = array(
            '<?xml version="1.0" encoding="UTF-8" standalone="no"?>'
        , '<!DOCTYPE svg PUBLIC "-//W3C//DTD SVG 1.1//EN" "http://www.w3.org/Graphics/SVG/1.1/DTD/svg11.dtd">'
        );
        $l = sizeof( $data );
        $xLimits = array();
        $yLimits = array();
        $sizeX = 0;
        $sizeY = 0;
        $shiftX = 0;
        $shiftY = 0;
        $padding = 1;

        if($l !== 0) {
            for($i = 0; $i < $l; $i++){
                $stroke = $data[$i];
                $xLimits = array_merge($xLimits, $stroke['x']);
                $yLimits = array_merge($yLimits, $stroke['y']);
            }

            $minX = min($xLimits) - $padding;
            $maxX = max($xLimits) + $padding;
            $minY = min($yLimits) - $padding;
            $maxY = max($yLimits) + $padding;
            $shiftX = $minX < 0 ? 0 : $minX;
            $shiftY = $minY < 0 ? 0 : $minY;
            $sizeX = $maxX - $minX;
            $sizeY = $maxY - $minY;
        }

        $answer[] = '<svg xmlns="http://www.w3.org/2000/svg" version="1.1" width="'. $sizeX. '" height="'. $sizeY. '">';
        $answer[] = '<style type="text/css"><![CDATA[ .signature-style {fill:'.$background.';stroke:'.$color.';stroke-width:'.$lineSize.';stroke-linecap:round;stroke-linejoin:round}]]></style>';

        for($i = 0; $i < $l; $i++){
            $answer[] = '<path class="signature-style" d="' . $this->_addStroke($data[$i], $shiftX, $shiftY) . '"/>';
            //$answer[] = '<path fill="'.$background.'" stroke="'.$color.'" stroke-width="'.$lineSize.'" stroke-linecap="round" stroke-linejoin="round" d="' . $this->addStroke($data[$i], $shiftX, $shiftY) . '"/>';
        }
        $answer[] = '</svg>';

        return implode('', $answer);
    }

    /**
     * Converts base30 signature encoded data string to the SVG image data
     *
     * @param $base30_data_string
     * @param string $color
     * @param string $background
     * @param int $lineSize
     * @return string|null
     *
     * $base30_data_string = "3E13Z5Y5_1O24Z66_1O1Z3_3E2Z4";
     * or
     * $base30_data_string = "image/jsignature;base30,3E13Z5Y5_1O24Z66_1O1Z3_3E2Z4";
     */
    public function base30ToSVG($base30_data_string, $color='darkblue', $background='none', $lineSize=3)
    {
        $svg_data = null;
        $native_array = $this->base30ToNative($base30_data_string);
        if(!empty($native_array)) {
            $svg_data = $this->nativeToSVG($native_array, $color, $background, $lineSize);
        }
        return $svg_data;
    }
}
