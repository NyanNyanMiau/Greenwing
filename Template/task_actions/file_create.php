<?php if ($this->user->hasProjectAccess('TaskModificationController', 'edit', $task['project_id'])): ?>
<span class="detail-action">
	<?= $this->modal->medium('file', t('Attach a document'), 'TaskFileController', 'create', array('task_id' => $task['id'], 'project_id' => $task['project_id'])) ?>
</span>
<?php endif; ?>