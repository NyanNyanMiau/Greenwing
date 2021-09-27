<?php
$activeFilter = $this->dashboardHelper->getFilterForMyTasks( $user_id );
?>

<?php
    $k = 'closed';
    if ($activeFilter===$k){
        echo '<span class="btn btn-light active">'.t($k).'</span>';
    } else {
        echo $this->url->link( t($k), 'MyDashboardController', 'changeFilterForMyTasks',
            array( 'user_id' => $user_id, 'task_filter' => $k, 'referrer' => $ref,'plugin' => 'Greenwing' ), '', 'btn btn-light' );
    }
?>

<?php
    $k = 'nodue';
    if ($activeFilter===$k){
        echo '<span class="btn btn-light active">'.t($k).'</span>';
    } else {
        echo $this->url->link( t($k), 'MyDashboardController', 'changeFilterForMyTasks',
            array( 'user_id' => $user_id, 'task_filter' => $k, 'referrer' => $ref, 'plugin' => 'Greenwing' ), '', 'btn btn-light' );
    }
?>

<?php
    $k = 'due';
    if ($activeFilter===$k){
        echo '<span class="btn btn-light active">'.t($k).'</span>';
    } else {
        echo $this->url->link( t($k), 'MyDashboardController', 'changeFilterForMyTasks',
            array( 'user_id' => $user_id, 'task_filter' => $k, 'referrer' => $ref, 'plugin' => 'Greenwing' ), '', 'btn btn-light' );
    }
?>

<?php
    $k = 'overdue';
    if ($activeFilter===$k){
        echo '<span class="btn btn-light active">'.t($k).'</span>';
    } else {
        echo $this->url->link( t($k), 'MyDashboardController', 'changeFilterForMyTasks',
            array( 'user_id' => $user_id, 'task_filter' => $k, 'referrer' => $ref, 'plugin' => 'Greenwing' ), '', 'btn btn-light' );
    }
?>

<?php
    $k = 'all';
    if ($activeFilter===$k){
        echo '<span class="btn btn-light active">'.t($k).'</span>';
    } else {
        echo $this->url->link( t($k), 'MyDashboardController', 'changeFilterForMyTasks',
            array( 'user_id' => $user_id, 'task_filter' => $k, 'referrer' => $ref, 'plugin' => 'Greenwing' ), '', 'btn btn-light' );
    }
?>