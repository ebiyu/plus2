<?php

$pi = 3.141592653589793238462;
date_default_timezone_set('Etc/GMT-9');

$db;

function startDB(){
    global $db;
    $db = mysqli_connect('localhost', 'root', 'InoHead@IN3','plus2');
    mysqli_query($db,"SET NAMES utf8mb4");
    
    return $db;
}

function getDataFromDB($query){
    global $db;
    $result = mysqli_query($db,$query);
    echo mysqli_error($db);
    $data_arr = array();
    while($row = mysqli_fetch_assoc($result)){
        array_push($data_arr, $row);
    }
        
    return $data_arr;
}

function addScore($user_name, $val, $score, $cnt){
    global $db;
    $is_infinity = 0;
    if($score == "Infinity"){
        $score = 0;$is_infinity = 1;
    }

    $q = "INSERT INTO records (user_name,cnt,val,score,is_infinity,creation_time) VALUES ('{$user_name}',{$cnt},{$val},{$score},{$is_infinity},'" . date("Y-m-d H:i:s") . "');";
    
    $result = mysqli_query($db,$q);
    echo mysqli_error($db);
}

function showScore(){
    global $pi;
    $q = "SELECT * FROM records WHERE is_infinity = 0;";
    $arr = getDataFromDB($q);
    $q = "SELECT * FROM records WHERE is_infinity = 1;";
    $arr2 = getDataFromDB($q);

    $srt=array();
    
    foreach($arr as $key => $val){
        $srt[$key] = $val['score'];
    }
    
    array_multisort($srt, SORT_DESC, $arr);
    
    echo "<table class='ranking'>";
    echo "<tr class='legend'><td>順位</td><td>名前</td><td>スコア</td><td>回数</td><td>値</td><td>差</td><td>日時</td></tr>";
    
    foreach($arr2 as $sc){
        echo "<tr name='infinity'><td class='rank'>0</td><td class='usr_name'>{$sc['user_name']}</td><td>Infinity</td><td>{$sc['cnt']}</td><td class='val_tbl'>{$sc['val']}</td><td>" . sprintf('%e', $pi - $sc['val']) . "</td><td>{$sc['creation_time']}</td></tr>";
    }
    
    $i = 0;
    foreach($arr as $sc){
        $i++;
        echo "<tr><td class='rank'>{$i}</td><td class='usr_name'>{$sc['user_name']}</td><td>{$sc['score']}</td><td>{$sc['cnt']}</td><td class='val_tbl'>{$sc['val']}</td><td>" . sprintf('%e', $pi - $sc['val']) . "</td><td>{$sc['creation_time']}</td></tr>";
        if($i >= 10) break;
    }
    echo "</table>";

}
    
?>
