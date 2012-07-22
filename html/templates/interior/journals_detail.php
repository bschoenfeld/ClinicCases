<div class="journal_detail">

	<div class="journal_header ui-widget-header ui-corner-tl ui-corner-all ui-helper-clearfix">

		<img src="<?php echo return_thumbnail($dbh,$username); ?>" border="0">

		<p>Journal Submitted by <?php echo username_to_fullname ($dbh,$username); ?> on <?php echo extract_date_time($date_added); ?>
		</p>

		<div class = "journal_detail_control">

			<button></button>

			<button></button>

		</div>

	</div>

	<div class="journal_body" data-id="<?php echo $id; ?>">

		<?php echo $text; ?>

		<div class="journal_comments">

			<?php if ($comments)
			{
				$c_array = unserialize($comments);

				foreach ($c_array as $key => $value) {?>

					<div class = "comment" data-id="<?php echo $key; ?>">

						<img src="<?php echo return_thumbnail($dbh, $value['by']); ?>" border="0">

						<p><?php echo $value['text']; ?></p>

					</div>

				<?php }
			}
			?>

			<div class = "comment">
				<img src="<?php echo return_thumbnail($dbh, $_SESSION['login']); ?>" border="0">

				<textarea class="expand">Your comment</textarea>

				<a href="#" class="comment_save">Save</a>

			</div>

		</div>

	</div>




</div>