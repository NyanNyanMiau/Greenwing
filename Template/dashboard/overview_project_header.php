<i class="fa fa-folder me-2"></i>
<?php if ($this->user->hasProjectAccess('ProjectViewController', 'show', $project['id'])): ?>
	<?= $this->render('project/dropdown', array('project' => $project)) ?>
<?php else: ?>
	<strong><?= '#'.$project['id'] ?></strong>
<?php endif ?>
	<div class="title ms-2">
		<?= $this->hook->render('template:dashboard:project:before-title', array('project' => $project)) ?>

		<span class="<?= $project['is_active'] == 0 ? 'status-closed' : '' ?>">
			<?= $this->url->link($this->text->e($project['name']), 'BoardViewController', 'show', array('project_id' => $project['id'])) ?>
		</span>

		<?php if ($project['is_private']): ?>
			<i class="fa fa-lock fa-fw" title="<?= t('Personal project') ?>" role="img" aria-label="<?= t('Personal project') ?>"></i>
		<?php endif ?>

		<?= $this->hook->render('template:dashboard:project:after-title', array('project' => $project)) ?>

		<div class="project_identifier mt-3 <?= mb_strlen($project['identifier']) ?"":"empty" ?>"><?= mb_strlen($project['identifier']) ? $project['identifier'] : "&nbsp;" ?></div>
	</div>
