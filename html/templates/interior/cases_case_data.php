<div class="user_display ui-widget ui-corner-bottom user_widget" tabindex="1">

</div>

<div class="case_detail_panel_tools">

	<div class="case_detail_panel_tools_left">

		<?php if ($type == 'new'){ echo "<b>Please enter new case data</b>";} ?>

		<?php if ($type == 'edit'){ echo "<b>Edit Case Data below:</b>";} ?>


	</div>

	<div class="case_detail_panel_tools_right">

		<?php if ($type !== 'new' AND $type !== 'edit'){?>
            <?php if ($_SESSION['permissions']['edit_cases'] === '1'){ ?>
                <button class="case_data_edit">Edit</button>
            <?php } ?>
            <?php if ($_SESSION['permissions']['delete_cases'] === '1'){ ?>
                <button class="case_data_delete">Delete</button>
            <?php } ?>
                <button class="case_data_print">Print</button>

		<?php } else { ?>
				<button class="case_cancel_submit <?php if ($type == 'new') {echo 'cancel_new_case';}?>">
				Cancel
				</button>
				<button class="case_modify_submit <?php if ($type == 'new') {echo 'update_new_case';}?>">
				Submit
				</button>
		<?php } ?>

	</div>

</div>

<div class="case_detail_panel_casenotes">


<?php if ($type == 'new' || $type == 'edit'){ ?>

	<div class="new_case_data">

		<form>
			
			<div class="case_data_section">

			<?php foreach ($dta as $d) {extract($d);

			if ($section_header != null)
			{ ?>
				</div>
				<div class="new_case_data_section_title" style="background-color:<?php echo htmlspecialchars($section_header_color,ENT_QUOTES,'UTF-8'); ?>"><?php echo $section_header; ?></div>
				<div class="new_case_data_section">
			<?php } ?>

			<div class = "<?php echo $db_name; ?>_display new_case_data_display">
				
				<label><?php echo htmlspecialchars($display_name,ENT_QUOTES,'UTF-8'); ?></label>

				<?php if ($input_type === 'text'){ ?>

					<input type="text" name = "<?php echo $db_name; ?>" value = "<?php echo htmlspecialchars($value,ENT_QUOTES,'UTF-8'); ?>">

				<?php } elseif ($input_type === 'date'){ ?>

					<input type="hidden" class="date_field" name = "<?php echo $db_name; ?>" value = "<?php echo $value; ?>">

				<?php } elseif ($input_type === 'multi-text'){ ?>

						<?php if (!empty($value)){$items = unserialize($value);
							foreach ($items as $key => $item) {?>

						<span class = "<?php echo $db_name . "_multi-text multi-text"; ?>">

						<input class="multi-text" name = "<?php echo $db_name; ?>" value="<?php echo htmlspecialchars($key,ENT_QUOTES,'UTF-8'); ?>">

						</span>

						<?php }}else{ ?>

						<span class = "<?php echo $db_name . "_multi-text multi-text"; ?>">

						<input class="multi-text" name = "<?php echo $db_name; ?>">

						</span>

				<?php }} elseif ($input_type === 'dual'){ ?>

						<?php if (!empty($value)){$items = unserialize($value);
							foreach ($items as $key=>$val){?>

						<span class = "<?php echo $db_name . "_dual dual_input"; ?>">

							<select class="dual" name="<?php echo $db_name . '_select'; ?>">

								<?php $options = unserialize($select_options);
								foreach ($options as $o_key => $o){
									if ($o_key  == $val){?>

									<option selected=selected value = "<?php echo htmlspecialchars($o_key, ENT_QUOTES,'UTF-8'); ?>">
                                    <?php echo htmlspecialchars($o,ENT_QUOTES,'UTF-8'); ?></option>
									<?php } else{ ?>

									<option value = "<?php echo htmlspecialchars($o_key, ENT_QUOTES, 'UTF-8'); ?>">
                                    <?php echo htmlspecialchars($o,ENT_QUOTES,'UTF-8'); ?></option>

									<?php } ?>

								<?php } ?>

							</select>

							<input type="text" name = "<?php echo $db_name; ?>" value = "<?php echo htmlspecialchars($key,ENT_QUOTES,'UTF-8'); ?>">

						</span>

						<?php }} else { ?>

							<span class = "<?php echo $db_name . "_dual dual_input"; ?>">

								<select class="dual" name="<?php echo $db_name . '_select'; ?>">

									<?php $options = unserialize($select_options);
									foreach ($options as $o_key => $o){ ?>

									<option value = "<?php echo $o_key; ?>"><?php echo htmlspecialchars($o,ENT_QUOTES,'UTF-8'); ?></option>

									<?php } ?>

								</select>

								<input type="text" name = "<?php echo $db_name; ?>" value = "">

							</span>

						<?php } ?>

				<?php } elseif ($input_type === 'select') { ?>

					<select name = "<?php echo $db_name; ?>" style="min-width:60px;">

						<option value="" <?php if($type == 'new'){echo "selected=selected";} //identify new users?>> --- </option>

						<?php
							$s = unserialize($select_options);

							foreach ($s as $key => $val) {

							if ($val == $value)
								{echo "<option value = '" . htmlspecialchars($val,ENT_QUOTES,'UTF-8')  . "' data-code='$key' selected=selected>" .
                                htmlspecialchars($val ,ENT_QUOTES,'UTF-8'). "</option>";}
							else
								{echo "<option value = '" . htmlspecialchars($val,ENT_QUOTES,'UTF-8')  . "' data-code='$key'>" . 
                                htmlspecialchars($val ,ENT_QUOTES,'UTF-8'). "</option>";}

						} ?>

					</select>

				<?php } elseif ($input_type === 'radio'){ ?>

					<?php 	$s = unserialize($select_options);
							foreach ($s as $key => $val) { ?>
								<input type="radio" name="<?php echo $db_name; ?>" value="<?php echo htmlspecialchars($val,ENT_QUOTES,'UTF-8'); ?>" <?php if ($val == $value) {echo 'checked';} ?>>
								<label style="display:inline"><?php echo htmlspecialchars($val,ENT_QUOTES,'UTF-8'); ?></label>
					<?php 	} ?>

				<?php } elseif ($input_type === 'textarea'){ ?>

					<textarea name = "<?php echo $db_name; ?>"><?php echo htmlspecialchars($value,ENT_QUOTES,'UTF-8'); ?></textarea>

				<?php } elseif ($input_type === 'm-textarea'){ ?>

					<?php if (!empty($value)){ $items = @unserialize($value);
						// This is here to support switching from textarea to m-textarea after data already exists in the system
						if($items === false) {?>

							<textarea name = "<?php echo $db_name; ?>" class = "<?php echo $db_name; ?>_m-textarea m-textarea"><?php echo htmlspecialchars($value,ENT_QUOTES,'UTF-8'); ?></textarea>
						
						<?php } else {
							foreach ($items as $key => $item) {?>

								<textarea name = "<?php echo $db_name; ?>" class = "<?php echo $db_name; ?>_m-textarea m-textarea"><?php echo htmlspecialchars($item,ENT_QUOTES,'UTF-8'); ?></textarea>

					<?php }}}else{ ?>
						<textarea name = "<?php echo $db_name; ?>" class = "<?php echo $db_name; ?>_m-textarea m-textarea"></textarea>
				<?php }} ?>

			</div>

			<?php } ?>

			<p style="text-align:center;clear:both;">
				<button class="case_cancel_submit <?php if ($type == 'new') {echo 'cancel_new_case';}?>">
				Cancel
				</button>

				<button class="case_modify_submit <?php if ($type == 'new') {echo 'update_new_case';}?>">
				Submit
				</button>
			</p>
		
			</div>
		</form>

	</div>

<?php } else { ?>

	<div class = "case_data">
		<div class="case_data_section">

		<?php foreach ($dta as $d) {extract($d);
			if ($section_header != null)
			{ ?>
				</div>
				<div class="case_data_section_title" style="background-color:<?php echo htmlspecialchars($section_header_color,ENT_QUOTES,'UTF-8'); ?>"><?php echo $section_header; ?></div>
				<div class="case_data_section">
			<?php }

			if ($input_type == 'dual') //special handling for dual inputs
				{ ?>

					<div class = "<?php echo $db_name; ?>_display case_data_display">

						<div class="case_data_name"><?php echo htmlspecialchars($display_name,ENT_QUOTES,'UTF-8'); ?></div>

						<?php if (!empty($value)){$duals = unserialize($value);

							foreach ($duals as $v => $type) { ?>

							<div class="case_data_value"><?php echo $v . " (" . $type . ")"; ?></div>

							<?php }?>

						<?php } else {?>
							<div class="case_data_value"></div>
						<?php }?>
					</div>

			<?php } else { ?>

		<div class = "<?php echo $db_name;?>_display case_data_display">

				<?php if ($db_name === 'initial_conflicts_checked'){ ?>
					<button style="float:right" onclick="checkConflicts('<?php echo $case_id;?>')">Check</button>
				<?php } ?>

				<div class = "case_data_name"><?php echo htmlspecialchars($display_name,ENT_QUOTES,'UTF-8'); ?></div>

				<div class="case_data_value">
					<?php
					//first check if this is a serialized value
					$items = @unserialize($value);
					if ($items !== false)
					{
						$val = null;
						foreach ($items as $key => $item) {
							if($input_type === 'm-textarea') {
								$val .= $item . "\n\n";
							} else {
								$val .= $key . ", ";
							}
						}

						echo nl2br(htmlspecialchars(substr($val, 0,-2), ENT_QUOTES, 'UTF-8'));
					}
					elseif ($input_type === 'date')
					//then check if it's a date
					{
						echo sql_date_to_us_date($value);
					}
					else
					{
						echo htmlspecialchars($value,ENT_QUOTES,'UTF-8');
					}?>
				</div>

		</div>

			<?php }} ?>
		</div>			
	</div>

<?php } ?>
</div>
