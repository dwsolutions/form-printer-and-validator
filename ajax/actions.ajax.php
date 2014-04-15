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

include_once '../includes/config.php';
$action = $_REQUEST['action'];
$arrReturn['status'] = false;
$arrReturn['message']['title'] = "";
$arrReturn['message']['text'] = "";
try {
    switch ($action) {
        case "generate-password":
            $arrReturn['status'] = true;
            $arrReturn['pw'] = generateRandomString(8, "all");
            break;
        default: throw new Exception(NEM_DEFINIALT_AKCIO);
    }
} catch (Exception $ex) {
    $arrReturn['status'] = false;
    $arrReturn['message']['title'] = HIBA;
    $arrReturn['message']['text'] = $e->getMessage();
}
echo json_encode($arrReturn);
exit;
