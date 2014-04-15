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
class Listform extends Form {

    protected $strTemplate = '<form action="%action%" method="%method%" id="%id%" class="%class%" %html% enctype="multipart/form-data">%before-inputs%%inputs%%after-inputs%%buttons%</form>';
    protected $strRowTemplate = '<div class="form-group input-group">%label-before%%input%%label-after%</div>';
    protected $strLabelWrapperTemplate = '<span class="input-group-addon">%label%</span>';
    protected $strButtonRowTemplate = '<div class="buttonRow">%inputs%</div>';

    public function render($boolReturn = false) {
        parent::render();
        $arrReplace = array();
        $arrReplace['%action%'] = getExists("action", $this->arrSettings, '');
        $arrReplace['%method%'] = getExists("method", $this->arrSettings, 'POST');
        $arrReplace['%id%'] = getExists("id", $this->arrSettings, '');
        $arrReplace['%class%'] = getExists("classname", $this->arrSettings, '');
        $arrReplace['%html%'] = getExists("html", $this->arrSettings, '');
        $arrReplace['%before-inputs%'] = getExists("before-inputs", $this->arrSettings, '');
        $arrReplace['%inputs%'] = $this->renderInputs();
        $arrReplace['%after-inputs%'] = getExists("after-inputs", $this->arrSettings, '');
        $arrReplace['%buttons%'] = $this->renderButtons();
        $strRender = str_replace(array_keys($arrReplace), array_values($arrReplace), $this->strTemplate);
        if ($boolReturn) {
            return $strRender;
        }
        echo $strRender;
    }

    protected function renderInputs() {
        $arrStringTokens = $arrReplace = array();
        foreach ($this->arrInputElements as $objInput) {
            if ($objInput->getType() != "hidden") {
                if ($objInput->isRenderable()) {
                    $arrReplace['%label-before%'] = $objInput->getInputLabel() !== false ? str_replace('%label%', $objInput->getInputLabel(), $this->strLabelWrapperTemplate) : "";
                    $arrReplace['%label-after%'] = $objInput->getData("addon-after") !== false ? str_replace('%label%', $objInput->getData("addon-after"), $this->strLabelWrapperTemplate) : "";
                    $arrReplace['%input%'] = $objInput->render();
                    $arrStringTokens[] = str_replace(array_keys($arrReplace), array_values($arrReplace), $this->strRowTemplate);
                }
            } else {
                $arrStringTokens[] = $objInput->render();
            }
        }
        return implode(" ", $arrStringTokens);
    }

    protected function renderButtons() {
        $arrReplace = $arrBtnTokens = array();
        foreach ($this->arrButtonElements as $objBtn) {
            $arrBtnTokens[] = $objBtn->render();
        }
        $arrReplace['%inputs%'] = implode("", $arrBtnTokens);
        return str_replace(array_keys($arrReplace), array_values($arrReplace), $this->strButtonRowTemplate);
    }

}

?>
