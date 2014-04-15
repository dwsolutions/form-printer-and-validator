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

class File extends Input {

    protected $strTemplate = '
        <div class="form-control">
            <button class="btn btn-success fileinput-button">
                %icon%
                <span>%label%</span>
                <input type="file" name="%name%%multiple_indicator%" %multiple%>
            </button>
        </div>';
    private $strDefaultIcon = '<i class="fa fa-plus-circle"></i>';

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
        $isMultiple = array_key_exists("multiple", $this->arrData) ? $this->arrData['multiple'] : false;
        $arrReplace = array();
        $arrReplace['%icon%'] = array_key_exists("icon", $this->arrData) ? $this->arrData['icon'] : $this->strDefaultIcon;
        $arrReplace['%id%'] = array_key_exists("id", $this->arrData) ? $this->arrData['id'] : md5("file-" . $this->strName);
        $arrReplace['%classname%'] = array_key_exists("classname", $this->arrData) ? $this->arrData['classname'] : "";
        $arrReplace['%label%'] = array_key_exists("select_label", $this->arrData) ? $this->arrData['select_label'] : FAJLOK_HOZZAADASA;
        $arrReplace['%multiple_indicator%'] = $isMultiple ? "[]" : "";
        $arrReplace['%multiple%'] = $isMultiple ? "multiple" : "";
        $arrReplace['%name%'] = $this->strName;
        $this->strRender = str_replace(array_keys($arrReplace), array_values($arrReplace), $this->strTemplate);
    }

}

?>
