<?php

if ($_SERVER["REQUEST_METHOD"] === "POST")
{
    //accepting inputs from form
    $name = trim($_POST["name"] ?? "");
    $email = trim($_POST["email"] ?? "");
    $password = trim($_POST["password"] ?? "");
    $confirmpassword = trim($_POST["confirmpassword"] ?? "");
    $course = trim($_POST["course"] ?? "");
    $year = trim($_POST["year"] ?? "");
    $gender = trim($_POST["gender"] ?? "");

    //validation logic 
    $errors = [];

    if (empty($name))
        {
            $errors[] = "name is required";
        }
    
    if (empty($email))
        {
            $errors[] = "email is required";
        }
    else if (!filter_var($email, FILTER_VALIDATE_EMAIL))
        {
            $errors[] = "enter valid email";
        }

    if (empty($password))
        {
            $errors[] = "password is required";
        }
    else if ($password != $confirmpassword)
        {
            $errors[] = "passwords dont match";
        }

    if (empty($year))
        {
            $errors[] = "year is required";
        }
    else if ($year > 2050 || $year < 2000 || !is_numeric($year))
        {
            $errors[] = "enter valid year";
        }

    if (empty($errors))
        {
            echo "no errors";

            $record = [
                "name" => $name,
                "email" => $email,
                "password" => $password,
                "course" => $course,
                "year" => $year,
                "gender" => $gender
            ];

            $file = "practical7.json"; //just a string that will be used to look for file named same
            $readfile = file_exists($file) ? file_get_contents($file) : "[]";
            $data = json_decode($readfile, true) ?? [];
            //json_decode converted json data in string that is read to php array
            //true argument converts this array to associative array
            //if expression left to ?? evaluates to null, returns an empty array
            $data[] = $record; //appending the input array were working on, to the json data
            $putbacktojson = json_decode($data, JSON_PRETTY_PRINT);
            file_put_contents($file, $putbacktojson);

            echo "record saved";
        }
    else
        {
            forEach($errors as $i)
            {
                echo $i;
            }
        }

}
else
    {
        echo "invalid access method";
    }


