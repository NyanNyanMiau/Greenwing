  <?php global $GreenwingConfig; ?><h1>
    <span class="logo">
        <?php /* <?= $this->url->link('K<span>B</span>', 'DashboardController', 'show', array(), false, '', t('Dashboard')) ?> */ ?>
        <?= $this->url->link('<img src="'.$this->url->dir().'plugins/Greenwing/'.$GreenwingConfig['logo'].'" />', 'MyDashboardController', 'show', array('plugin' => 'Greenwing'), false, '', t('Dashboard')) ?>
    </span>
    <span class="title">
        <?php if (! empty($project) && ! empty($task)): ?>
            <?= $this->url->link($this->text->e($project['name']), 'BoardViewController', 'show', array('project_id' => $project['id'])) ?>
        <?php else: ?>
            <?= $this->text->e($title) ?>
        <?php endif ?>
    </span>
	<?php if (! empty($description)): ?>
		<?= $this->app->tooltipHTML($description) ?>
	<?php endif ?>
</h1>