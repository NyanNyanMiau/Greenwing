<div class="tooltip">
    <table class="table-small">
        <tr>
        	<th><?= t('Files') ?></th>
        	<th><?= t('from') ?></th>
        	<th><?= t('Date') ?></th>
        	<th><?= t('Size') ?></th>
        	<th></th>
        </tr>
        <?php foreach ($files as $file):?>
        <tr>
            <td>
				<?= $this->url->icon('eye', $this->text->e($file['name']), 'FileViewerController', ($file['is_image'] == 1 ? 'image' : 'show'), array('task_id' => $file['task_id'], 'project_id' => $file['project_id'], 'file_id' => $file['id']), false, '', '', true) ?>
            </td>
            <td> <?php if (! empty($file['username'])): ?>
                    <?= $this->text->e($file['user_name'] ?: $file['username']) ?>
                <?php endif ?>
            </td>
            <td> <?= $this->dt->datetime($file['date']) ?> </td>
            <td> <?= $this->text->bytes($file['size']) ?> </td>
            <td>
                <?= $this->url->icon('download', '', 'FileViewerController', 'download', array('task_id' => $file['task_id'], 'project_id' => $file['project_id'], 'file_id' => $file['id'])) ?>
            </td>
        </tr>
        <?php endforeach ?>
    </table>
</div>
