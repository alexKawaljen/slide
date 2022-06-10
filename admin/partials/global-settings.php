
<ul class="fieldset">
	<li>
		<?php $this->create_dcs_form_control( $this->plugin_prefix.'_speed', $this->settings ); ?>
	</li>
	
	<li>
		<?php $this->create_dcs_form_control( $this->plugin_prefix.'_has_min_width', $this->settings ); ?>
		
		<div class="<?php echo $this->plugin_prefix; ?>_min_width_settings settings-container">
			<ul class="fieldset">
				<li>
					<?php $this->create_dcs_form_control( $this->plugin_prefix.'_min_width', $this->settings ); ?>
				</li>
			</ul>
		</div>
	</li>
</ul>
