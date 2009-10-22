<?php
if (!function_exists('add_action'))
{
	require_once("../../../../wp-config.php");
	require_once(dirname(__FILE__).'/../trackthebook-model.php');
}
?>
<?php header('Content-type: application/vnd.google-earth.kml+xml'); ?>
<kml xmlns="http://www.opengis.net/kml/2.2">
  <Document>
  <?php
  	$trackthebook_model = new TrackTheBookModel;
  	
  	$filters = array();
  	$filters['book'] = $_GET['book'];
  	
	$placemarks = $trackthebook_model->getPlacemarks($filters);
	foreach ($placemarks as $placemark) {
  ?>
    <Placemark id="<?php echo $placemark->id; ?>">
      <name>Book <?php echo $placemark->book; ?></name>
      <description>
        <![CDATA[
        <?php echo $placemark->location; ?><br />
		Added on <?php echo date(TTB_DATE_FORMAT,strtotime($placemark->date_added)); ?>
        ]]>
      </description>
      <Point>
        <coordinates><?php echo $placemark->coords; ?></coordinates>
      </Point>
    </Placemark>
  <?php 
	}
  ?>
  </Document>
</kml>
