<?php

require("oopGenPage.php");


$bodyContent=<<<BODY
<div class="newPasswordForm">
  <div class="container">
    <?php
    #This is to ensure no one can mess with the tokens in the URL
    $selector = $_GET["selector"];
    $validator = $_GET["validator"];

    if (empty($selector) || empty($validator)) {
        echo "Request cannot be Validated.";
    } else {
        if (ctype_xdigit($selector) !== false && ctype_xdigit($validator) !== false) {
            ?>
            <form action="resetRequest.php" method="post">
                <input type="hidden" name="selector" value="<?php echo $selector?>">
                <input type="hidden" name="validator" value="<?php echo $validator?>">
                <input type="password" name="newPassword" placeholder="Enter New Password">
                <input type="password" name="repeatNewPassword" placeholder="Confirm New Password">
                <button type="submit" name="submitNewPassword">Reset Password</button>
            </form>
            <?php

            ?>
        }
    }

  </div>
</div>
BODY;

$page=new HTMLPage($title,$cssPath);
$page->setBodyContent($bodyContent);
$page->render();

?>
