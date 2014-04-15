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
class Multiselect extends Input {

    protected $strTemplate = '%possibles%%buttons%%target%';
    //protected $strTemplate = '%possibles%%buttons%%target%';
    private $strPossiblesTemplate = '<select multiple="multiple" size="10" class="multiselect possibles form-control">%options%</select>';
    private $strTargetTemplate = '<select name="%name%[]" multiple="multiple" size="10" class="multiselect target form-control">%options%</select>';
    private $strOptionTemplate = '<option value="%value%">%text%</option>';
    private $strButtons = '
      <span class="multibuttons input-group-addon">
      <input class="btn move_left_all btn-primary" type="button" value="&gt;&gt;">
      <input class="btn move_left_sel btn-info" type="button" value="&gt;">
      <input class="btn move_right_sel btn-info" type="button" value="&lt;">
      <input class="btn move_right_all btn-primary" type="button" value="&lt;&lt;">
      </span>';

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
        $arrPossibleOptions = $arrSelectedOptions = array();
        $arrReplace = array();
        foreach ((array) $this->arrData['options'] as $key => $value) {
            $selected = (array_key_exists($this->strName, $arrSourceData) && in_array($key, (array) $arrSourceData[$this->strName])) || (array_key_exists("all_selected", $this->arrData) && $this->arrData['all_selected']);
            if ($selected) {
                $arrSelectedOptions[] = str_replace(array('%value%', '%text%'), array($key, $value), $this->strOptionTemplate);
            } else {
                $arrPossibleOptions[] = str_replace(array('%value%', '%text%'), array($key, $value), $this->strOptionTemplate);
            }
        }
        $arrReplace['%possibles%'] = str_replace("%options%", implode("", $arrPossibleOptions), $this->strPossiblesTemplate);
        $arrReplace['%buttons%'] = $this->strButtons;
        $arrReplace['%target%'] = str_replace(array("%name%", "%options%"), array($this->strName, implode("", $arrSelectedOptions)), $this->strTargetTemplate);
        $this->strRender = str_replace(array_keys($arrReplace), array_values($arrReplace), $this->strTemplate);
    }

}

?>