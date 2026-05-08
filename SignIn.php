<?php
session_start();
include 'database.php';
    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = $_POST['password'];

    $sql = "SELECT * FROM signin WHERE email='$email' AND password='$password'";

    $result = mysqli_query($conn, $sql);
    if(mysqli_num_rows($result) > 0){

    $row= mysqli_fetch_assoc($result);

     //vertify password
    if($password == $row['password'] && $email == $row['email']){
        // $_SESSION['user__name']=$name;
        $_SESSION['user_email']=$email;
        // $_SESSION['user_password']=$password;
        header("Location: Front_End.php");
        exit();
    }else{
        echo "<script>alert('Invalid email or password');</script>";}
    }

?>