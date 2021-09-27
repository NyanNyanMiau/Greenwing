<?php if (! empty($task['subtasks'])): ?>
<div class="subtasks-list collapse" id="taskList<?= $task['id'] ?>">
	<?php foreach ($task['subtasks'] as $subtask): ?>
	<div class="subtask ms-4 mt-4 mb-3">
		<div class="subtask-header d-flex">
			<span>
			<?= $this->modal->medium('subtasks ms-2 me-2', '<strong>#' . $subtask['id'].'</strong> '. t('Teilaufgabe'), 'SubtaskController', 'edit', array('task_id' => $task['id'], 'project_id' => $task['project_id'], 'subtask_id' => $subtask['id'])) ?>
			</span>
			<span class="ms-auto subtask_timing">
				<span class="ms-4 duration">
				    <?php if (! empty($subtask['due_date'])): ?>
			        <span title="<?= t('Due date') ?>" class="task-date
			        <?php if (time() > $subtask['due_date']): ?>
			                 task-date-overdue
			                 <?php elseif (date('Y-m-d') == date('Y-m-d', $subtask['due_date'])): ?>
			                 task-date-today
			            <?php endif ?>
			            ">
			            <i class="fa fa-calendar" role="img" aria-label="<?= t('Due date') ?>"></i>
			            <?= $this->dt->datetime($subtask['due_date']) ?>
			        </span>
				    <?php endif ?>

				    <?= $this->helper->subtask->renderToggleStatus($task, $subtask); ?>
				</span>
			</span>
		</div>
		<div class="mt-3">
			<div class="d-flex">
				<?= $this->avatar->render(
					$subtask['user_id'],
					$subtask['username'],
					$subtask['name'],
					'',
					'',
					'avatar-inline',
					38
				) ?>
				<div class="markdown align-self-center ms-4">
					<?= $this->modal->medium('', $this->text->e($subtask['title']), 'SubtaskController', 'edit', array('task_id' => $task['id'], 'project_id' => $task['project_id'], 'subtask_id' => $subtask['id'])) ?>
				</div>
			</div>
		</div>
	</div>
	<hr class="mx-n3 mb-0">
	<?php endforeach; ?>
</div>
<?php endif; ?>