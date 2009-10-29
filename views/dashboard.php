<?php
$trackthebook_model = new TrackTheBookModel;

$total_registered = $trackthebook_model->getTotalRegistered();
$most_passed = $trackthebook_model->getMostPassed();
?>
<div id="dashboard_right_now">
	<div class='inside' style="margin-left:0; margin-right: 0;">
		<p class='sub'><?php _e('Statistics')?></p>
		<div class='table'>
			<table>
				<tr class='first'>
					<td><?php _e('Number of books registered')?></td>
					<td class="b t"><?php echo $total_registered; ?></td>
				</tr>
				<tr>
					<td><?php _e('Book(s) that have been passed the most')?></td>
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
	<div class="versions">
	<p><a class='button' href='<?php echo TTB_URL; ?>/export.php'>Download CSV file of ALL Registered Books</a></p>
	<div style='clear:both; height: 0px;'></div>
	</div>
</div>