<?php
include_once __DIR__.'/../PDO/PDO.php';
$obj = PDO_class::initializer();

$id=$_GET['id'] ?? null;
$type=$_GET['type']=="" ? 2 : $_GET['type'];
if(!$id){
    $id = 0;
}
$rows = $obj->get_images($id , $type);

exit(json_encode($rows ,JSON_PRETTY_PRINT));

?>