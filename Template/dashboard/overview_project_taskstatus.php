<div class="d-flex mt-2">
	<div class="stats d-flex col-6 flex-column my-auto">
		<div class="stat d-flex align-items-center mb-2 pb-1">
			<span class="stat_color planned_color me-3"></span>
			<span><?= t('planned') ?></span>
			<b class="ms-auto percent"><?= getPercent($projectData['tasks']["planned"], $projectData['tasks']["total"]) ?></b>
		</div>
		<div class="stat d-flex align-items-center mb-2 pb-1">
			<span class="stat_color due-color me-3"></span>
			<span><?= t('due') ?></span>
			<b class="ms-auto percent"><?= getPercent($projectData['tasks']["due"], $projectData['tasks']["total"]) ?></b>
		</div>
		<div class="stat d-flex align-items-center mb-2 pb-1">
			<span class="stat_color overdue-color me-3"></span>
			<span>
				<?= $this->url->link(t('overdue'), 'BoardViewController', 'show', array('project_id' => $project['id'], 'search' => "status:open+due:<today")) ?>
			</span>
			<b class="ms-auto percent"><?= getPercent($projectData['tasks']["overdue"], $projectData['tasks']["total"]) ?></b>
		</div>
		<div class="stat d-flex align-items-center">
			<span class="stat_color closed_color me-3"></span>
			<span>
				<?= $this->url->link(t('closed'), 'BoardViewController', 'show', array('project_id' => $project['id'], 'search' => "status:closed")) ?>
			</span>
			<b class="ms-auto percent"><?= getPercent($projectData['tasks']["closed"], $projectData['tasks']["total"]) ?></b>
		</div>
	</div>
	<div class="d-flex col-6 flex-column align-items-end">
	<?php
		$columns = [
				[ "_total_", $projectData['tasks']["total"] ],
				[ "closed",  $projectData['tasks']["closed"] ],
				[ "planned", $projectData['tasks']["planned"] ],
				[ "due",     $projectData['tasks']["due"] ],
				[ "overdue", $projectData['tasks']["overdue"] ],
		];
		$names = [
				"planned" => t('planned'),
				"due"     => t('due'),
				"overdue" => t('overdue'),
				"closed"  => t('closed')
		];
// 		array_multisort(array_column($taskStatColumns, 1), SORT_ASC, $taskStatColumns);
// 		$taskStatColumns[] = [ "_total_", $projectData["total"] ];
	?>
		<div class="project-tasks-chart"
			id="project-tasks-chart<?= $project['id'] ?>"
			data-columns='<?= json_encode($columns); ?>'
			data-names='<?= json_encode($names); ?>'
			data-colors='<?= json_encode($colors); ?>'
		></div>
	</div>
</div>