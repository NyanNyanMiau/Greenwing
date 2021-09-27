<?php
$comment = $data;
$editable = isset($editable) ? $editable : true;
$is_public = isset($is_public) ? $is_public : true;
$preview = isset($preview) ? $preview : false;

?>

<div class="comment <?= isset($preview) ? 'comment-preview' : '' ?>" id="comment-<?= $comment['id'] ?>">

    <?= $this->myAvatarHelper->small($comment['user_id'], $comment['username'], $comment['user_name'], $comment['email'], $comment['avatar_path'], 'me-4') ?>

    <div class="comment-description">
        <div class="comment-title">
            <span class=" <?= $comment['task_is_active'] == 0 ? 'status-closed' : '' ?>">
                <?= $this->url->icon('tasks me-2','#'. $comment['task_id'] .' '. $this->text->e($comment['task_title']), 'TaskViewController', 'show', array('task_id' => $comment['task_id'])) ?>
            </span>
            <span class="ms-5 <?= $comment['task_is_active'] == 0 ? 'status-closed' : '' ?>">
                <?= $this->url->icon('folder me-2', '#'. $comment['project_id'] .' '. $this->text->e($comment['project_name']), 'ProjectViewController', 'show', array('project_id' => $comment['project_id'])) ?>
            </span>
            <span class="comment-date ms-auto" title="<?= t('Updated at:') ?> <?= $this->dt->datetime($comment['date_modification']) ?>"><?= $this->dt->datetime($comment['date_creation']) ?></span>
            <?= $this->url->icon('share', '', 'TaskViewController', 'show', array('task_id' => $comment['task_id'], 'project_id' => $comment['project_id']), false, '', '', $this->app->isAjax(), 'comment-'.$comment['id']) ?>
        </div>
        <div class="comment-content">
            <div class="markdown">
                <?= $this->text->markdown($comment['comment'], isset($is_public) && $is_public) ?>
            </div>
        </div>
    </div>
</div>
