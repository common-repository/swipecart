<?php
/**
 * Admin View: Page - Admin options.
 *
 * @package WooCommerce\Integrations
 */

defined('ABSPATH') or die('No script kiddies please!');

?>

<table class="form-table">
	<tr valign="top">
		<th scope="row" class="titledesc">
			<label>
				<?php esc_html_e('StoreFront Token', 'swipecart'); ?>
			</label>
		</th>
		<td class="forminp">
			<fieldset>
				<legend class="screen-reader-text"><span><?php esc_html_e('Storefront Token', 'swipecart'); ?></span></legend>
				<input class="input-text regular-input" type="text" value="<?php echo $this->authCombo['StoreFrontToken']; ?>" readonly>
				<p class="description"><?php esc_html_e('This StoreFront Token required for Mobile API.', 'swipecart'); ?></p>
			</fieldset>
		</td>
	</tr>
</table>
