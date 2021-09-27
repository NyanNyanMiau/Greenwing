<div class="sidebar">
    <ul>

<?php if ( $this->user->isAdmin() ): ?>
        <li <?= $this->app->checkMenuSelection('MyDashboardController', 'show', 'Greenwing') ?>>
            <?= $this->url->icon('screen', t('Project overview'), 'MyDashboardController', 'show', array('plugin' => 'Greenwing')) ?>
        </li>
<?php endif ?>

        <li <?= $this->app->checkMenuSelection('MyDashboardController', 'projects', 'Greenwing') ?>>
            <?= $this->url->icon('folder', t('My projects'), 'MyDashboardController', 'projects', array('user_id' => $user['id'], 'plugin' => 'Greenwing')) ?>
        </li>


<?php if ( $this->user->isAdmin() ): ?>
        <li <?= $this->app->checkMenuSelection('MyDashboardController', 'tasksoverview', 'Greenwing') ?>>
            <?= $this->url->icon('tasks', t('Task overview'), 'MyDashboardController', 'tasksoverview', array('plugin' => 'Greenwing')) ?>
        </li>
<?php endif ?>


        <li <?= $this->app->checkMenuSelection('MyDashboardController', 'tasks', 'Greenwing') ?>>
            <?= $this->url->icon('tasks', t('My tasks'), 'MyDashboardController', 'tasks', array('user_id' => $user['id'], 'plugin' => 'Greenwing')) ?>
        </li>
        <li <?= $this->app->checkMenuSelection('MyDashboardController', 'duetasks', 'Greenwing') ?>>
            <?= $this->url->icon('tasks-due', t('Due tasks'), 'MyDashboardController', 'duetasks', array('user_id' => $user['id'], 'plugin' => 'Greenwing')) ?>
        </li>
        <li <?= $this->app->checkMenuSelection('MyDashboardController', 'overduetasks', 'Greenwing') ?>>
            <?= $this->url->icon('tasks-overdue', t('Overdue tasks'), 'MyDashboardController', 'overduetasks', array('user_id' => $user['id'], 'plugin' => 'Greenwing')) ?>
        </li>

        <li <?= $this->app->checkMenuSelection('MyDashboardController', 'comments', 'Greenwing') ?>>
            <?= $this->url->icon('bullhorn', t('Comments'), 'MyDashboardController', 'comments', array('user_id' => $user['id'], 'plugin' => 'Greenwing')) ?>
        </li>

<!--
		<li><hr></li>
        <li <?= $this->app->checkMenuSelection('DashboardController', 'show') ?>>
            <?= $this->url->link(t('Overview'), 'DashboardController', 'show', array('user_id' => $user['id'])) ?>
        </li>

        <li <?= $this->app->checkMenuSelection('DashboardController', 'projects') ?>>
            <?= $this->url->link(t('My projects'), 'DashboardController', 'projects', array('user_id' => $user['id'])) ?>
        </li>
        <li <?= $this->app->checkMenuSelection('DashboardController', 'tasks') ?>>
            <?= $this->url->link(t('My tasks'), 'DashboardController', 'tasks', array('user_id' => $user['id'])) ?>
        </li>
        <li <?= $this->app->checkMenuSelection('DashboardController', 'subtasks') ?>>
            <?= $this->url->link(t('My subtasks'), 'DashboardController', 'subtasks', array('user_id' => $user['id'])) ?>
        </li>
 -->
        <?= $this->hook->render('template:dashboard:sidebar', array('user' => $user)) ?>
    </ul>
</div>
