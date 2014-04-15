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
class Radio extends Input {

    //protected $strTemplate = '<div class="radiocontainer"><label for="%for%">%label%</label>%input%</div>';
    protected $strTemplate = '<div class="form-control">%inputs%</div>';
    protected $strInputTemplate = '<div class="col-lg-1"><input type="radio" name="%name%" %checked% value="%value%" class="%classname%" id="%id%" /><label for="%for%">%label%</label></div>';

    public function render() {
        return $this->strRender;
    }

    public function getSaveData($arrMethodData) {
        $saveData = "";
        if (array_key_exists($this->strName, $arrMethodData)) {
            $saveData = $arrMethodData[$this->strName];
        } else {
            return $saveData;
        }

        if (array_key_exists('beforeSave', $this->arrData)) {
            if (function_exists($this->arrData['beforeSave'])) {
                $saveData = call_user_func_array($this->arrData['beforeSave'], array($saveData));
            } else {
                throw new Exception($this->arrData['beforeSave'] . FUNKCIO . NEM_LETEZIK);
            }
        }

        return $saveData;
    }

    public function init() {
        $arrStringTokens = array();
        $arrSourceData = $this->objForm->getSourceData();
        $arrInputReplace = array();
        foreach ((array) $this->arrData['options'] as $key => $value) {
            $strInputId = md5($key . "-" . $value . "-" . $this->strName);
            $checked = array_key_exists($this->strName, $arrSourceData) && $key == $arrSourceData[$this->strName] ? 'checked="checked"' : '';
            $arrInputReplace['%for%'] = $strInputId;
            $arrInputReplace['%name%'] = $this->strName;
            $arrInputReplace['%id%'] = $strInputId;
            $arrInputReplace['%checked%'] = $checked;
            $arrInputReplace['%value%'] = $key;
            $arrInputReplace['%classname%'] = array_key_exists("classname", $this->arrData) ? $this->arrData['classname'] : '';
            $arrInputReplace['%label%'] = $value;

            $arrStringTokens[] = str_replace(array_keys($arrInputReplace), array_values($arrInputReplace), $this->strInputTemplate);
        }
        $this->strRender = str_replace("%inputs%", implode("", $arrStringTokens), $this->strTemplate);
    }

}

?>
