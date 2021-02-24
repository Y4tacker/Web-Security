<?php

/*

Copyright (c) 2017-2021 Mika Tuupola

Permission is hereby granted, free of charge, to any person obtaining a copy
of this software and associated documentation files (the "Software"), to deal
in the Software without restriction, including without limitation the rights
to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
copies of the Software, and to permit persons to whom the Software is
furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in all
copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
SOFTWARE.

*/

/**
 * @see       https://github.com/tuupola/base85
 * @license   https://opensource.org/licenses/mit-license.php
 */

namespace Tuupola;

final class Base85
{
    /* Adobe ASCII85. Only all zero data exception, ignore whitespace. */
    /* https://www.adobe.com/products/postscript/pdfs/PLRM.pdf */
    const ASCII85 = "!\"#$%&'()*+,-./0123456789:;<=>?@ABCDEFGHIJKLMNOPQRSTUVWXYZ[\]^_`abcdefghijklmnopqrstu";

    /* https://rfc.zeromq.org/spec:32/Z85/ */
    const Z85 = "0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz.-:+=^!/*?&<>()[]{}@%$#";

    /* https://tools.ietf.org/html/rfc1924 which is an Aprils fools joke. */
    const RFC1924 = "0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz!#$%&()*+-;<=>?@^_`{|}~";

    private $options = [];
    private $encoder;

    public function __construct($options = [])
    {
        $this->options = array_merge($this->options, (array) $options);
        if (function_exists("gmp_init")) {
            $this->encoder = new Base85\GmpEncoder($this->options);
        }
        $this->encoder = new Base85\PhpEncoder($this->options);
    }

    /**
     * Encode given data to a base85 string
     */
    public function encode($data)
    {
        return $this->encoder->encode($data);
    }

    /**
     * Decode given a base85 string back to data
     */
    public function decode($data, $integer = false)
    {
        return $this->encoder->decode($data, $integer);
    }

    /**
     * Encode given integer to a base85 string
     */
    public function encodeInteger($data)
    {
        return $this->encoder->encodeInteger($data);
    }
    /**
     * Decode given base85 string back to an integer
     */
    public function decodeInteger($data)
    {
        return $this->encoder->decodeInteger($data);
    }
}
