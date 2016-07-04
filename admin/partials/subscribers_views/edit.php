<?php
/**
 * Created by PhpStorm.
 * User: eugen
 * Date: 27.06.16
 * Time: 19:45
 */
?>
	<div class="wrap atf-fields">


	<h2><?php
		if ($subscriber !== null) echo __('Edit subscriber', 'si') . ' #' . $data['id'];
		else _e('New subscriber', 'si');
		?></h2>

	<form method="post">
		<input type="hidden" name="action" value="doedit"/>
		<input type="hidden" name="id" value="<?php echo $data['id']; ?>"/>
		<table class="form-table">
			<tr class="form-required">
				<th scope="row"><label for="name"><?php _e('Name'); ?></label></th>
				<td><?php AtfHtmlHelper::text(array('id' => 'name', 'name' => 'name', 'value' => $data['name'])); ?></td>
			</tr>
			<tr class="form-required">
				<th scope="row"><label
						for="email"><?php _e('Email <span class="description">(required)</span>'); ?></label></th>
				<td><?php AtfHtmlHelper::text(array('id' => 'email', 'name' => 'email', 'value' => $data['email'])); ?></td>
			</tr>
			<tr class="form-required">
				<th scope="row"><label for="groups"><?php _e('Groups'); ?></label></th>
				<td>
					<?php

					AtfHtmlHelper::multiselect(array('id' => 'groups', 'name' => 'groups',
						'value' => $data['groups'],
						'options' => AtfHtmlHelper::get_taxonomy_options(array('taxonomy' => 'newsletter_groups'))
					)); ?>
				</td>
			</tr>
			<tr class="form-field form-required">
				<th scope="row"><label><?php _e('Confirm'); ?></label></th>
				<td><?php AtfHtmlHelper::tumbler(array('id' => 'confirm', 'name' => 'confirm', 'value' => $data['status'])); ?></td>
			</tr>
		</table>

		<p class="submit"><input type="submit" name="submit" id="submit" class="button button-primary"
								 value="Submit"></p>
	</form>

<?php