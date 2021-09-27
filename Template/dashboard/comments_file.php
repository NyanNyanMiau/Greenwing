<?php
$file = $data;
$editable = isset($editable) ? $editable : true;
$is_public = isset($is_public) ? $is_public : true;
$preview = isset($preview) ? $preview : false;

?>

<div class="comment <?= isset($preview) ? 'comment-preview' : '' ?>" id="file-<?= $file['id'] ?>">

    <?= $this->myAvatarHelper->small($file['user_id'], $file['username'], $file['user_name'], $file['email'], $file['avatar_path'], 'me-4') ?>

    <div class="comment-description">

        <div class="comment-title">
            <span class=" <?= $file['task_is_active'] == 0 ? 'status-closed' : '' ?>">
                <?= $this->url->icon('tasks me-2','#'. $file['task_id'] .' '. $this->text->e($file['task_title']), 'TaskViewController', 'show', array('task_id' => $file['task_id'])) ?>
            </span>
            <span class="ms-5 <?= $file['task_is_active'] == 0 ? 'status-closed' : '' ?>">
                <?= $this->url->icon('folder me-2', '#'. $file['project_id'] .' '. $this->text->e($file['project_name']), 'ProjectViewController', 'show', array('project_id' => $file['project_id'])) ?>
            </span>

            <span class="comment-date ms-auto"><?= $this->dt->datetime($file['date']) ?></span>
		</div>

		<div class="comment-content">

			<div class="thumbnail">
				<img src=<?= $this->url->to('FileViewerController', 'thumbnail', array('file_id' => $file['id'], 'project_id' => $file['project_id'], 'task_id' => $file['task_id'])) ?> />
			</div>

			<div>
			    <div class="dropdown">
                    <a href="#" class="dropdown-menu dropdown-menu-link-text"><?= $this->text->e($file['name']) ?> <i class="fa fa-caret-down"></i></a>
                    <ul>
                        <li>
                            <?= $this->url->icon('external-link', t('View file'), 'FileViewerController', 'image', array('task_id' => $file['task_id'], 'project_id' => $file['project_id'], 'file_id' => $file['id']), false, '', '', true) ?>
                        </li>
                        <li>
                            <?= $this->url->icon('download', t('Download'), 'FileViewerController', 'download', array('task_id' => $file['task_id'], 'project_id' => $file['project_id'], 'file_id' => $file['id'])) ?>
                        </li>
                        <?php if ($this->user->hasProjectAccess('TaskFileController', 'remove', $file['project_id'])): ?>
                            <li>
                                <?= $this->modal->confirm('trash-o', t('Remove'), 'TaskFileController', 'confirm', array('task_id' => $file['task_id'], 'project_id' => $file['project_id'], 'file_id' => $file['id'])) ?>
                            </li>
                        <?php endif ?>
                    </ul>
                </div>
                <small class="ms-3">( <?= $this->text->bytes($file['size']) ?> )</small>
			</div>
		</div>
	</div>
</div>
