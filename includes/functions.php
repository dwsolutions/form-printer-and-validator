<?php

function __autoload($classname) {
    $arrFolders = array('form', 'input', 'button', 'exception');
    $classname = strtolower($classname);
    foreach ($arrFolders as $folder) {
        if (is_file(dirname(__FILE__) . "/../classes/{$folder}/{$classname}.class.php")) {
            include(dirname(__FILE__) . "/../classes/{$folder}/{$classname}.class.php");
            break;
        }
    }
}

function startsWith($haystack, $needle) {
    return !strncmp($haystack, $needle, strlen($needle));
}

function endsWith($haystack, $needle) {
    $length = strlen($needle);
    if ($length == 0) {
        return true;
    }
    return substr($haystack, -$length) === $needle;
}

function generateRandomString($length = 8, $type = "all") {
    $strData = 'qwertzuiopasdfghjklyxcvbnmASDFGHJKLQWERTZUIOPYXCVBNM0123456789';
    $retStr = '';
    for ($i = 0; $i < $length; $i++) {
        switch ($type) {
            case "all":
                $retStr.=substr($strData, mt_rand(0, strlen($strData) - 1), 1);
                break;
            case "numeric":
                $retStr.=substr($strData, mt_rand(strlen($strData) - 10, strlen($strData) - 1), 1);
                break;
            case "uppercase":
                $retStr.=substr($strData, mt_rand(26, strlen($strData) - 11), 1);
                break;
            case "lowercase":
                $retStr.=substr($strData, mt_rand(0, 25), 1);
                break;
        }
    }
    return $retStr;
}

function createFilename($str) {
    $what = array(
        "/" => "_",
        "\\" => "_",
        "$" => "_",
        "?" => "_",
        "&" => "_",
        "." => "_",
        "'" => "",
        "\"" => "",
        "\xc3\x81" => "A",
        "\xc3\x89" => "E",
        "\xc3\x8d" => "I",
        "\xc3\x93" => "O",
        "\xc3\x96" => "O",
        "\xc5\x90" => "O",
        "\xc3\x9a" => "U",
        "\xc3\x9c" => "U",
        "\xc5\xb0" => "U",
        "\xc3\x94" => "O",
        "\xc3\x95" => "O",
        "\xc3\x9b" => "U",
        "\xc5\xa8" => "U",
        "\xc3\xa1" => "a",
        "\xc3\xa9" => "e",
        "\xc3\xad" => "i",
        "\xc3\xb3" => "o",
        "\xc3\xb6" => "o",
        "\xc5\x91" => "o",
        "\xc3\xba" => "u",
        "\xc3\xbc" => "u",
        "\xc5\xb1" => "u",
        "\xc3\xb4" => "o",
        "\xc3\xb5" => "o",
        "\xc3\xbb" => "u",
        "\xc5\xa9" => "u");
    $str = str_replace(array_keys($what), array_values($what), $str);
    $str = strtolower($str);
    return $str;
}

function utf8_to_latin2_hun($str) {
    return str_replace(
            array("\xc3\xb6", "\xc3\xbc", "\xc3\xb3", "\xc5\x91", "\xc3\xba", "\xc3\xa9", "\xc3\xa1", "\xc5\xb1", "\xc3\xad", "\xc3\x96", "\xc3\x9c", "\xc3\x93", "\xc5\x90", "\xc3\x9a", "\xc3\x89", "\xc3\x81", "\xc5\xb0", "\xc3\x8d"), array("\xf6", "\xfc", "\xf3", "\xf5", "\xfa", "\xe9", "\xe1", "\xfb", "\xed", "\xd6", "\xdc", "\xd3", "\xd5", "\xda", "\xc9", "\xc1", "\xdb", "\xcd"), $str);
}

/**
 * <p></p>
 * @param string $strTitle
 * @param string $strText
 * <p>BOOTSTRAP-ből a következők vannak:
 * <ul>
 * <li>[alert-]dismissable (Ha bezárható)</li>
 * <li>[alert-]danger</li>
 * <li>[alert-]success</li>
 * <li>[alert-]info</li>
 * <li>[alert-]warning</li>
 * </ul>
 * </p>
 * @param string $strType
 * @param bool $boolReturn
 * 
 * @return string OR void
 */
function printStatusBar($strTitle, $strText, $strType, $boolReturn = false) {
    $str = '<div class = "row"><div class = "col-lg-12"><div class="alert ' . $strType . '"><strong>' . $strTitle . '</strong>' . $strText . '</div></div></div>';
    if ($boolReturn) {
        return $str;
    }
    echo $str;
    //echo '<div class="statusbar ' . $strType . '">' . $strText . '</div>';
}

function printDump() {
    $args = func_get_args();
    foreach ((array) $args as $var) {
        echo '<pre>' . print_r($var, 1) . '</pre>';
    }
}

function createURL($str) {
    return str_replace(" ", "-", strtolower($str));
}

function arrayGetValuesBySpecifiedKey($arrData, $key) {
    $arrRet = array();
    foreach ($arrData as $v) {
        $arrRet[] = $v[$key];
    }
    return $arrRet;
}

function fetchSecureSerializedObject($strSerialized) {
    $urldecode = urldecode($strSerialized);
    $base64_decode = base64_decode($urldecode);
    return unserialize($base64_decode);
}

function secureSerializeObject($object) {
    return urlencode(base64_encode(serialize($object)));
}

function getUrl() {
    $s = empty($_SERVER["HTTPS"]) ? '' : ($_SERVER["HTTPS"] == "on") ? "s" : "";
    $sp = strtolower($_SERVER["SERVER_PROTOCOL"]);
    $protocol = substr($sp, 0, strpos($sp, "/")) . $s;
    $port = ($_SERVER["SERVER_PORT"] == "80") ? "" : (":" . $_SERVER["SERVER_PORT"]);
    return $protocol . "://" . $_SERVER['SERVER_NAME'] . $port . $_SERVER['REQUEST_URI'];
}

/**
 * Visszaadja a tömbből a megadott kulcsút, ha látezik a kulcs, különben a $default értékével tér vissza
 * @param string $key
 * <p>A keresendő kulcs</p>
 * @param array $array
 * <p>A tömb, amiben keresünk</p>
 * @param mixed $default
 * <p>A visszatérési érték, ha a kulcs nincs beállítva</p>
 * @return mixed
 */
function getExists($key, $array, $default = '') {
    return array_key_exists($key, $array) ? $array[$key] : $default;
}

?>
