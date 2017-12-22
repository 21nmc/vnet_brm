<?php

function msg_return($arr){
    $callback = $_GET['callback'];
    echo $callback.'('.json_encode($arr).')';
    exit;
}