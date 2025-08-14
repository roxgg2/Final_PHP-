<?php
function flashMessages() {
    if (isset($_SESSION['error'])) {
        echo '<p style="color:red">'.htmlentities($_SESSION['error'])."</p>\n";
        unset($_SESSION['error']);
    }
    if (isset($_SESSION['success'])) {
        echo '<p style="color:green">'.htmlentities($_SESSION['success'])."</p>\n";
        unset($_SESSION['success']);
    }
}

function validateProfile() {
    foreach (['first_name', 'last_name', 'email', 'headline', 'summary'] as $field) {
        if (strlen(trim($_POST[$field])) < 1) {
            return "All fields are required";
        }
    }
    if (strpos($_POST['email'], '@') === false) {
        return "Email address must contain @";
    }
    return true;
}

function validatePosEdu() {
    for ($i = 1; $i <= 9; $i++) {
        if (isset($_POST['year'.$i]) || isset($_POST['desc'.$i])) {
            if (strlen($_POST['year'.$i]) == 0 || strlen($_POST['desc'.$i]) == 0) {
                return "All fields are required";
            }
            if (!is_numeric($_POST['year'.$i])) {
                return "Position year must be numeric";
            }
        }
        if (isset($_POST['edu_year'.$i]) || isset($_POST['edu_school'.$i])) {
            if (strlen($_POST['edu_year'.$i]) == 0 || strlen($_POST['edu_school'.$i]) == 0) {
                return "All fields are required";
            }
            if (!is_numeric($_POST['edu_year'.$i])) {
                return "Education year must be numeric";
            }
        }
    }
    return true;
}
?>