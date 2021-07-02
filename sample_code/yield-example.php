<?

$data = 1;

function gen_one_to_three() {
    yield $data;
}

$generator = gen_one_to_three();
foreach ($generator as $value) {
    echo "$value\n";
}
?>