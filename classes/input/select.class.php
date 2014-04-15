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
class Select extends Input {

    protected $strTemplate = '<select name="%name%" %html% id="%id%" class="form-control %classname%" >%options%</select>';

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
        $arrSourceData = $this->objForm->getSourceData();
        $default = array_key_exists("default", $this->arrData) ? $this->arrData['default'] : "";
        $default_first_element_text = array_key_exists("default_first_element_text", $this->arrData) ? $this->arrData['default_first_element_text'] : "-";
        $default_first_element_value = array_key_exists("default_first_element_value", $this->arrData) ? $this->arrData['default_first_element_value'] : "0";
        $value = array_key_exists($this->strName, $arrSourceData) ? $arrSourceData[$this->strName] : $default;

        $arrReplace = array();
        $arrReplace["%name%"] = $this->strName;
        $arrReplace["%classname%"] = array_key_exists("classname", $this->arrData) ? $this->arrData['classname'] : "";
        $arrReplace["%id%"] = array_key_exists("id", $this->arrData) ? $this->arrData['id'] : "";
        $arrReplace["%html%"] = array_key_exists("html", $this->arrData) ? $this->arrData['html'] : "";

        $arrReplace['%options%'] = "";
        $arrReplace['%options%'] .= '<option value="' . $default_first_element_value . '">' . $default_first_element_text . '</option>';
        $arrReplace['%first%'] = "-";
        foreach ((array) $this->arrData['options'] as $key => $value) {
            $checked = array_key_exists($this->strName, $arrSourceData) && $key == $arrSourceData[$this->strName] ? 'selected="selected"' : '';
            $arrReplace['%options%'] .= '<option value="' . $key . '" ' . $checked . '>' . $value . '</option>';
        }
        $this->strRender = str_replace(array_keys($arrReplace), array_values($arrReplace), $this->strTemplate);
    }

}

?>
