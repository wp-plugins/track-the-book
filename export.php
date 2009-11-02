<?php
if (!function_exists('add_action'))
{
	require_once("../../../wp-config.php");
	require_once(dirname(__FILE__).'/trackthebook-model.php');
}

if ( is_user_logged_in() ) {
$trackthebook_model = new TrackTheBookModel;

$data = "";

$headings = array();
$headings[] = "id";
$headings[] = "book";
$headings[] = "name";
$headings[] = "location";
$headings[] = "coords";
$headings[] = "email";
$headings[] = "school";
$headings[] = "grade";
$headings[] = "ip_address";
$headings[] = "date_added";

$data .= '"' . join('","', $headings) . '"' . "\n"; 

$rows = $trackthebook_model->getAllRows();
foreach ($rows as $row) {
$data .= '"' . join('","', $row) . '"' . "\n"; // Join all values without any trailing commas and add a new line
}


// Output the headers to download the file
header("Content-type: text/csv");
header("Content-Disposition: attachment; filename=trackthebook.csv");
header("Pragma: no-cache");
header("Expires: 0");

echo $data;
}
else {
	_e('You must be logged in to view this resource.');
}
?>