<?php
/*
The MIT License (MIT)

Copyright (c) 2014 Attila Molnár

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

class Submit {

    protected $strTemplate = '<input type="submit" id="%id%" class="%classname%" value="%label%" name="%name%" %html%/>';
    private $arrData;

    public function __construct($arrData) {
        $this->arrData = $arrData;
    }

    public function render() {
        $arrReplace = array();
        $arrReplace['%id%'] = array_key_exists("id", $this->arrData) ? $this->arrData['id'] : "";
        $arrReplace['%classname%'] = array_key_exists("classname", $this->arrData) ? $this->arrData['classname'] : "";
        $arrReplace['%label%'] = array_key_exists("label", $this->arrData) ? $this->arrData['label'] : MENTES;
        $arrReplace['%name%'] = array_key_exists("name", $this->arrData) ? $this->arrData['name'] : "";
        $arrReplace['%html%'] = array_key_exists("html", $this->arrData) ? $this->arrData['html'] : "";
        return str_replace(array_keys($arrReplace), array_values($arrReplace), $this->strTemplate);
    }

}

?>