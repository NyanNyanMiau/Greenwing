<?php
namespace Kanboard\Plugin\Greenwing\Pagination;

use Kanboard\Core\Base;
use Kanboard\Core\Paginator;
use Kanboard\Model\TaskModel;
use Kanboard\Model\SubtaskModel;
use Kanboard\Model\ProjectModel;
use Kanboard\Model\ColumnModel;

/**
 * Class TaskPagination
 *
 * @package Kanboard\Pagination
 * @author Frederic Guillot
 */
class MyTaskPagination extends Base
{

    /**
     * Get dashboard pagination
     *
     * @access public
     * @param integer $userId
     * @param string $method
     * @param integer $max
     * @return Paginator
     */
    public function getDashboardOverviewPaginator($userId, $method, $max, $forcedTaskFilter=null)
    {
        $taskFilter = strlen($forcedTaskFilter) ? $forcedTaskFilter : $this->helper->dashboardHelper->getFilterForMyTasks();
        $taskStatus = $taskFilter==='closed' ? TaskModel::STATUS_CLOSED : TaskModel::STATUS_OPEN;
        $subtaskStatus = $taskFilter==='closed' ? [2] : [0,1];
        $time = time();

        if ( $this->userSession->isAdmin() ) {
            $projects = $this->projectModel->getListByStatus(1);
            $adminOverview = true;
        } else {
            $projects = $this->projectUserRoleModel->getActiveProjectsByUser($userId);
            $adminOverview = false;
        }

        if (empty($projects)){
            $projects = [-1];
        }else{
            $projects = array_keys($projects);
        }

        $query = $this->db->table(TaskModel::TABLE)
            ->columns(
                ProjectModel::TABLE . '.*',
                '(select count(*) from ' . TaskModel::TABLE . ' where ' . TaskModel::TABLE . '.project_id = ' . ProjectModel::TABLE . '.id and ' . TaskModel::TABLE . '.is_active =' . TaskModel::STATUS_OPEN . ') as nb_active',
                '(select count(*) from ' . TaskModel::TABLE . ' where ' . TaskModel::TABLE . '.project_id = ' . ProjectModel::TABLE . '.id and ' . TaskModel::TABLE . '.is_active =' . TaskModel::STATUS_CLOSED . ') as nb_closed'
                )
                ->join(ProjectModel::TABLE, 'id', 'project_id')
                ->join(ColumnModel::TABLE, 'id', 'column_id', TaskModel::TABLE)
                ->join(SubtaskModel::TABLE, 'task_id', 'id', TaskModel::TABLE)

                ->beginOr()
                    ->in(SubtaskModel::TABLE . '.status', $subtaskStatus)
                    ->isNull(SubtaskModel::TABLE . '.status')
                ->closeOr()

                ->eq(ProjectModel::TABLE . '.is_active', ProjectModel::ACTIVE)
                ->eq(TaskModel::TABLE . '.is_active', $taskStatus)
                ->eq(ColumnModel::TABLE . '.hide_in_dashboard', 0)

                ->in(TaskModel::TABLE . '.project_id', $projects)
                ->groupBy(TaskModel::TABLE . '.project_id');

        $this->hook->reference('pagination:dashboard:projectlist:query', $query);

        $this->helper->dashboardHelper->addTasksFilter($query, $taskFilter);

//         echo $query->buildSelectQuery(); die();


        return $this->paginator->setUrl('MyDashboardController', $method, array(
                    'pagination' => 'tasksoverview',
                    'user_id' => $userId,
                    'plugin' => 'Greenwing'
                ))
                ->setMax($max)
                ->setOrder(TaskModel::TABLE.'.priority')
                ->setDirection('DESC')
                ->setQuery($query);
                    //                 ->setFormatter($this->taskListFormatter)
//                     ->calculateOnlyIf($this->request->getStringParam('pagination') === 'tasksoverview');
    }



    /**
     * Get dashboard pagination
     *
     * @access public
     * @param integer $userId
     * @param string $method
     * @param integer $max
     * @return Paginator
     */
    public function getDashboardPaginator($userId, $method, $max, $forcedTaskFilter=null)
    {
        $taskFilter = strlen($forcedTaskFilter) ? $forcedTaskFilter : $this->helper->dashboardHelper->getFilterForMyTasks();
        $taskStatus = $taskFilter==='closed' ? TaskModel::STATUS_CLOSED : TaskModel::STATUS_OPEN;
        $subtaskStatus = $taskFilter==='closed' ? [2] : [0,1];

        $time = time();

        // users tasks and subtasks result
        $query = $this->db->table(TaskModel::TABLE)
                    ->columns(
                        ProjectModel::TABLE . '.*',
                        '(select count(*) from ' . TaskModel::TABLE . ' where ' . TaskModel::TABLE . '.project_id = ' . ProjectModel::TABLE . '.id and ' . TaskModel::TABLE . '.is_active =' . TaskModel::STATUS_OPEN . ') as nb_active',
                        '(select count(*) from ' . TaskModel::TABLE . ' where ' . TaskModel::TABLE . '.project_id = ' . ProjectModel::TABLE . '.id and ' . TaskModel::TABLE . '.is_active =' . TaskModel::STATUS_CLOSED . ') as nb_closed'
                    )
                    ->join(ProjectModel::TABLE, 'id', 'project_id')
                    ->join(ColumnModel::TABLE, 'id', 'column_id', TaskModel::TABLE)
                    ->join(SubtaskModel::TABLE, 'task_id', 'id', TaskModel::TABLE)

                    ->beginOr()
                        ->in(SubtaskModel::TABLE . '.status', $subtaskStatus)
                        ->isNull(SubtaskModel::TABLE . '.status')
                    ->closeOr()
                    ->beginOr()
                        ->eq(SubtaskModel::TABLE.'.user_id', $userId)
                        ->isNull(SubtaskModel::TABLE.'.id')
                    ->closeOr()

                    ->eq(ProjectModel::TABLE . '.is_active', ProjectModel::ACTIVE)
                    ->eq(TaskModel::TABLE . '.is_active', $taskStatus)
                    ->eq(ColumnModel::TABLE . '.hide_in_dashboard', 0)

                    ->beginOr()
                        ->eq(TaskModel::TABLE . '.owner_id', (int) $userId)
                        ->eq(SubtaskModel::TABLE . '.user_id', (int) $userId)
                    ->closeOr()

                    ->groupBy(TaskModel::TABLE . '.project_id');

        $this->hook->reference('pagination:dashboard:projectlist:query', $query);

        $this->helper->dashboardHelper->addTasksFilter($query, $taskFilter);

        return $this->paginator->setUrl('MyDashboardController', $method, array(
                    'pagination' => 'tasks',
                    'user_id' => $userId,
                    'plugin' => 'Greenwing'
                ))
                ->setMax($max)
                ->setOrder(TaskModel::TABLE.'.priority')
                ->setDirection('DESC')
                ->setQuery($query)
//                 ->setFormatter($this->taskListFormatter)
                ->calculateOnlyIf($this->request->getStringParam('pagination') === 'tasks')
        ;
    }
}
