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
class Datefromto extends Input {

    protected $strTemplate = '
        <input type="text" name="%name%" class="form-control datepicker" value="%value%" placeholder="%placeholder%" />
        <span class="input-group-addon">-</span>
        <input type="text" name="%name2%" class="form-control datepicker" value="%value2%" placeholder="%placeholder2%" />';

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
        $default2 = array_key_exists("default2", $this->arrData) ? $this->arrData['default2'] : "";
        $value = array_key_exists($this->strName, $arrSourceData) ? $arrSourceData[$this->strName] : $default;
        $value2 = array_key_exists($this->arrData['name2'], $arrSourceData) ? $arrSourceData[$this->arrData['name2']] : $default2;
        $arrReplace = array();
        $arrReplace['%name%'] = $this->strName;
        $arrReplace['%name2%'] = $this->arrData['name2'];
        $arrReplace['%value%'] = $value;
        $arrReplace['%value2%'] = $value2;
        $arrReplace['%placeholder%'] = array_key_exists("placeholder", $this->arrData) ? $this->arrData['placeholder'] : "";
        $arrReplace['%placeholder2%'] = array_key_exists("placeholder2", $this->arrData) ? $this->arrData['placeholder2'] : "";

        $input2 = new Datepicker(array(
            "name" => $this->arrData['name2'],
            "renderable" => false
                ), $this->objForm);

        $this->addValidation("datesmaller", array(
            "relative_to" => "input",
            "relative_to_value" => $this->arrData['name2'],
            "enabled_equal" => true
        ));

        $input2->addValidation("datebigger", array(
            "relative_to" => "input",
            "relative_to_value" => $this->arrData['name'],
            "enabled_equal" => true
        ));
        $this->addValidation("date", true);
        $input2->addValidation("date", true);
        $input2->addValidation("mandatory", $this->getValidation("mandatory"));
        $this->objForm->addObjInput($input2);
        //$this->addValidation("", $validateSettings);

        $this->strRender = str_replace(array_keys($arrReplace), array_values($arrReplace), $this->strTemplate);
    }

}

?>
