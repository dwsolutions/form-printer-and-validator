<?php
include_once "includes/config.php";

try {
    if (isset($_REQUEST['formtype'])) {
        if (getExists(SESSIONPREFIX . 'form_objects', $_SESSION, false)) {
            $formObject = getExists($_REQUEST['formtype'], $_SESSION[SESSIONPREFIX . 'form_objects'], false);
            if (!$formObject) {
                throw new FormValidationException(NEM_DEFINIALT_URLAP);
            }

            $objTempForm = fetchSecureSerializedObject($formObject);
            if ($objTempForm) {
                $arrValidationErrors = array();
                $arrValidation = $objTempForm->validate(array_merge($_FILES, $_POST, $_GET));
                foreach ($arrValidation as $validationKey => $validationError) {
                    if (!empty($validationError)) {
                        $arrValidationErrors[$validationKey] = implode("<br>", $validationError);
                    }
                }
                if (!empty($arrValidationErrors)) {
                    $arrValidationMessages = array();
                    foreach ($arrValidationErrors as $validationInputName => $validationInputMessage) {
                        $arrValidationMessages[] = $objTempForm->getInput($validationInputName)->getInputLabel() . ":" . $validationInputMessage;
                    }
                    throw new FormValidationException(implode("<br>", $arrValidationMessages));
                }
            }
        }
    }
} catch (FormValidationException $fvex) {
    $exception = $fvex->getMessage();
} catch (Exception $ex) {
    
}

// Sample article form
$objArticleExampleForm = new Listform("example-article");
$objArticleExampleForm->addInputs(
        array(
            array(
                "type" => "text",
                "name" => "title",
                "label" => "Title",
                "classname" => "example",
                "id" => "title-field",
                "validate" => array(
                    "mandatory" => true
                )
            ),
            array(
                "type" => "ckeditor",
                "name" => "content",
                "label" => "Content",
                "validate" => array(
                    "mandatory" => true
                )
            ),
            array(
                "type" => "checkbox",
                "name" => "is_active",
                "label" => "Active?",
                "default" => 1
            ),
            array(
                "type" => "select",
                "name" => "group_to_see",
                "label" => "Group to see",
                "options" => array(
                    "all" => "Everybody",
                    "logged_in" => "Only logged in users"
                ),
                "validate" => array(
                    "mandatory" => true
                )
            ),
            array(
                "type" => "datepicker",
                "name" => "date_to_available",
                "label" => "Finish date",
                "validate" => array(
                    "mandatory" => true,
                    "date" => true,
                    "datesmaller" => array(
                        "relative_to" => "value",
                        "relative_to_value" => date("Y-m-d"),
                        "enabled_equal" => true
                    )
                )
            )
        )
);

$arrArticleSampleData = array(
    "title" => "Example article",
    "content" => "Lorem ipsum..."
);
$objArticleExampleForm->setSourceData($arrArticleSampleData);

$objArticleExampleForm->addButton(array(
    "type" => "submit",
    "label" => "Save article",
    "classname" => "btn btn-primary"
));


//  Sample user datas form
$objUserForm = new Listform("user_datas");
$objUserForm->addInputs(
        array(
            array(
                "type" => "text",
                "name" => "name",
                "label" => "Name",
                "validate" => array(
                    "mandatory" => true
                )
            ),
            array(
                "type" => "text",
                "name" => "email",
                "label" => "E-mail",
                "validate" => array(
                    "mandatory" => true,
                    "email" => true
                )
            ),
            array(
                "type" => "password",
                "name" => "password",
                "label" => "Password",
                "generator" => true,
                "fill-field" => array("password", "password2"),
                "validate" => array(
                    "mandatory" => false,
                    "same" => "password2"
                )
            ),
            array(
                "type" => "password",
                "name" => "password2",
                "label" => "Password again",
                "validate" => array(
                    "mandatory" => false,
                    "same" => "password"
                )
            ),
            array(
                "type" => "text",
                "name" => "phone",
                "label" => "Phone number",
                "validate" => array(
                    "mandatory" => false,
                    "phone" => true
                )
            ),
            array(
                "type" => "textarea",
                "name" => "about",
                "label" => "About you"
            ),
            array(
                "type" => "multiselect",
                "name" => "interests",
                "label" => "Your interests",
                "options" => array(
                    "books" => "Books",
                    "carss" => "Cars",
                    "movies" => "Movies",
                    "music" => "Music"
                )
            ),
            array(
                "type" => "file",
                "name" => "profile_image",
                "label" => "Profile image",
                "multiple" => false,
                "validate" => array(
                    "mandatory" => false,
                    "extension" => array("jpg", "jpeg", "png", "gif"),
                    "image" => true,
                    "min_filenum" => 1,
                    "max_filenum" => 1,
                    "min_imagesize" => array("width" => 300, "height" => 500),
                    "max_imagesize" => array("width" => 3000, "height" => 5000),
                    "max_filesize" => 3145728 // 3MB
                )
            )
        )
);

if (isset($_POST)) {
    $objUserForm->setSourceData($_POST);
}
$objUserForm->addButton(array(
    "type" => "submit",
    "label" => "Save user",
    "classname" => "btn btn-primary"
));
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <title>Form render &amp; validator</title>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="description" content="">
        <meta name="author" content="Attila">
        <!--  JS  -->
        <script src="js/jquery-1.10.2.js" type="text/javascript"></script>
        <script src="js/bootstrap.min.js" type="text/javascript"></script>
        <script src="js/jquery-ui-1.10.3.custom.min.js" type="text/javascript"></script>
        <script src="ckeditor/ckeditor.js" type="text/javascript"></script>
        <script src="js/core.js"></script>

        <!--  STYLESHEETS  -->
        <link rel="stylesheet" href="css/bootstrap.min.css" />
        <link rel="stylesheet" href="font-awesome/css/font-awesome.min.css" />
        <link rel="stylesheet" href="css/ui-lightness/jquery-ui-1.10.3.custom.min.css" />
        <link rel="stylesheet" href="css/core.css" />
    </head>
    <body>
        <div id="wrapper">
            <div id="page-wrapper">
                <div class="row">
                    <div class="col-lg-12">
                        <?php
                        if (isset($exception) && $exception !== false) {
                            printStatusBar($exception, "", "alert-danger");
                        }
                        ?>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-12">
                        <h3>User datas</h3>
                        <?php
                        $objUserForm->render();
                        ?>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-6">
                        <h3>Article</h3>
                        <?php
                        $objArticleExampleForm->render();
                        ?>
                    </div>
                </div>
            </div><!-- /#page-wrapper -->
        </div><!-- /#wrapper -->
    </body>
</html>