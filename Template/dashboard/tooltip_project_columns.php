<div class="tooltip">
<!-- 	<textarea><?php print_r($debug);?></textarea>  -->
	<table class="table-small">
		<tr>
			<th>Spalte</th>
			<th></th>
			<th>offen</th>
			<th>fertig</th>
		</tr>
		<?php foreach ((array)$project['columns'] as $column): ?>
		<tr>
			<td><?= $this->text->e($column['title']) ?></td>
			<td>
				<?php if ($column['nb_open_tasks']): ?>
				<?= $this->app->tooltipLink('<i class="fa fa-eye fa-fw"></i>',
						$this->url->href('MyDashboardController', 'tooltip_tasklist', array('column_id' => $column["id"], 'project_id' => $column['project_id'], 'plugin'=>'Greenwing'))) ?>
				<?php endif ?>
			</td>
			<td><?= $column['nb_open_tasks'] ?></td>
			<td><?= $column['nb_closed_tasks'] ?></td>
		</tr>
		<?php endforeach ?>
	</table>
</div>