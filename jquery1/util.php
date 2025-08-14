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
?>