<div class="page-header">
    <h2 class="pb-1"><?= t('Comments') ?></h2>
</div>

<div class="panel d-flex bg-white p-3">
    <?= $this->url->link('<span class="fa-stack">
                            <i class="fa fa-sort fa-stack-1x text-lightgray"></i>
                            <i class="fa fa-fw '.($sorting_dir==='ASC'?'fa-sort-up':'fa-sort-down').' fa-stack-1x"></i>
                          </span> '. t('Change sorting'), 'MyDashboardController', 'dashboard_comments__toggle_sorting', array('user_id' => $user['id'], 'plugin' => 'Greenwing'), false) ?>
    <? if ( count($projects) > 1): ?>
    	<div class="dropdown d-flex ms-auto align-items-center">
            <a href="#" class="dropdown-menu dropdown-menu-link-text dropdown-menu-link-icon"><i class="fa fa-caret-down me-2"></i> <?= $project_id ? $projects[$project_id] : t('filter by project') ?></a>
            <ul class="show-icons" >
            <?php foreach ($projects as $id => $title): ?>
                <li>
                	<i class="fa fa-fw me-2 <?= ($project_id == $id) ? 'fa-check':'' ?>"></i>
                	 <?= $this->url->link( $this->text->e($title), 'MyDashboardController', 'comments', array('project_id' => $id,'user_id' => $user['id'], 'plugin' => 'Greenwing')) ?></li>
            <?php endforeach; ?>
            </ul>

        </div>
		<?php if ($project_id): ?>
    	    <?= $this->url->icon('close', '', 'MyDashboardController', 'comments', array('user_id' => $user['id'], 'plugin' => 'Greenwing'), '', ' ms-2 p-1') ?>
	    <?php endif; ?>
    <?php endif; ?>
</div>

<div class="comments dashboard-comments mt-3 py-4">

	<?php if ( empty($result) ):?>
		<p class="alert m-3"><?= t('Nothing found.') ?></p>
	<?php else: ?>
    	<?php foreach ($result as $entry): ?>
    	<div class="entry">
    		<?= $this->render('Greenwing:dashboard/comments_'.$entry["type"], array ('data' => $entry ))?>
    	</div>
    	<?php endforeach ?>
	<?php endif; ?>
</div>
