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
abstract class Form {

    protected $arrInputDatas = array();
    protected $arrInputElements = array();
    protected $arrSourceData = array();
    protected $arrButtonDatas = array();
    protected $arrButtonElements = array();
    protected $arrSettings = array();
    protected $strFormType;

    public function __construct($strFormType, $arrSettings = array()) {
        $this->strFormType = $strFormType;
        $this->arrSettings = $arrSettings;
        $this->addInput(array("type" => "hidden", "name" => "formtype", "default" => $this->strFormType));
    }

    public function init() {
        $this->initInputs();
        $this->initButtons();
        $_SESSION[SESSIONPREFIX . "form_objects"][$this->strFormType] = secureSerializeObject($this);
    }

    public function addInput($arrInputData) {
        if ((array_key_exists("cloneable", $arrInputData) && $arrInputData["cloneable"]) || (array_key_exists("is_array", $arrInputData) && $arrInputData["is_array"])) {
            $this->arrInputDatas[$arrInputData['name']][] = $arrInputData;
        } else {
            $this->arrInputDatas[$arrInputData['name']] = $arrInputData;
        }
    }

    public function addInputs($arrInputDatas) {
        foreach ((array) $arrInputDatas as $arrInputData) {
            $this->addInput($arrInputData);
        }
    }

    public function addInputObject($objInput) {
        if ($objInput->isCloneable() || $objInput->isArray()) {
            $this->arrInputElements[$objInput->getName()][] = $objInput;
        } else {
            $this->arrInputElements[$objInput->getName()] = $objInput;
        }
    }

    public function getInputs() {
        return $this->arrInputElements;
    }

    public function getInputNames() {
        return array_keys($this->arrInputDatas);
    }

    public function getInput($strName) {
        return getExists($strName, $this->arrInputElements, false);
    }

    public function resetInputs() {
        $this->arrInputElements = array();
        $this->arrButtonElements = array();
    }

    public function removeInput($name) {
        unset($this->arrInputDatas[$name]);
    }

    public function removeInputObject($name) {
        if (is_array($this->arrInputElements[$name])) {
            unset($this->arrInputElements[$name][count($this->arrInputElements[$name]) - 1]);
        } else {
            unset($this->arrInputElements[$name]);
        }
    }

    public function addButton($arrButtonData) {
        $this->arrButtonDatas[] = $arrButtonData;
    }

    public function addButtons($arrButtonDatas) {
        foreach ((array) $arrButtonDatas as $arrButtonData) {
            $this->addButton($arrButtonData);
        }
    }

    private function initInputs() {
        foreach ($this->arrInputDatas as $input) {
            if (isset($input['type'])) {
                $objName = ucfirst($input['type']);
                unset($input['type']);
                $objInput = new $objName($input, $this);
                if ($objInput->isCloneable() || $objInput->isArray()) {
                    $this->arrInputElements[$objInput->getName()][] = $objInput;
                } else {
                    $this->arrInputElements[$objInput->getName()] = $objInput;
                }
            } else {
                foreach ($input as $inp) {
                    $objName = ucfirst($inp['type']);
                    unset($inp['type']);
                    $objInput = new $objName($inp, $this);
                    if ($objInput->isCloneable() || $objInput->isArray()) {
                        $this->arrInputElements[$objInput->getName()][] = $objInput;
                    } else {
                        $this->arrInputElements[$objInput->getName()] = $objInput;
                    }
                }
            }
        }
    }

    private function initButtons() {
        if (empty($this->arrButtonElements)) {
            foreach ($this->arrButtonDatas as $button) {
                $objName = ucfirst($button['type']);
                unset($button['type']);
                $objBtn = new $objName($button);
                $this->arrButtonElements[] = $objBtn;
            }
        }
    }

    public function setSourceData($arrSourceData) {
        $this->arrSourceData = $arrSourceData;
    }

    public function getSourceData() {
        return $this->arrSourceData;
    }

    public function getSaveData($arrMethodData) {
        $arrSaveData = array();
        foreach ($this->arrInputElements as $objInput) {
            $arrSaveData[$objInput->getName()] = $objInput->getSaveData($arrMethodData);
        }
        return $arrSaveData;
    }

    public function render() {
        $this->init();
    }

    public function validate($arrMethodData) {
        $arrReturn = array();
        foreach ($this->arrInputElements as $key => $input) {
            if (!is_array($input)) {
                $arrReturn[$input->getName()] = array();
                $inputSaveData = $input->getSaveData($arrMethodData);
                foreach ($input->getValidation() as $validationKey => $validationData) {
                    switch ($validationKey) {
                        case "mandatory":
                            if ($input->getType() != "file") {
                                if (empty($inputSaveData) && $validationData) {
                                    $arrReturn[$input->getName()][] = A_MEZO_KITOLTESE_KOTELEZO;
                                }
                            } else {
                                if (is_array($inputSaveData)) {
                                    if ((empty($inputSaveData['name']) || empty($inputSaveData['tmp_name'])) && $validationData) {
                                        $arrReturn[$input->getName()][] = A_MEZO_KITOLTESE_KOTELEZO;
                                    }
                                }
                            }
                            break;
                        case "email":
                            $strRegexp = '/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})$/';
                            if (!preg_match($strRegexp, $inputSaveData)) {
                                $arrReturn[$input->getName()][] = A_MEZO_FORMATUMA_NEM_MEGFELELO;
                            }
                            break;
                        case "phone":
                            $strRegexp = '/^[+]?([0-9]{1,2})[-]?([0-9]{1,2})[-]?([0-9]{6,8})$/';
                            if (!preg_match($strRegexp, $inputSaveData)) {
                                $arrReturn[$input->getName()][] = str_replace("%FORMAT%", ($validationData === true ? "" : "Enabled format: " . $validationData['format']), A_MEZO_FORMATUMA_NEM_MEGFELELO_TELEFON);
                            }
                            break;
                        case "unique":
                            $strTable = $validationData['table'];
                            $strField = (array_key_exists("fieldname", $validationData) && !empty($validationData['fieldname'])) ? $validationData['fieldname'] : $input->getName();
                            $objDb = Database::getSingleton();
                            $arrResult = $objDb->select("*", $strTable, $strField . "='" . $inputSaveData . "'");
                            if ($arrResult) {
                                $arrReplace = array("%FIELDVALUE%" => $inputSaveData);
                                $arrReturn[$input->getName()][] = str_replace(array_keys($arrReplace), array_values($arrReplace), A_MEZO_ERTEKE_FOGLALT);
                            }
                            break;
                        case "extension":
                            if (getExists("mandatory", $input->getValidation(), false) && !empty($inputSaveData['name'])) {
                                foreach ((array) $inputSaveData['name'] as $name) {
                                    $ext = Filehandler::getExtension($name);
                                    if (!in_array($ext, $validationData)) {
                                        $arrReturn[$input->getName()][] = NEM_TAMOGATOTT_FORMATUM;
                                        break;
                                    }
                                }
                            }
                            break;
                        case "same":
                            $sameTo = $validationData;
                            //$index = getIndexByKey(array_flip(arrayGetValuesBySpecifiedKey($this->arrInputDatas, "name")), $sameTo);
                            if (!array_key_exists($sameTo, $this->arrInputElements)) {
                                throw new Exception(str_replace("%FIELDNAME%", $sameTo, MEZO_NEM_TALALHATO));
                            }
                            $sameToValue = $this->arrInputElements[$sameTo]->getSaveData($arrMethodData);
                            if ($inputSaveData != $sameToValue) {
                                $arrReturn[$input->getName()][] = str_replace("%FIELDNAME%", $this->arrInputElements[$sameTo]->getInputLabel(true), A_MEZO_ERTEKE_NEM_EGYEZIK);
                            }
                            break;
                        case "regexp":
                            if (!preg_match($validationData['expression'], $inputSaveData)) {
                                $arrReturn[$input->getName()][] = str_replace("%FORMAT%", ($validationData['format'] === true ? "" : $validationData['format']), A_MEZO_FORMATUMA_NEM_MEGFELELO_TELEFON);
                            }
                            break;
                        case "image":
                            if ($input->getType() == "file" && !empty($inputSaveData)) {
                                $valid_types = array(IMAGETYPE_GIF, IMAGETYPE_JPEG, IMAGETYPE_PNG, IMAGETYPE_BMP);
                                foreach ((array) $inputSaveData['tmp_name'] as $temp) {
                                    if (is_uploaded_file($temp)) {
                                        list($w, $h, $t) = getimagesize($temp);
                                        if (!in_array($t, $valid_types) || ($w <= 1 && $h <= 1)) {
                                            $arrReturn[$input->getName()][] = A_MEZO_NEM_KEP;
                                            break;
                                        }
                                    }
                                }
                            }
                            break;
                        case "max_filenum":
                            if ($input->getType() == "file" && !empty($inputSaveData)) {
                                if (count((array) $inputSaveData['tmp_name']) > $validationData) {
                                    $arrReturn[$input->getName()][] = str_replace('%NUM%', $validationData, TUL_SOK_FAJL);
                                }
                            }
                            break;
                        case "min_filenum":
                            if ($input->getType() == "file" && !empty($inputSaveData)) {
                                if (count((array) $inputSaveData['tmp_name']) < $validationData) {
                                    $arrReturn[$input->getName()][] = str_replace('%NUM%', $validationData, TUL_KEVES_FAJL);
                                }
                            }
                            break;
                        case "min_imagesize":
                            if ($input->getType() == "file" && !empty($inputSaveData)) {
                                $valid_types = array(IMAGETYPE_GIF, IMAGETYPE_JPEG, IMAGETYPE_PNG, IMAGETYPE_BMP);
                                foreach ((array) $inputSaveData['tmp_name'] as $temp) {
                                    if (is_uploaded_file($temp)) {
                                        list($w, $h, $t) = getimagesize($temp);
                                        if (($w < $validationData['width'] && $h < $validationData['height']) || ($h < $validationData['width'] && $w < $validationData['height'])) {
                                            $arrReturn[$input->getName()][] = str_replace('%IMAGESIZE%', "{$validationData['width']}x{$validationData['height']} or {$validationData['height']}x{$validationData['width']}", TUL_KICSI_KEP);
                                        }
                                    }
                                }
                            }
                            break;
                        case "max_imagesize":
                            if ($input->getType() == "file" && !empty($inputSaveData)) {
                                $valid_types = array(IMAGETYPE_GIF, IMAGETYPE_JPEG, IMAGETYPE_PNG, IMAGETYPE_BMP);
                                foreach ((array) $inputSaveData['tmp_name'] as $temp) {
                                    if (is_uploaded_file($temp)) {
                                        list($w, $h, $t) = getimagesize($temp);
                                        if (($w > $validationData['width'] && $h > $validationData['height']) || ($h > $validationData['width'] && $w > $validationData['height'])) {
                                            $arrReturn[$input->getName()][] = str_replace('%IMAGESIZE%', "{$validationData['width']}x{$validationData['height']} or {$validationData['height']}x{$validationData['width']}", TUL_NAGY_KEP);
                                        }
                                    }
                                }
                            }
                            break;
                        case "max_filesize":
                            if ($input->getType() == "file" && !empty($inputSaveData)) {
                                foreach ((array) $inputSaveData['size'] as $temp) {
                                    if ($temp > $validationData) {
                                        $arrReturn[$input->getName()][] = str_replace('%FILESIZE%', $validationData, TUL_NAGY_FAJL);
                                        break;
                                    }
                                }
                            }
                            break;
                        case "min_filesize":
                            if ($input->getType() == "file" && !empty($inputSaveData)) {
                                foreach ((array) $inputSaveData['size'] as $temp) {
                                    if ($temp < $validationData) {
                                        $arrReturn[$input->getName()][] = str_replace('%FILESIZE%', $validationData, TUL_KICSI_FAJL);
                                        break;
                                    }
                                }
                            }
                            break;
                        case "minlength":
                            $minWidth = $validationData;
                            if (strlen($inputSaveData) < intval($minWidth)) {
                                $arrReturn[$input->getName()][] = str_replace('%NUMVALUE%', $minWidth, A_MEZO_HOSSZA_ROVID);
                            }
                            break;
                        case "maxlength":
                            $maxWidth = $validationData;
                            if (strlen($inputSaveData) > intval($maxWidth)) {
                                $arrReturn[$input->getName()][] = str_replace('%NUMVALUE%', $maxWidth, A_MEZO_HOSSZA_HOSSZU);
                            }

                            break;
                        case "number":
                            $strRegexp = '/^[-+]?[0-9]*\.?[0-9]+([eE][-+]?[0-9]+)?$/'; //'/^[-+]?[0-9]*\.?[0-9]*$/';
                            if (!preg_match($strRegexp, $inputSaveData)) {
                                $arrReturn[$input->getName()][] = A_MEZO_FORMATUMA_NEM_SZAM;
                            }
                            break;

                        case "datebigger":
                            if (!empty($inputSaveData)) {
                                switch ($validationData['relative_to']) {
                                    case 'input':
                                        $fieldname = $validationData['relative_to_value'];
                                        if (!array_key_exists($fieldname, $this->arrInputElements)) {
                                            throw new Exception(str_replace("%FIELDNAME%", $fieldname, MEZO_NEM_TALALHATO));
                                        }
                                        $date = $this->arrInputElements[$fieldname]->getSaveData($arrMethodData);
                                        if (array_key_exists("enabled_equal", $validationData) && $validationData['enabled_equal']) {
                                            if (strtotime($inputSaveData) < strtotime($date)) {
                                                $arrReturn[$input->getName()][] = str_replace("%FIELDNAME%", $this->arrInputElements[$fieldname]->getInputLabel(true), A_MEZO_ERTEKE_KISEBB_MEZO);
                                            }
                                        } else {
                                            if (strtotime($inputSaveData) <= strtotime($date)) {
                                                $arrReturn[$input->getName()][] = str_replace("%FIELDNAME%", $this->arrInputElements[$fieldname]->getInputLabel(true), A_MEZO_ERTEKE_KISEBB_MEZO);
                                            }
                                        }
                                        break;
                                    case 'value':
                                        $fieldname = $validationData['relative_to_value'];
                                        $date = array_key_exists("relative_to_value", $validationData) ? strtotime($validationData['relative_to_value']) : strtotime(date("Y-m-d"));
                                        if (isset($validationData['enabled_equal']) && $validationData['enabled_equal']) {
                                            if (strtotime($inputSaveData) < $date) {
                                                $arrReturn[$input->getName()][] = str_replace("%VALUE%", date("Y-m-d", $date), A_MEZO_ERTEKE_KISEBB_EGYENLO_ERTEK);
                                            }
                                        } else {
                                            if (strtotime($inputSaveData) <= $date) {
                                                $arrReturn[$input->getName()][] = str_replace("%VALUE%", date("Y-m-d", $date), A_MEZO_ERTEKE_KISEBB_ERTEK);
                                            }
                                        }
                                        break;
                                }
                            }

                            break;
                        case "datesmaller":
                            if (!empty($inputSaveData)) {
                                switch ($validationData['relative_to']) {
                                    case 'input':
                                        $fieldname = $validationData['relative_to_value'];
                                        if (!array_key_exists($fieldname, $this->arrInputElements)) {
                                            throw new Exception(str_replace("%FIELDNAME%", $fieldname, MEZO_NEM_TALALHATO));
                                        }
                                        $date = $this->arrInputElements[$fieldname]->getSaveData($arrMethodData);
                                        if (array_key_exists("enabled_equal", $validationData) && $validationData['enabled_equal']) {
                                            if (strtotime($inputSaveData) > strtotime($date)) {
                                                $arrReturn[$input->getName()][] = str_replace("%FIELDNAME%", $this->arrInputElements[$fieldname]->getInputLabel(true), A_MEZO_ERTEKE_NAGYOBB_MEZO);
                                            }
                                        } else {
                                            if (strtotime($inputSaveData) >= strtotime($date)) {
                                                $arrReturn[$input->getName()][] = str_replace("%FIELDNAME%", $this->arrInputElements[$fieldname]->getInputLabel(true), A_MEZO_ERTEKE_NAGYOBB_MEZO);
                                            }
                                        }
                                        break;
                                    case 'value':
                                        $fieldname = $validationData['relative_to_value'];
                                        $date = array_key_exists("relative_to_value", $validationData) ? strtotime($validationData['relative_to_value']) : strtotime(date("Y-m-d"));
                                        if (isset($validationData['enabled_equal']) && $validationData['enabled_equal']) {
                                            if (strtotime($inputSaveData) > $date) {
                                                $arrReturn[$input->getName()][] = str_replace("%VALUE%", date("Y-m-d", $date), A_MEZO_ERTEKE_NAGYOBB_EGYENLO_ERTEK);
                                            }
                                        } else {
                                            if (strtotime($inputSaveData) >= $date) {
                                                $arrReturn[$input->getName()][] = str_replace("%VALUE%", date("Y-m-d", $date), A_MEZO_ERTEKE_NAGYOBB_ERTEK);
                                            }
                                        }
                                        break;
                                }
                            }
                            break;
                        default:
                            break;
                    }
                }
            }
        }
        return $arrReturn;
    }

}

?>