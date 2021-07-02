<?php

$data = $_POST['data'];
$data2 = $_POST['data2'];
// $data2 = $_POST['second'];

// echo($data);

function gen_one_to_three($data) {
    yield $data;
    yield $data;
    // yield readline("enter 2st number: ");
}

$generator = gen_one_to_three($data);
foreach ($generator as $value) {
    echo "This is value of : $value\n";
}
?>