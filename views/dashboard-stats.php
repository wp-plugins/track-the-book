<?php
$trackthebook_model = new TrackTheBookModel;

$total_registered = $trackthebook_model->getTotalRegistered();
$most_passed = $trackthebook_model->getMostPassed();
?>
<div id="dashboard_right_now">
	<div class='table'>
		<table>
			<tr class='first'>
				<td><?php _e('Number of books registered')?></td>
				<td class="b t"><?php echo $total_registered; ?></td>
			</tr>
			<tr>
				<td><?php _e('Book(s) that has been passed the most')?></td>
				<td class="b t"><?php
					if (is_array($most_passed)) {
						foreach ($most_passed as $book => $times_passed) {
								echo __('Book #') . $book . __(' (') . $times_passed . __(' times)') . "<br />";
						} 
					}
					else { // If this is not an array then tell the user that no books have been passed
						echo $most_passed; 
					}
					?></td>
			</tr>
		</table>
	</div>
</div>