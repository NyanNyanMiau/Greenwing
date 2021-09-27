<span class="duration">
<?php if ($icon_pos === 'start'): ?>
	<i class="fa fa-calendar me-2" role="img"></i>
<?php endif; ?>
<?php if ( $project['start_date'] ): ?>
  <span><?= $this->dt->date($project['start_date']) ?></span>
<?php endif; ?>
-
<?php if ( $project['end_date'] ): ?>
  <span title="<?= t('Due date') ?>"
    class="task-date
    <?php if ( date('Y-m-d') > date('Y-m-d', strtotime($project['end_date']) )): ?>
      task-date-overdue
    <?php elseif (date('Y-m-d') == date('Y-m-d', strtotime($project['end_date']))): ?>
      task-date-today
    <?php endif ?>
    ">
    <?= $this->dt->date($project['end_date']) ?>
  </span>
<?php endif; ?>

<?php if ($icon_pos === 'end'): ?>
	<?php if ( $project['start_date'] || $project['end_date'] ): ?>
		<i class="fa fa-calendar ms-2" role="img"></i>
	<?php endif; ?>
<?php endif; ?>
</span>