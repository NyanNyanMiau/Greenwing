<?php if ($this->user->hasProjectAccess('TaskModificationController', 'edit', $task['project_id'])): ?>
<span class="detail-action">
    <?= $this->modal->medium('plus', t('Add a sub-task'), 'SubtaskController', 'create', array('task_id' => $task['id'], 'project_id' => $task['project_id'])) ?>
</span>
<?php endif; ?>