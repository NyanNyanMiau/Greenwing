<?php
// return percent value of given number and given total
function getPercent($i = 0, $total = 1, $prec=0){
	if ($total>0){
		return round($i/$total*100, $prec);
	} else {
		return 0;
	}
}
?>

<?= $this->hook->render('template:dashboard:show:before-filter-box', array('user' => $user)) ?>
<!-- filter box removed -->
<?= $this->hook->render('template:dashboard:show:after-filter-box', array('user' => $user)) ?>

<?php if (! $project_paginator->isEmpty()): ?>

	<div hidden>
	    <?= $this->render('project_list/header', array('paginator' => $project_paginator)) ?>
	</div>

	<div class="project-grid d-flex flex-wrap">
		<?php foreach ($project_paginator->getCollection() as $project): ?>
			<?php $projectData = $project_overview[$project['id']]; ?>
			<div class="project-element d-flex flex-column me-3 mb-3">

				<div class="headline d-flex"><?= $this->render('Greenwing:dashboard/overview_project_header', array(
						'project' => $project,
				)) ?></div>

				<div><?= $this->render('Greenwing:dashboard/overview_project_duration', array(
						'project' => $project,
				        'icon_pos' => 'start'
				)) ?></div>

				<?= $this->render('Greenwing:dashboard/overview_project_taskstatus', array(
						'project' => $project,
						"projectData" => $project_overview[$project['id']]
				)) ?>

				<hr class="my-3">
				<?= $this->render('Greenwing:dashboard/overview_project_team', array(
						'project' => $project,
						"projectData" => $project_overview[$project['id']]
				)) ?>

				<hr class="mt-auto mb-3">
				<div class="project-list-icons d-flex flex-wrap align-items-center sh-color">
					<?php if ($project['nb_active_tasks']): ?>
						<?= $this->app->tooltipLink('<i class="fa fa-bars fa-fw"></i>'.$project['nb_active_tasks'], $this->url->href('MyDashboardController', 'tooltip_project_columns', array('project_id' => $project['id'], 'plugin'=>'Greenwing'))) ?>
						<vr class="mx-3"></vr>
					<?php endif ?>

					<?php if ($project['nb_comments']): ?>
						<?= $this->modal->medium(
							'comments-o',
							$project['nb_comments'],
							'MyDashboardController',
							'project_comment_list',
							array('project_id' => $project['id'], 'plugin' => 'Greenwing'),
						) ?>
						<vr class="mx-3"></vr>
					<?php endif ?>

					<?php if ($project['nb_files']): ?>
						<?= $this->app->tooltipLink('<i class="fa fa-paperclip fa-fw"></i>'.$project['nb_files'], $this->url->href('MyDashboardController', 'tooltip_filelist', array('project_id' => $project['id'], 'plugin'=>'Greenwing'))) ?>
						<vr class="mx-3"></vr>
					<?php endif ?>

					<span>
						<i class="fa fa-pencil fa-fw"></i><?= $this->dt->datetime($project['last_modified']) ?>
					</span>
				</div>

            </div>
        <?php endforeach /* next project */?>
    </div>

    <?= $project_paginator ?>
<?php endif ?>


// <?= $this->hook->render('template:dashboard:show', array('user' => $user)) ?>

<?= $this->asset->js('plugins/Greenwing/Assets/taskchart.js') ?>
