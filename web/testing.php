<?php
$json_file = 'sites/default/files/migration/companies.json';
if (file_exists($json_file)) {
    $data = file_get_contents($json_file);
    echo $data;
} else {
    echo "File not found.";
}
?>

