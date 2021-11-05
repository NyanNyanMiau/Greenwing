<?php
namespace Kanboard\Plugin\Greenwing\Helper;

use Kanboard\Core\Base;
use Kanboard\Model\TaskModel;
use Kanboard\Model\ProjectModel;
use Kanboard\Model\CommentModel;
use Kanboard\Model\SubtaskModel;
use Kanboard\Model\TaskLinkModel;
use Kanboard\Model\TaskFileModel;
use Kanboard\Model\TaskExternalLinkModel;
use Kanboard\Model\ColumnModel;
use Kanboard\Formatter\TaskListFormatter;
use Kanboard\Model\UserMetadataModel;

class DashboardHelper extends Base
{

    /*
     * Task should finish today
     */
    public function getProjectUserDueCount(array $project, array $user)
    {
        // due is set, check if in due end period
        $futureDue = abs(intval($this->projectMetadataModel->get($projectId, 'DueDate_Board_Distant_Future')));
        if ($futureDue == 0) {
            $futureDue = 3;
        }
        $futureDueTime = strtotime('+' . strval($futureDue) . 'days');

        // ->lte(TaskModel::TABLE.'.date_due', $futureDueTime)

        $task =  $this->db->table(TaskModel::TABLE)
                ->join(ProjectModel::TABLE, 'id', 'project_id')
                ->eq(TaskModel::TABLE . '.project_id', $project['id'])
                ->eq(TaskModel::TABLE . '.owner_id', $user['id'])
                ->eq(ProjectModel::TABLE . '.is_active', 1)
                ->eq(TaskModel::TABLE . '.is_active', 1)
                ->neq(TaskModel::TABLE . '.date_due', 0)
                ->gt(TaskModel::TABLE . '.date_due', strtotime('today midnight'))
                ->lt(TaskModel::TABLE . '.date_due', $futureDueTime)    // strtotime('tomorrow midnight')
                ->count();

       $subtasks =  $this->db->table(SubtaskModel::TABLE)
                    ->join(TaskModel::TABLE, 'id', 'task_id')
                    ->join(ProjectModel::TABLE, 'id', 'project_id', TaskModel::TABLE)
                    ->join(ColumnModel::TABLE, 'id', 'column_id', TaskModel::TABLE)
                    ->eq(TaskModel::TABLE . '.project_id', $project['id'])
                    ->eq(SubtaskModel::TABLE . '.user_id', $user['id'])
                    ->eq(ProjectModel::TABLE . '.is_active', 1)
                    ->eq(TaskModel::TABLE . '.is_active', 1)
                    ->eq(ColumnModel::TABLE . '.hide_in_dashboard', 0)
                    ->in(SubtaskModel::TABLE . '.status', [0,1])

                    ->neq(SubtaskModel::TABLE . '.due_date', 0)
                    ->gt(SubtaskModel::TABLE . '.due_date', strtotime('today midnight'))
                    ->lte(SubtaskModel::TABLE . '.due_date', $futureDueTime)
                    ->groupBy(SubtaskModel::TABLE.'.id')
                    ->count();

       return $tasks+$subtasks;
    }
    /*
     * return users overdue tasks
     *
     * */
    public function getProjectUserOverdueCount(array $project, array $user)
    {
        $tasks  = $this->db->table(TaskModel::TABLE)
                ->join(ProjectModel::TABLE, 'id', 'project_id')
                ->join(ColumnModel::TABLE, 'id', 'column_id')
                ->eq(ProjectModel::TABLE.'.is_active', 1)
                ->eq(TaskModel::TABLE.'.is_active', 1)
                ->eq(TaskModel::TABLE . '.project_id', $project['id'])
                ->eq(TaskModel::TABLE . '.owner_id', $user['id'])
                ->eq(ColumnModel::TABLE . '.hide_in_dashboard', 0)

                ->neq(TaskModel::TABLE.'.date_due', 0)
                ->lte(TaskModel::TABLE.'.date_due', time())
                ->count();

        $subtasks =  $this->db->table(SubtaskModel::TABLE)
                ->join(TaskModel::TABLE, 'id', 'task_id')
                ->join(ProjectModel::TABLE, 'id', 'project_id', TaskModel::TABLE)
                ->join(ColumnModel::TABLE, 'id', 'column_id', TaskModel::TABLE)
                ->eq(TaskModel::TABLE . '.project_id', $project['id'])
                ->eq(SubtaskModel::TABLE . '.user_id', $user['id'])
                ->eq(ColumnModel::TABLE . '.hide_in_dashboard', 0)
                ->eq(ProjectModel::TABLE . '.is_active', 1)
                ->eq(TaskModel::TABLE . '.is_active', 1)
                ->in(SubtaskModel::TABLE . '.status', [0,1])

                ->neq(SubtaskModel::TABLE . '.due_date', 0)
                ->lte(SubtaskModel::TABLE . '.due_date', time()) // strtotime('today midnight')
                ->groupBy(SubtaskModel::TABLE.'.id')
                ->count();

        return $tasks+$subtasks;
    }


    public function getProjectDueCount(array $project)
    {
        // due is set, check if in due end period
        $futureDue = abs(intval($this->projectMetadataModel->get($project['id'], 'DueDate_Board_Distant_Future')));
        if (!$futureDue) { $futureDue = 3; }
        $futureDueTime = strtotime('+' . strval($futureDue) . 'days');

        // ->lte(TaskModel::TABLE.'.date_due', $futureDueTime)

        $tasks =  $this->db->table(TaskModel::TABLE)
                    ->join(ProjectModel::TABLE, 'id', 'project_id')
                    ->join(ColumnModel::TABLE, 'id', 'column_id')
                    ->eq(TaskModel::TABLE . '.project_id', $project['id'])
                    ->eq(ProjectModel::TABLE . '.is_active', 1)
                    ->eq(TaskModel::TABLE . '.is_active', 1)
                    ->eq(ColumnModel::TABLE . '.hide_in_dashboard', 0)

                    ->neq(TaskModel::TABLE . '.date_due', 0)
                    ->gt(TaskModel::TABLE . '.date_due', strtotime('today midnight'))
                    ->lte(TaskModel::TABLE . '.date_due', $futureDueTime)
                    ->count();

        $subtasks =  $this->db->table(SubtaskModel::TABLE)
                    ->join(TaskModel::TABLE, 'id', 'task_id')
                    ->join(ProjectModel::TABLE, 'id', 'project_id', TaskModel::TABLE)
                    ->join(ColumnModel::TABLE, 'id', 'column_id', TaskModel::TABLE)
                    ->eq(TaskModel::TABLE . '.project_id', $project['id'])
                    ->eq(ProjectModel::TABLE . '.is_active', 1)
                    ->eq(TaskModel::TABLE . '.is_active', 1)
                    ->eq(ColumnModel::TABLE . '.hide_in_dashboard', 0)
                    ->in(SubtaskModel::TABLE . '.status', [0,1])

                    ->neq(SubtaskModel::TABLE . '.due_date', 0)
                    ->gt(SubtaskModel::TABLE . '.due_date', strtotime('today midnight'))
                    ->lte(SubtaskModel::TABLE . '.due_date', $futureDueTime)
                    ->groupBy(SubtaskModel::TABLE.'.id')
                    ->count();

        return $tasks+$subtasks;
    }

    public function getProjectOverdueCount(array $project)
    {
        $tasks  = $this->db->table(TaskModel::TABLE)
                    ->join(ProjectModel::TABLE, 'id', 'project_id')
                    ->join(ColumnModel::TABLE, 'id', 'column_id')
                    ->eq(ProjectModel::TABLE.'.is_active', 1)
                    ->eq(TaskModel::TABLE.'.is_active', 1)
                    ->eq(TaskModel::TABLE . '.project_id', $project['id'])
                    ->eq(ColumnModel::TABLE . '.hide_in_dashboard', 0)

                    ->neq(TaskModel::TABLE.'.date_due', 0)
                    ->lte(TaskModel::TABLE.'.date_due', time())
                    ->count();

        $subtasks =  $this->db->table(SubtaskModel::TABLE)
                    ->join(TaskModel::TABLE, 'id', 'task_id')
                    ->join(ProjectModel::TABLE, 'id', 'project_id', TaskModel::TABLE)
                    ->join(ColumnModel::TABLE, 'id', 'column_id', TaskModel::TABLE)
                    ->eq(TaskModel::TABLE . '.project_id', $project['id'])
                    ->eq(ColumnModel::TABLE . '.hide_in_dashboard', 0)
                    ->eq(ProjectModel::TABLE . '.is_active', 1)
                    ->eq(TaskModel::TABLE . '.is_active', 1)
                    ->in(SubtaskModel::TABLE . '.status', [0,1])

                    ->neq(SubtaskModel::TABLE . '.due_date', 0)
                    ->lte(SubtaskModel::TABLE . '.due_date', time()) // strtotime('today midnight')
                    ->groupBy(SubtaskModel::TABLE.'.id')
                    ->count();

     return $tasks+$subtasks;
    }

    public function getProjectTaskSubtaskCount(array $user)
    {
        if ( $this->userSession->isAdmin() ) {
            $projects = $this->projectModel->getListByStatus( 1 );
            $adminOverview = true;
        } else {
            $projects = $this->projectUserRoleModel->getActiveProjectsByUser($user["id"]);
            $adminOverview = false;
        }

        // start default
        $q =  $this->taskFinderModel->getExtendedQuery()
                ->eq(ProjectModel::TABLE . '.is_active', 1)
                ->eq(TaskModel::TABLE . '.is_active', 1)
                ->eq(ColumnModel::TABLE . '.hide_in_dashboard', 0)
                ->in(ProjectModel::TABLE.'.id', array_keys($projects));

        $qs = clone $q;

        // subtask count
        $qs ->columns(
                'count( distinct ' . SubtaskModel::TABLE . '.id ) as count',
                '"subtask_cnt" as type'
            )
            ->join(SubtaskModel::TABLE, 'task_id', 'id');

        // taskcount
        $q->columns(
                'count( distinct ' . TaskModel::TABLE . '.id ) as count',
                '"task_cnt" as type'
            )
            ->union($qs);

        $result = $q->findAll();
        $return = [];
        foreach ($result as $row){
            $return[ $row['type'] ] = $row['count'];
        }

        return $return;
    }

    public function getProjectTaskList(array $project, $forcedTaskFilter = null)
    {
        $taskFilter = strlen($forcedTaskFilter) ? $forcedTaskFilter : $this->helper->dashboardHelper->getFilterForMyTasks();
        $taskStatus = $taskFilter==='closed' ? TaskModel::STATUS_CLOSED : TaskModel::STATUS_OPEN;
        $subtaskStatus = $taskFilter==='closed' ? [2] : [0,1];

        // status filter - closed // no due / due / overdue / all
        $query = $this->taskFinderModel->getExtendedQuery()
                    ->subquery('group_concat('.SubtaskModel::TABLE.'.id)', 'subtask_ids')

                    ->eq(TaskModel::TABLE . '.project_id', $project['id'])
                    ->eq(ProjectModel::TABLE . '.is_active', 1)
                    ->eq(TaskModel::TABLE . '.is_active', $taskStatus)
                    ->eq(ColumnModel::TABLE . '.hide_in_dashboard', 0)

                    ->join(SubtaskModel::TABLE, 'task_id', 'id', TaskModel::TABLE)
                    ->beginOr()
                        ->in(SubtaskModel::TABLE . '.status', $subtaskStatus)
                        ->isNull(SubtaskModel::TABLE . '.status')
                    ->closeOr()

                    ->groupBy(TaskModel::TABLE.'.id')
                    ->orderBy(TaskModel::TABLE . '.id');

        $this->addTasksFilter($query, $taskFilter);

//                echo $query->buildSelectQuery(); die();

        $tasks = $query->findAll();
        foreach ($tasks as &$task)
        {
            $task['subtasks'] = [];
            if ( strlen($task['subtask_ids']) )
            {
                $q = $this->subtaskModel->getQuery()->in(SubtaskModel::TABLE.'.id', explode(',' , $task['subtask_ids']));
                $subtasks = $this->subtaskListFormatter->withQuery($q)->format();
                $task['subtasks'] = $subtasks;
            }
        }

        return $tasks;

//         return $this->taskListSubtaskFormatter->withQuery($query)
//                 ->format();
    }


    public function getProjectUserTaskSubtaskCount(array $user)
    {
//         $taskFilter = strlen($forcedTaskFilter) ? $forcedTaskFilter : $this->helper->dashboardHelper->getFilterForMyTasks();
//         $taskStatus = $taskFilter==='closed' ? TaskModel::STATUS_CLOSED : TaskModel::STATUS_OPEN;

        // start default
        $q =  $this->taskFinderModel->getExtendedQuery()
                ->eq(ProjectModel::TABLE . '.is_active', 1)
                ->eq(TaskModel::TABLE . '.is_active', 1)
                ->eq(ColumnModel::TABLE . '.hide_in_dashboard', 0);

       $qs = clone $q;

       // subtask count
       $qs ->columns(
                'count( distinct ' . SubtaskModel::TABLE . '.id ) as count',
                '"subtask_cnt" as type'
           )
           ->join(SubtaskModel::TABLE, 'task_id', 'id')
           ->eq(SubtaskModel::TABLE . '.user_id', $user['id'])
           ->lt(SubtaskModel::TABLE . '.status', 2);

       // taskcount
       $q->columns(
             'count( distinct ' . TaskModel::TABLE . '.id ) as count',
               '"task_cnt" as type'
           )
           ->eq(TaskModel::TABLE . '.owner_id', $user['id'])
           ->union($qs);

//          echo $q->buildSelectQuery(); die();

           $result = $q->findAll();
           $return = [];
           foreach ($result as $row){
               $return[ $row['type'] ] = $row['count'];
           }

           return $return;
    }


    function addTasksFilter(&$query, $taskFilter='')
    {
        $defaultFutureDue = 3;
        $time = time();

        if ($taskFilter === 'nodue')
        {
            $query
                    ->beginOr()
                        ->eq(SubtaskModel::TABLE.'.due_date', 0)
                        ->isNull(SubtaskModel::TABLE.'.due_date')
                    ->closeOr()
                    ->beginOr()
                        ->eq(TaskModel::TABLE.'.date_due', 0)
                        ->eq(SubtaskModel::TABLE.'.due_date', 0)
                    ->closeOr();
            return;
        }

        if ($taskFilter === 'due')
        {
            // due is set, check if in due end period

            $query  ->join('project_has_metadata', 'project_id', 'project_id')
                    ->beginOr()
                        ->eq('project_has_metadata.name', 'DueDate_Board_Distant_Future')
                        ->isNull('project_has_metadata.name')
                    ->closeOr()

                    ->beginOr()
                        ->isNull( SubTaskModel::TABLE . '.due_date')
                        ->addCondition( SubTaskModel::TABLE . '.due_date != 0 AND ' . SubTaskModel::TABLE . '.due_date > '. $time .'
                                AND '. SubTaskModel::TABLE . '.due_date <= '. $time . " + (86400 * COALESCE(project_has_metadata.value , $defaultFutureDue))" )
                    ->closeOr()

                    ->beginOr()
                        ->addCondition( TaskModel::TABLE . '.date_due != 0 AND ' . TaskModel::TABLE . '.date_due > '. $time .'
                                AND '. TaskModel::TABLE . '.date_due <= '. $time . " + (86400 * COALESCE(project_has_metadata.value , $defaultFutureDue))" )
                        ->addCondition( SubTaskModel::TABLE . '.due_date != 0 AND ' . SubTaskModel::TABLE . '.due_date > '. $time .'
                                AND '. SubTaskModel::TABLE . '.due_date <= '. $time . " + (86400 * COALESCE(project_has_metadata.value , $defaultFutureDue))" )
                    ->closeOr();
            return;
        }

        if ($taskFilter === 'overdue')
        {
            $query
                    ->beginOr()
                        ->isNull( SubTaskModel::TABLE . '.due_date')
                        ->addCondition( SubtaskModel::TABLE.'.due_date != 0 AND '. SubtaskModel::TABLE.'.due_date <= '. $time)
                    ->closeOr()
                    ->beginOr()
                        ->addCondition( TaskModel::TABLE . '.date_due != 0 AND ' . TaskModel::TABLE . '.date_due <= '. $time)
                        ->addCondition( SubTaskModel::TABLE . '.due_date != 0 AND ' . SubtaskModel::TABLE . '.due_date <= '. $time)
                    ->closeOr();

//             echo $query->buildSelectQuery(); die();
            return;
        }
    }


//     function addSubtasksFilter(&$query, $taskFilter='')
//     {

//     }

    public function getProjectUserTaskList(array $project, array $user, $forcedTaskFilter=null)
    {
        $userId = (int) $user['id'];
        $taskFilter = strlen($forcedTaskFilter) ? $forcedTaskFilter : $this->helper->dashboardHelper->getFilterForMyTasks();
        $taskStatus = $taskFilter==='closed' ? TaskModel::STATUS_CLOSED : TaskModel::STATUS_OPEN;
        $subtaskStatus = $taskFilter==='closed' ? [2] : [0,1];

        // status filter - closed // no due / due / overdue / all
        $query = $this->taskFinderModel->getExtendedQuery()

                    ->subquery('group_concat('.SubtaskModel::TABLE.'.id)', 'subtask_ids')

                    ->eq(TaskModel::TABLE . '.project_id', $project['id'])
                    ->eq(ProjectModel::TABLE . '.is_active', 1)
                    ->eq(TaskModel::TABLE . '.is_active', $taskStatus)
                    ->eq(ColumnModel::TABLE . '.hide_in_dashboard', 0)

                    ->join(SubtaskModel::TABLE, 'task_id', 'id', TaskModel::TABLE)
                    ->beginOr()
                        ->in(SubtaskModel::TABLE . '.status', $subtaskStatus)
                        ->isNull(SubtaskModel::TABLE . '.status')
                    ->closeOr()

                    ->beginOr()
                        ->eq(SubtaskModel::TABLE.'.user_id', $userId)
                        ->isNull(SubtaskModel::TABLE.'.user_id')
                    ->closeOr()

                    ->beginOr()
                        ->eq(TaskModel::TABLE . '.owner_id', $userId)
                        ->eq(SubtaskModel::TABLE . '.user_id', $userId)
                    ->closeOr()

                    ->groupBy(TaskModel::TABLE.'.id')
                    ->orderBy(TaskModel::TABLE . '.id');

       $this->helper->dashboardHelper->addTasksFilter($query, $taskFilter);

//        echo $query->buildSelectQuery(); die();

       $tasks = $query->findAll();
       foreach ($tasks as &$task)
       {
           $task['subtasks'] = [];
           if ( strlen($task['subtask_ids']) )
           {
               $q = $this->subtaskModel->getQuery()->in(SubtaskModel::TABLE.'.id', explode(',' , $task['subtask_ids']));
               $subtasks = $this->subtaskListFormatter->withQuery($q)->format();
               $task['subtasks'] = $subtasks;
           }
       }

       return $tasks;

//         return $this->taskListSubtaskAssigneeFormatter->withQuery($query)
//                     ->withUserId($userId)
//                     ->format();
    }


    public function getFilterForMyTasks()
    {
        return $this->userMetadataCacheDecorator->get('MyTasksFilter', 'all');
    }

    public function changeFilterForMyTasks($newFilter)
    {
        $this->userMetadataCacheDecorator->set('MyTasksFilter', $newFilter);
    }

    public function commentToggleSorting()
    {
        $oldDirection = $this->userMetadataCacheDecorator->get('DashboardCommentsSortingOrder', 'ASC');
        $newDirection = $oldDirection === 'ASC' ? 'DESC' : 'ASC';

        $this->userMetadataCacheDecorator->set('DashboardCommentsSortingOrder', $newDirection);
    }


}