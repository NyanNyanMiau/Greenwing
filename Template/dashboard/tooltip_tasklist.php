<div class="tooltip">
    <table class="table-small">
		<tr>
			<th>Aufgabe</th>
			<th>Start</th>
			<th>Fällig</th>
		</tr>
        <?php foreach ((array)$tasks as $task): ?>
        <tr>
            <td>
    <span class="table-list-title <?= $task['is_active'] == 0 ? 'status-closed' : '' ?>">
        <?= $this->url->link('<b>#'.$task['id'].'</b> ' . $this->text->e($task['title']), 'TaskViewController', 'show', array('project_id' => $task['project_id'], 'task_id' => $task['id'])) ?>
    </span>
            </td>
            <td>
			    <?php if (! empty($task['date_started'])): ?>
			        <span title="<?= t('Start date') ?>" class="task-date">
			            <i class="fa fa-clock-o" role="img" aria-label="<?= t('Start date') ?>"></i>
			            <?= $this->dt->date($task['date_started']) ?>
			        </span>
			    <?php endif ?>
            </td>
            <td>
	<?php if (! empty($task['date_due'])): ?>
        <span title="<?= t('Due date') ?>" class="task-date
            <?php if (time() > $task['date_due']): ?>
                 task-date-overdue
            <?php elseif (date('Y-m-d') == date('Y-m-d', $task['date_due'])): ?>
                 task-date-today
            <?php endif ?>
            ">
            <i class="fa fa-calendar" role="img" aria-label="<?= t('Due date') ?>"></i>
            <?= $this->dt->datetime($task['date_due']) ?>
        </span>
    <?php endif ?>
            </td>
        </tr>
        <?php endforeach; ?>
    </table>
</div>
