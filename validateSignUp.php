<?php
$email = $_POST['email'];
$password = $_POST['psw'];
$repeat = $_POST['psw-repeat'];


// Validate password strength
$uppercase = preg_match('@[A-Z]@', $password);
$lowercase = preg_match('@[a-z]@', $password);
$number    = preg_match('@[0-9]@', $password);
$specialChars = preg_match('@[^\w]@', $password);
if (strlen($email)<1 )
{
    echo "Email length too short";

    exit();

}

elseif (!strpos($email, '@'))
{
    echo "email invalid";
    exit();
}

else if(!$uppercase || !$lowercase ||!$specialChars  || !$number  || strlen($password) < 9)
{
    echo 'Password should be at least 9 characters in length and should include at least one upper case letter, one number, and one special character.';

}
elseif ($password !== $repeat)
{
    echo "passwords do not match";
    exit();
}
$hash = password_hash($password, PASSWORD_DEFAULT);
echo $password;
?>