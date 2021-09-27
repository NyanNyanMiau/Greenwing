	<?php foreach ((array)$tasks as $task): ?>
<!--
	<textarea> <?php print_r($task) ?> </textarea>
 -->

	<div class="task task-element pt-4 px-3 color-<?= $task['color_id'] ?>">
		<div class="task-header d-flex <?= empty($task['subtasks']) ? "ms-4" : "" ?>">

		<?php if ( !empty($task['subtasks']) ): ?>
			<a class="collapseOpener collapsed px-2 me-2" data-bs-toggle="collapse" href="#taskList<?= $task['id'] ?>"
				role="button" aria-expanded="false" aria-controls="taskList<?= $task['id'] ?>"><i></i></a>
		<?php endif; ?>

            <div>
            	<i class="fa fa-tasks me-1"></i>
                <?php if ($this->user->hasProjectAccess('TaskModificationController', 'edit', $task['project_id'])): ?>
                    <?php if (isset($show_items_selection)): ?>
                        <input type="checkbox" data-list-item="selectable" name="tasks[]" value="<?= $task['id'] ?>">
                    <?php endif ?>
                    <?= $this->render('task/dropdown', array('task' => $task, 'redirect' => isset($redirect) ? $redirect : '')) ?>
                <?php else: ?>
                    <strong><?= '#'.$task['id'] ?></strong>
                <?php endif ?>

                <span class="ms-2 table-list-title <?= $task['is_active'] == 0 ? 'status-closed' : '' ?>">
                    <?= $this->url->link($this->text->e($task['title']), 'TaskViewController', 'show', array('project_id' => $task['project_id'], 'task_id' => $task['id'])) ?>
                </span>
            </div>

		    <?php if (! empty($task['category_id'])): ?>
	        <span class="ms-4 table-list-category <?= $task['category_color_id'] ? "color-{$task['category_color_id']}" : '' ?>">
	            <?php if ($this->user->hasProjectAccess('TaskModificationController', 'edit', $task['project_id'])): ?>
	                <?= $this->url->link(
	                    $this->text->e($task['category_name']),
	                    'TaskModificationController',
	                    'edit',
	                    array('task_id' => $task['id'], 'project_id' => $task['project_id']),
	                    false,
	                    'js-modal-medium' . (! empty($task['category_description']) ? ' tooltip' : ''),
	                    t('Change category')
	                ) ?>
	                <?php if (! empty($task['category_description'])): ?>
	                    <?= $this->app->tooltipMarkdown($task['category_description']) ?>
	                <?php endif ?>
	            <?php else: ?>
	                <?= $this->text->e($task['category_name']) ?>
	            <?php endif ?>
	        </span>
		    <?php endif ?>

			<span class="ms-auto task_timing">
			    <?php if (! empty($task['time_estimated']) || ! empty($task['time_spent'])): ?>
		        <span class="task-time-estimated" title="<?= t('Time spent and estimated') ?>">
		            <span class="ui-helper-hidden-accessible"><?= t('Time spent and estimated') ?> </span><?= $this->text->e($task['time_spent']) ?>/<?= $this->text->e($task['time_estimated']) ?>h
		        </span>
			    <?php endif ?>
				<span class="ms-4 duration">
				    <?php if (! empty($task['date_started'])): ?>
			        <span title="<?= t('Start date') ?>" class="task-date">
			            <?= $this->dt->date($task['date_started']) ?>
			        </span>
				        <?php if ($task['date_due']): ?> - <?php endif; ?>
				    <?php endif ?>
				    <?php if (! empty($task['date_due'])): ?>
			        <span title="<?= t('Due date') ?>" class="task-date
			            <?php if (time() > $task['date_due']): ?>
			                 task-date-overdue
			            <?php elseif (date('Y-m-d') == date('Y-m-d', $task['date_due'])): ?>
			                 task-date-today
			            <?php endif ?>
			            ">
			            <?= $this->dt->datetime($task['date_due']) ?>
			        </span>
				    <?php endif ?>
				</span>
			</span>

			<span class="task-status">
			<?=  $task['is_active'] == 0 ? '<i class="fa fa-check-square-o fa-fw"></i>' : '<i class="fa fa-square-o fa-fw"></i>' ?>
			</span>

		</div>

		<div class="task-content mt-3">
			<div class="d-flex ms-4">
				<?= $this->avatar->render(
					$task['owner_id'],
					$task['assignee_username'],
					$task['assignee_name'],
					$task['assignee_email'],
					$task['assignee_avatar_path'],
					'avatar-inline',
					38
				) ?>
				<div class="markdown align-self-center ms-4">
					<?= $this->text->markdown($task['description'], isset($is_public) && $is_public) ?>
				</div>
			</div>

			<div class="tasks-list-icons d-flex align-items-center ms-4 my-3 sh-color">
				<?= $this->render('Greenwing:dashboard/tasks_tasklist_icons', ['task'=>$task])?>
			</div>
			<hr class="mx-n3 mb-0">
			<?= $this->render('Greenwing:dashboard/tasks_subtasklist', ['task'=>$task]); ?>
		</div>
	</div>
	<?php endforeach; ?>
