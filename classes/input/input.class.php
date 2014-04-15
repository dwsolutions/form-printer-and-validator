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
abstract class Input {

    protected $arrData;
    protected $strName;
    private $strMandatoryFieldsLabelPostfix = '<span> *</span>';
    protected $objForm;
    protected $strTemplate;
    protected $strRender;

    public function __construct($arrData, $objForm) {
        $this->arrData = $arrData;
        if ((array_key_exists('cloneable', $arrData) && $arrData['cloneable']) || (array_key_exists('is_array', $arrData) && $arrData['is_array'])) {
            if (strcmp(substr($arrData["name"], -2), "[]") != 0) {
                $arrData["name"].="[]";
            }
        }
        $this->strName = $this->arrData["name"];
        $this->objForm = $objForm;
        $this->init();
    }

    public function isRenderable() {
        if (!array_key_exists("renderable", $this->arrData)) {
            return true;
        }
        return is_bool($this->arrData['renderable']) ? $this->arrData['renderable'] : true;
    }

    public function getData($key = false) {
        $return = false;
        if ($key !== false) {
            if (array_key_exists($key, $this->arrData)) {
                return $this->arrData[$key];
            }
        } else {
            $return = $this->arrData;
        }
        return $return;
    }

    public function getType() {
        return strtolower(get_class($this));
    }

    public function getInputLabel($boolOnlyLabel = false) {
        $label = array_key_exists('label', $this->arrData) ? $this->arrData['label'] : $this->strName;
        if ($boolOnlyLabel) {
            return $label;
        }
        $validation = $this->getValidation();
        $label .= array_key_exists("mandatory", $validation) && $validation['mandatory'] && (!array_key_exists('hideMandatoryLabel', $this->arrData) || $this->arrData['hideMandatoryLabel'] == false) ? $this->strMandatoryFieldsLabelPostfix : '';
        return $label;
    }

    public function isCloneable() {
        if (array_key_exists("cloneable", $this->arrData)) {
            return $this->arrData['cloneable'];
        }
        return false;
    }

    public function isArray() {
        if (array_key_exists("is_array", $this->arrData)) {
            return $this->arrData['is_array'];
        }
        return false;
    }

    public function getName() {
        return $this->strName;
    }

    public function getValidation($key = false) {
        if ($key !== false) {
            if (array_key_exists("validate", $this->arrData)) {
                if (array_key_exists($key, $this->arrData['validate'])) {
                    return $this->arrData['validate'][$key];
                }
            }
        }
        return array_key_exists("validate", $this->arrData) ? $this->arrData['validate'] : array();
    }

    public function addValidation($type, $validateSettings) {
        $arrAvailableValidation = $this->getValidation();
        if (empty($arrAvailableValidation)) {
            $this->arrData['validate'] = array();
        }

        if (!array_key_exists($type, $arrAvailableValidation)) {
            $this->arrData['validate'][$type] = $validateSettings;
        }
    }

    abstract function render();

    abstract function init();

    abstract function getSaveData($arrMethodData);
}

?>