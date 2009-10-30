<?php 
$trackthebook_model = new TrackTheBookModel;
?>
<style type="text/css" media="screen">
.trackthebook #filter_container {
	float: left;
}
.trackthebook #filters {
	border: 1px solid #CCCCCC;
	padding: 5px;
	margin: 2px;
}
.trackthebook #filters h3 {
	margin: 0;
}
.trackthebook #filters p {
	text-align: left;
}
</style>
<div class="wrap trackthebook">
	<div id="filter_container">
		<div id="filters">
			<h3><?php _e('Filter by'); ?></h3>
			<form id="trackthebook_register" method="get" action="<?php echo $_SERVER["REQUEST_URI"]; ?>">
			<input type="hidden" name="page_id" id="page_id" value="<?php echo $_GET['page_id']; ?>" />
			<table>
				<tr>
					<td><?php _e('Book'); ?></td>
					<td><select name="book" id="book">
							<option value=""></option>
							<?php
							$books = $trackthebook_model->getUniqueBooks();
							foreach ($books as $book) {
								echo '<option value="'. $book . '"';
								if ($book == $_GET['book']) {
									echo " selected";
								}
								
								echo '>' . $book . '</option>' . "\n";
							} 
							?>
							
						</select>
					</td>
				</tr>
			</table>
			<p><input type="submit" name="Submit"
				value="<?php _e('Filter') ?>" />
			</p>
			</form>
		</div>
		<div id="results">
			<?php
				if (!empty($_GET['book'])) {
					$results = $trackthebook_model->getBookByNumber($_GET['book']);
					
					$row_counter = 1;
					foreach ($results as $row) {
					?>
					<table>
						<tr>
							<td style="vertical-align: top;"><strong><?php echo $row_counter; ?>.</strong> </td>
							<td><?php echo $row->location; ?><br />
								<?php echo __('Added on ') . date(TTB_DATE_FORMAT,strtotime($row->date_added)); ?>
							</td>
						</tr>
					</table>
					<?php 
						$row_counter++;
					}
				} 
			?>
		</div>
	</div>
</div>
