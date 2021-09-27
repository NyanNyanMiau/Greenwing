<?php if ( ! empty($task['subtasks']) ): ?>
	<?php $finished = 0; foreach($task['subtasks'] as $subtask){ if ($subtask['status'] == 2) $finished++; } ?>
	<span>
		<i class="fa fa-bars fa-fw"></i>
		<?=  round( $finished / count($task['subtasks']) * 100, 0) ,"%"; ?>
	</span>
	<vr class="mx-3"></vr>
<?php endif; ?>
    <?php if ($task['reference']): ?>
        <span class="task-board-reference" title="<?= t('Reference') ?>">
            <span class="ui-helper-hidden-accessible"><?= t('Reference') ?> </span><?= $this->task->renderReference($task) ?>
        </span>
        <vr class="mx-3"></vr>
    <?php endif ?>
    <?php if ($task['is_milestone'] == 1): ?>
        <span title="<?= t('Milestone') ?>">
            <i class="fa fa-flag flag-milestone" role="img" aria-label="<?= t('Milestone') ?>"></i>
        </span>
        <vr class="mx-3"></vr>
    <?php endif ?>

    <?php if ($task['score']): ?>
        <span class="task-score" title="<?= t('Complexity') ?>">
            <i class="fa fa-trophy" role="img" aria-label="<?= t('Complexity') ?>"></i>
        <?= $this->text->e($task['score']) ?>
        </span>
        <vr class="mx-3"></vr>
    <?php endif ?>

    <?php if ($task['recurrence_status'] == \Kanboard\Model\TaskModel::RECURRING_STATUS_PENDING): ?>
        <?= $this->app->tooltipLink('<i class="fa fa-refresh fa-rotate-90"></i>', $this->url->href('BoardTooltipController', 'recurrence', array('task_id' => $task['id'], 'project_id' => $task['project_id']))) ?>
        <vr class="mx-3"></vr>
    <?php endif ?>

    <?php if ($task['recurrence_status'] == \Kanboard\Model\TaskModel::RECURRING_STATUS_PROCESSED): ?>
        <?= $this->app->tooltipLink('<i class="fa fa-refresh fa-rotate-90 fa-inverse"></i>', $this->url->href('BoardTooltipController', 'recurrence', array('task_id' => $task['id'], 'project_id' => $task['project_id']))) ?>
        <vr class="mx-3"></vr>
    <?php endif ?>

    <?php if (! empty($task['nb_links'])): ?>
        <?= $this->app->tooltipLink('<i class="fa fa-code-fork fa-fw"></i>'.$task['nb_links'], $this->url->href('BoardTooltipController', 'tasklinks', array('task_id' => $task['id'], 'project_id' => $task['project_id']))) ?>
        <vr class="mx-3"></vr>
    <?php endif ?>

    <?php if (! empty($task['nb_external_links'])): ?>
        <?= $this->app->tooltipLink('<i class="fa fa-external-link fa-fw"></i>'.$task['nb_external_links'], $this->url->href('BoardTooltipController', 'externallinks', array('task_id' => $task['id'], 'project_id' => $task['project_id']))) ?>
        <vr class="mx-3"></vr>
    <?php endif ?>

    <?php if (! empty($task['nb_files'])): ?>
        <?= $this->app->tooltipLink('<i class="fa fa-paperclip fa-fw"></i>'.$task['nb_files'], $this->url->href('BoardTooltipController', 'attachments', array('task_id' => $task['id'], 'project_id' => $task['project_id']))) ?>
        <vr class="mx-3"></vr>
    <?php endif ?>

    <?php if ($task['nb_comments'] > 0): ?>
        <?php if ($this->user->hasProjectAccess('TaskModificationController', 'edit', $task['project_id'])): ?>
            <?= $this->modal->medium(
                'comments-o',
                $task['nb_comments'],
                'CommentListController',
                'show',
                array('task_id' => $task['id'], 'project_id' => $task['project_id']),
                $task['nb_comments'] == 1 ? t('%d comment', $task['nb_comments']) : t('%d comments', $task['nb_comments'])
            ) ?>
        <?php else: ?>
            <?php $aria_label = $task['nb_comments'] == 1 ? t('%d comment', $task['nb_comments']) : t('%d comments', $task['nb_comments']); ?>
            <span title="<?= $aria_label ?>" role="img" aria-label="<?= $aria_label ?>"><i class="fa fa-comments-o"></i>&nbsp;<?= $task['nb_comments'] ?></span>
        <?php endif ?>
        <vr class="mx-3"></vr>
    <?php endif ?>

    <?= $this->task->renderPriority($task['priority']) ?>

    <?php if (isset($task['tags'])):?>
    <vr class="mx-3"></vr>
    <?php foreach ($task['tags'] as $tag): ?>
        <span class="table-list-category task-list-tag <?= $tag['color_id'] ? "color-{$tag['color_id']}" : '' ?>">
            <?= $this->text->e($tag['name']) ?>
        </span>
    <?php endforeach ?>
    <?php endif ?>
