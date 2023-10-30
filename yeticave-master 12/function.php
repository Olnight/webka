<?php
const COUNT_MINUTE = 60;
const COUNT =  COUNT_MINUTE * 60 *24;
const MINUTE = 1;

function formNum(int $price):string {
    return number_format($price, thousands_separator: ' '). ' ₽';
}

function get_dt_range(string $date):array{
    date_default_timezone_set('Asia/Yekaterinburg');
    
    
    $minutes = floor(((strtotime($date) + (COUNT))- time())/COUNT_MINUTE) ;
    $hours = str_pad(floor($minutes / COUNT_MINUTE), 2, "0", STR_PAD_LEFT);
    $minutes =  str_pad(floor($minutes - ($hours * COUNT_MINUTE) + MINUTE), 2, "0", STR_PAD_LEFT);
    return [$hours, $minutes];

}

function lot_list(mysqli $con):array{
    $sql = "SELECT Lot.id,  Lot.name as name_lot, picture, start_price, date_end, Category.name FROM Lot
    INNER JOIN Category ON Lot.category_id = Category.id
    WHERE date_end >= CURRENT_DATE ORDER BY date_reg DESC";

    return mysqli_fetch_all(mysqli_query($con, $sql), MYSQLI_ASSOC);
}

function category_list(mysqli $con):array{
    $sql = "SELECT id, name, sym_code FROM Category";

    return mysqli_fetch_all(mysqli_query($con, $sql), MYSQLI_ASSOC);
}

function lot_detail(mysqli $con, int $id_lot):array|null{
    $sql = "SELECT Lot.name as name_lot, picture, start_price, date_end, description, Category.name FROM Lot
    INNER JOIN Category ON Lot.category_id = Category.id 
    WHERE Lot.id = ? ";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, 'i', $id_lot);
    mysqli_stmt_execute($stmt);
    $res = mysqli_stmt_get_result($stmt);
    $rows = mysqli_fetch_assoc($res);
    if(mysqli_num_rows($res) === 0){ 
        http_response_code(404);
    }
    return $rows;
}


//function empty_field($fields):array{
//    $errors =[];
//    foreach ($fields as $field) {
//        if (empty($_POST[$field])) {
//            $errors[$field] = 'Поле не заполнено';
//        }
//    }
//    foreach($fields as $key => $value){
//        if($key == 'date_end'){
//            if(is_date_valid($value)){
//                $errors[$key] = 'Введите корректную дату';
//            }
//        }
//    }
//    return $errors;
//}

// function get_last_lot_id(mysqli $con){
//     $sql = "SELECT id FROM Lot ORDER BY id DESC;";
//     $res = mysqli_query($con, $sql);
    
//     return mysqli_fetch_row($res)[0];
// }

function add_lot(string $lot_name, int $category, string $message, string $picture, int $lotRate, int $lotStep, string $endDate, mysqli $con):int{
    
    $authorId = 1;

    $sql = "INSERT INTO Lot(name, description, picture, start_price, date_end, rate_step, creator_id, category_id)
            VALUES(?,?,?,?,?,?,?,?);";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, 'sssisiii', $lot_name, $message, $picture, $lotRate, $endDate, $lotStep, $authorId, $category);
    mysqli_stmt_execute($stmt);
    
    return $con -> insert_id;
}


function get_user_list(mysqli $con):array{
    $sql = "SELECT id, date_reg, email, name, contact, password FROM User";
    return mysqli_fetch_all(mysqli_query($con, $sql), MYSQLI_ASSOC);
}
//function pr($val){
//    $bt   = debug_backtrace();
//    $file = file($bt[0]['file']);
//    $src  = $file[$bt[0]['line']-1];
//    $pat = '#(.*)'.__FUNCTION__.' *?\( *?(.*) *?\)(.*)#i';
//    $var  = preg_replace ($pat, '$2', $src);
//    echo '<script>console.log("'.trim($var).'='.
//        addslashes(json_encode($val,JSON_UNESCAPED_UNICODE)) .'")</script>'."\n";
//}
function add_user(string $email, string $name, string $password, string $message, mysqli $con){

    $password_temp = password_hash($password, PASSWORD_DEFAULT);
    $sql = "INSERT INTO User(email, name, contact, password)
            VALUES(?,?,?,?);";
    $stmt = mysqli_prepare($con, $sql);

    mysqli_stmt_bind_param($stmt, 'ssss', $email, $name, $message, $password_temp);

    mysqli_stmt_execute($stmt);

}
function get_user(string $email, mysqli $con):array|null{
    $sql = "SELECT id, date_reg, email, name, contact, password FROM User WHERE email = ?";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, 's', $email);
    mysqli_stmt_execute($stmt);
    $res = mysqli_stmt_get_result($stmt);
    return mysqli_fetch_assoc($res);
}

function pr($val){
    $bt   = debug_backtrace();
    $file = file($bt[0]['file']);
    $src  = $file[$bt[0]['line']-1];
    $pat = '#(.*)'.__FUNCTION__.' *?\( *?(.*) *?\)(.*)#i';
    $var  = preg_replace ($pat, '$2', $src);
    echo '<script>console.log("'.trim($var).'='.
        addslashes(json_encode($val,JSON_UNESCAPED_UNICODE)) .'")</script>'."\n";
 }