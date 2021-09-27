<div class="page-header">
    <h2 class="pb-1"><?= $this->text->e($project['name']) ?></h2>
    <?php if (!isset($is_public) || !$is_public): ?>
        <ul>
            <li>
                <?= $this->url->icon('sort', t('Change sorting'), 'MyDashboardController', 'project_comment_list__toggle_sorting', array( 'project_id' => $project['id'], 'plugin' => 'Greenwing'), false, 'js-modal-replace') ?>
            </li>
        </ul>
    <?php endif ?>
</div>
<div class="comments mt-3">
    <?php foreach ($comments as $comment): ?>
    <p class="ms-3">
        <span class=" <?= $comment['task_is_active'] == 0 ? 'status-closed' : '' ?>">
            <?= $this->url->link('#'. $comment['task_id'] .' '. $this->text->e($comment['task_title']), 'TaskViewController', 'show', array('project_id' => $project['id'], 'task_id' => $comment['task_id'])) ?>
        </span>
    </p>
        <?= $this->render('comment/show', array(
            'comment'   => $comment,
            'task'      => [
            		'id' => $comment['task_id'],
            		'project_id' => $project['id']
        	],
            'project'   => $project,
            'editable'  => true,
            'is_public' => isset($is_public) && $is_public,
        )) ?>
    <?php endforeach ?>
</div>
