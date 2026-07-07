<?php
session_start();
include 'db.php';

if(!isset($_SESSION['user_id'])){header("Location: login.php");exit;}
$uid=(int)$_SESSION['user_id'];
$action=isset($_GET['action']) ? $_GET['action'] : '';
$id=isset($_GET['id']) ? (int)$_GET['id'] : 0;

$q=mysqli_query($conn,"SELECT * FROM cart WHERE id=$id AND user_id=$uid LIMIT 1");
if(mysqli_num_rows($q)==1){
    $item=mysqli_fetch_assoc($q);
    $qty=(int)$item['qty'];

    if($action==='increase'){
        $qty++;
        mysqli_query($conn,"UPDATE cart SET qty=$qty WHERE id=$id AND user_id=$uid");
    } elseif($action==='decrease'){
        if($qty>1){
            $qty--;
            mysqli_query($conn,"UPDATE cart SET qty=$qty WHERE id=$id AND user_id=$uid");
        } else {
            mysqli_query($conn,"DELETE FROM cart WHERE id=$id AND user_id=$uid");
        }
    }
}

header("Location: cart.php");
exit;