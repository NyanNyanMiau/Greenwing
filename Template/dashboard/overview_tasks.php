<?php $taskcount = $this->dashboardHelper->getProjectTaskSubtaskCount($user); ?>
<div class="page-header">
	<div class="d-flex align-items-center flex-wrap">
		<h2><?= $this->url->link(t('Open tasks (%d) and subtasks (%d)', $taskcount['task_cnt'], $taskcount['subtask_cnt']), 'MyDashboardController', 'tasksoverview', array('user_id' => $user['id'], 'plugin' => 'Greenwing')) ?></h2>

		<div class="ms-auto">
			<?=$this->render('Greenwing:dashboard/tasks_taskfilter', array('user_id' => $user['id'], 'ref' => $action))?>
		</div>

	</div>
</div>

<?php if ($paginator->isEmpty()): ?>
    <p class="alert"><?= t('There is no result.') ?></p>
<?php else: ?>

	<div hidden>
		<?= $this->render('task_list/header', array(
				'paginator' => $paginator,
		)) ?>
	</div>

	<div class="project-list">
		<?php foreach ($paginator->getCollection() as $project): ?>
		<div class="project-wrapper">
			<div class="project-header d-flex p-2 p-md-3 mb-0">
				<a class="collapseOpener collapsed px-2 me-md-1" data-bs-toggle="collapse" href="#projectList<?= $project['id'] ?>"
					role="button" aria-expanded="false" aria-controls="projectList<?= $project['id'] ?>"><i></i></a>

				<div class="headline d-flex align-items-center me-5">
					<?= $this->render('Greenwing:dashboard/overview_project_header', array(
							'project' => $project,
					)) ?></div>

				<span class="me-3 taskstat">
					<span class="taskstat_ac"><?= $project['nb_active'] ?></span> /
					<span class="taskstat_ac me-2"><?= $project['nb_closed'] + $project['nb_active'] ?></span><?= t('open') ?>
				</span>
				<span class="me-3 taskstat" title="finish today">
					<span class="due-color me-2">
						<?= $this->dashboardHelper->getProjectDueCount($project) ?>
					</span><?= t('due') ?>
				</span>
				<span class="me-3 taskstat" title="overdue">
					<span class="overdue-color me-2">
						<?= $this->dashboardHelper->getProjectOverdueCount($project) ?>
					</span><?= t('overdue') ?>
				</span>
				<span class="ms-auto">
				<?= $this->render('Greenwing:dashboard/overview_project_duration', array(
						'project' => $project,
    				    'icon_pos' => 'end'
				)) ?>
				</span>
			</div>
			<div class="task-list collapse" id="projectList<?= $project['id'] ?>">
				<?= $this->render('Greenwing:dashboard/tasks_tasklist', array(
					'tasks' => $this->dashboardHelper->getProjectTaskList($project),
					'user' => $user
				));?>
			</div>
		</div>
		<?php endforeach ?>
    <?= $paginator ?>
    </div>
<?php endif ?>

<?= $this->hook->render('template:dashboard:tasks', array('user' => $user)) ?>

<?= $this->asset->js('plugins/Greenwing/Assets/mytasks_openstate.js') ?>