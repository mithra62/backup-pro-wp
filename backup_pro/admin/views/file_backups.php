<div class='wrap'>
<h2>Backup Pro Dashboard</h2>

<?php include '_includes/_backups_submenu.php'; ?>
<div class="clear_left shun"></div>
	<table class="widefat" width="100%"  border="0" cellpadding="0" cellspacing="0">
	<thead>
		<tr class="even">
			<th><?php echo $view_helper->m62Lang('total_backups'); ?></th>
			<th style="width:65%"><?php echo $view_helper->m62Lang('total_space_used'); ?></th>
			<th><div style="float:right"><?php echo $view_helper->m62Lang('last_backup_taken'); ?></div></th>
			<th><div style="float:right"><?php echo $view_helper->m62Lang('first_backup_taken'); ?></div></th>
		</tr>
	</thead>
	<tbody>
		<tr class="odd">
			<td><?php echo $backup_meta['files']['total_backups']; ?></td>
			<td><?php echo $backup_meta['files']['total_space_used']; ?></td>
			<td><?php echo ($backup_meta['files']['newest_backup_taken'] != '' ? $view_helper->m62DateTime($backup_meta['files']['newest_backup_taken']) : $view_helper->m62Lang('na')); ?></td>
			<td width="150"><?php echo ($backup_meta['files']['oldest_backup_taken'] != '' ? $view_helper->m62DateTime($backup_meta['files']['oldest_backup_taken']) : $view_helper->m62Lang('na')); ?></td>
		</tr>
	</tbody>
	</table>	
<div class="clear_left shun"></div>

<?php //echo form_open($query_base.'delete_backup_confirm', array('id'=>'my_accordion')); ?>
		<input type="hidden" name="type" id="hidden_backup_type" value="files" />

<h3  class="accordion"><?php echo $view_helper->m62Lang('file_backups').' ('.count($backups['files']).')'?></h3>
	<?php if(count($backups['files']) == 0): ?>
		<div class="no_backup_found"><?php echo $view_helper->m62Lang('no_database_backups')?> <a href="<?php echo $nav_links['backup_db']; ?>"><?php echo $view_helper->m62Lang('would_you_like_to_backup_now')?></a></div>
	<?php else: ?>
	
	
		<form name="update_settings" action="{{ url('backuppro/delete/confirm') }}" method="post" accept-charset="UTF-8" />

		<input type="hidden" name="type" id="hidden_backup_type" value="database" />	
			{% include 'backuppro/_includes/_backup_table' with {'enable_type': 'no', '_backups': backups.database, 'enable_delete':'yes', 'enable_editable_note':'yes', 'enable_actions':'yes' } %}
		
		<div class="buttons right">
			<input type="submit" value="{{ "delete_backups"|m62Lang|t }}" class="btn submit" >
		</div>
		
		</form>
							
	<?php endif; ?>
</div>