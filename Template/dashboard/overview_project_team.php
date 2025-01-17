<p class="mb-2 sh-color"><?= t("Project manager") ?></p>
<div class="d-flex align-items-center w-100 mb-2">
	<?= $this->avatar->render(
		$project['owner_id'],
		$project['owner_username'],
		$project['owner_name'],
		'',
		'',
		'me-3',
		 38
	) ?><span class=""><?= $this->text->e($project['owner_name'] ?: $project['owner_username']) ?></span>
</div>

<p class="mb-2 sh-color"><?= t("Team") ?></p>
<div class="d-flex flex-wrap align-items-center w-100 mb-2">
	<?php foreach ((array) $projectData["team"] as $userId => $username):
		if ( $project['owner_id'] != $userId ): ?>
			<?= $this->avatar->render(
				$userId,
				$username,
				'',
				'',
				'',
			    'mb-1 '. (count($projectData["team"]) < 9 ? 'me-1' : 'me-n2'),
				 38
			) ?>
		<?php endif; ?>
	<?php endforeach; ?>
</div>