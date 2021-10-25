<?php
namespace Kanboard\Plugin\Greenwing\Controller;

use Kanboard\Core\Base;
use Kanboard\Controller\BaseController;
use Kanboard\Model\UserModel;
use Kanboard\Model\ProjectModel;
use Kanboard\Model\TaskModel;
use Kanboard\Model\CommentModel;
use Kanboard\Model\TaskFileModel;
use Kanboard\Model\UserMetadataModel;

/**
 * Dashboard Controller
 *
 * @package Kanboard\Controller
 * @author
 *
 */
class MyDashboardController extends BaseController
{

    public function projects()
    {
        return $this->show($userId = $this->userSession->getId());
    }

    /**
     * Dashboard overview
     *
     * @access public
     */
    public function show($userId = null)
    {
        $user = $this->getUser();
        $adminOverview = false;

        if ($userId === null && $this->userSession->isAdmin()) {
            $projects = $this->projectModel->getListByStatus(1);
            $adminOverview = true;
        } else {
            $projects = $this->projectUserRoleModel->getActiveProjectsByUser($user['id']);
        }

        $aOverview = [];
        foreach ($projects as $projectId => $projectName)
        {
            // active and closed
            $active = $this->taskFinderModel->countByProjectId($projectId, [
                TaskModel::STATUS_OPEN
            ]);
            $closed = $this->taskFinderModel->countByProjectId($projectId, [
                TaskModel::STATUS_CLOSED
            ]);
            // overdue
            $overdue = $this->taskFinderModel->getOverdueTasksQuery()
                ->eq(TaskModel::TABLE . '.project_id', $projectId)
                ->count();

            $defaultDue = strval($this->projectMetadataModel->get($projectId, 'DueDate_Board_Default_Date'));
            if (is_null($defaultDue)) {
                $defaultDue = "+75 days";
            }

            // due is set, check if in due end period
            $futureDue = abs(intval($this->projectMetadataModel->get($projectId, 'DueDate_Board_Distant_Future')));
            if ($futureDue == 0) {
                $futureDue = 3;
            }
            $futureDueTime = strtotime('+' . strval($futureDue) . 'days');

            $inDueTime = $this->db->table(TaskModel::TABLE)
                ->join(ProjectModel::TABLE, 'id', 'project_id')
                ->eq(TaskModel::TABLE . '.project_id', $projectId)
                ->eq(ProjectModel::TABLE . '.is_active', 1)
                ->eq(TaskModel::TABLE . '.is_active', 1)
                ->neq(TaskModel::TABLE . '.date_due', 0)
                ->gt(TaskModel::TABLE . '.date_due', time())
                ->lte(TaskModel::TABLE . '.date_due', $futureDueTime)
                ->count();
            // $this->taskListSubtaskAssigneeFormatter->withUserId($userId)

            $aOverview[$projectId] = array(
                // 'project' => $this->projectModel->getById($projectId),
                // 'project_id' => $projectId,
                // 'project_name' => $projectName,
                'tasks' => [
                    'default' => $defaultDue,
                    'future' => $futureDue,
                    'futureTime' => $futureDueTime,
                    'time' => time(),
                    'active' => $active,
                    'planned' => $active - $inDueTime - $overdue,
                    'due' => $inDueTime,
                    'overdue' => $overdue,
                    'closed' => $closed,
                    'total' => $active + $closed
                ],
                'team' => $this->projectUserRoleModel->getAllUsers($projectId)
            );
        }

//         $role = $user['role'] === "app-admin" ? "show": "projects";

        $this->response->html($this->helper->layout->dashboard('Greenwing:dashboard/overview', array(
            'title' => t('Dashboard for %s', $this->helper->user->getFullname($user)),
            'user' => $user,
            'project_overview' => $aOverview,
            'project_paginator' => $this->projectPagination->getDashboardPaginator($user['id'], 'show', 100, $adminOverview)
        )));
    }

    /**
     * tooltip tasks list
     *
     * @access public
     */
    public function tooltip_project_columns()
    {
        $user = $this->getUser();
        $project = $this->getProject();

        $a = $this->projectModel->getQueryColumnStats( [$project['id']] )
            ->eq(ProjectModel::TABLE . '.id', $project['id'])
            ->findOne();

        $this->response->html($this->helper->layout->dashboard('Greenwing:dashboard/tooltip_project_columns', array(
            'title' => t('Tasks overview'),
            'debug' => [
                "a" => $a
            ],
            'project' => $a
        )));
    }

    /**
     * tooltip tasks list
     *
     * @access public
     */
    public function tooltip_tasklist()
    {
        $user = $this->getUser();
        $project = $this->getProject();

        $a = $this->taskFinderModel->getProjectUserOverviewQuery([
            $project['id']
        ], TaskModel::STATUS_OPEN)
            ->eq(ProjectModel::TABLE . '.is_active', ProjectModel::ACTIVE)
            ->eq($this->columnModel::TABLE . '.id', $this->request->getIntegerParam("column_id"))
            ->findAll();

        $this->response->html($this->helper->layout->dashboard('Greenwing:dashboard/tooltip_tasklist', array(
            'title' => t('Tasks overview'),
            'tasks' => $a
        )));
    }

    /**
     * tooltip files list
     *
     * @access public
     */
    public function tooltip_filelist()
    {
        $user = $this->getUser();
        $project = $this->getProject();

        $a = $this->db->table(TaskFileModel::TABLE)
                ->columns(
                    TaskFileModel::TABLE . '.id',
                    TaskFileModel::TABLE . '.name',
                    TaskFileModel::TABLE . '.path',
                    TaskFileModel::TABLE . '.is_image',
                    TaskFileModel::TABLE . '.task_id',
                    TaskFileModel::TABLE . '.date',
                    TaskFileModel::TABLE . '.user_id',
                    TaskFileModel::TABLE . '.size',
                    UserModel::TABLE . '.username',
                    UserModel::TABLE . '.name as user_name',
                    TaskModel::TABLE . '.project_id'
                )
                ->join(UserModel::TABLE, 'id', 'user_id')
                ->join(TaskModel::TABLE, 'id', 'task_id')
                ->eq(TaskModel::TABLE . '.project_id', $project['id'])
                ->asc(TaskFileModel::TABLE . '.date')
            ->findAll();

        $this->response->html($this->helper->layout->dashboard('Greenwing:dashboard/tooltip_filelist', array(
            'title' => t('Tasks overview'),
            'files' => $a
        )));
    }

    /*
     * all project coments
     */
    public function project_comment_list()
    {
        $user = $this->getUser();
        $project = $this->getProject();
        $commentSortingDirection = $this->userMetadataCacheDecorator->get(UserMetadataModel::KEY_COMMENT_SORTING_DIRECTION, 'ASC');

        $projectComments = $this->db->table(CommentModel::TABLE)
            ->columns(
                CommentModel::TABLE . '.id',
                CommentModel::TABLE . '.date_creation',
                CommentModel::TABLE . '.date_modification',
                CommentModel::TABLE . '.task_id',
                CommentModel::TABLE . '.user_id',
                CommentModel::TABLE . '.comment',
                UserModel::TABLE . '.username',
                UserModel::TABLE . '.name',
                UserModel::TABLE . '.email',
                UserModel::TABLE . '.avatar_path',
                TaskModel::TABLE . '.title as task_title',
                TaskModel::TABLE . '.is_active as task_is_active'
            )
            ->join(TaskModel::TABLE, 'id', 'task_id')
            ->join(UserModel::TABLE, 'id', 'user_id')
            ->eq(TaskModel::TABLE . '.project_id', $project['id'])

        // ->orderBy(TaskModel::TABLE.'.id', 'ASC')
            ->orderBy(CommentModel::TABLE . '.date_creation', $commentSortingDirection)
            ->orderBy(CommentModel::TABLE . '.id', $commentSortingDirection)
            ->findAll();

        $this->response->html($this->template->render('Greenwing:dashboard/project_comment_list', array(
            'project' => $project,
            'task' => $task,
            'comments' => $projectComments,
            'editable' => false,
            'sorting_dir' => $commentSortingDirection
        )));
    }

    public function project_comment_list__toggle_sorting()
    {
        $this->helper->comment->toggleSorting();
        $this->project_comment_list();
    }

    /**
     * NEW My tasks - grouped by project
     *
     * @access public
     */
    public function tasksoverview()
    {
        $user = $this->getUser();

        $this->response->html($this->helper->layout->dashboard('Greenwing:dashboard/overview_tasks', array(
            'title' => t('Task overview'),
            'paginator' => $this->myTaskPagination->getDashboardOverviewPaginator($user['id'], 'tasksoverview', 1000),
            'user' => $user,
            'action' => 'tasksoverview'
        )));
    }

    /**
     * NEW My tasks - grouped by project
     *
     * @access public
     */
    public function tasks()
    {
        $user = $this->getUser();

        $taskcount = $this->helper->dashboardHelper->getProjectUserTaskSubtaskCount($user);

        $this->response->html($this->helper->layout->dashboard('Greenwing:dashboard/tasks', array(
            'title' => t('Tasks overview for %s', $this->helper->user->getFullname($user)),
            'headline' => t('My tasks (%d), my subtasks (%d)', $taskcount['task_cnt'], $taskcount['subtask_cnt']),
            'paginator' => $this->myTaskPagination->getDashboardPaginator($user['id'], 'tasks', 1000),
            'user' => $user,
            'showFilter' => true
        )));
    }

    public function duetasks()
    {
        $user = $this->getUser();

        $this->response->html($this->helper->layout->dashboard('Greenwing:dashboard/tasks', array(
            'title' => t('Due tasks for %s', $this->helper->user->getFullname($user)),
            'paginator' => $this->myTaskPagination->getDashboardPaginator($user['id'], 'duetasks', 1000, 'due'),
            'user' => $user,
            'showFilter' => false,
            'taskFilter' => 'due'
        )));
    }
    public function overduetasks()
    {
        $user = $this->getUser();

        $this->response->html($this->helper->layout->dashboard('Greenwing:dashboard/tasks', array(
            'title' => t('Overdue tasks for %s', $this->helper->user->getFullname($user)),
            'paginator' => $this->myTaskPagination->getDashboardPaginator($user['id'], 'overduetasks', 1000, 'overdue'),
            'user' => $user,
            'showFilter' => false,
            'taskFilter' => 'overdue'
        )));
    }

    /**
     * My subtasks
     *
     * @access public
     */
    public function subtasks()
    {
        $user = $this->getUser();

        $this->response->html($this->helper->layout->dashboard('dashboard/subtasks', array(
            'title' => t('Subtasks overview for %s', $this->helper->user->getFullname($user)),
            'paginator' => $this->subtaskPagination->getDashboardPaginator($user['id']),
            'user' => $user,
            'nb_subtasks' => $this->subtaskModel->countByAssigneeAndTaskStatus($user['id'])
        )));
    }


    public function changeFilterForMyTasks()
    {
        $user = $this->getUser();

        $taskFilter = $this->request->getStringParam("task_filter");
        $this->helper->dashboardHelper->changeFilterForMyTasks($taskFilter);

        $ref = $this->request->getStringParam("referrer", 'tasks');
        $this->response->redirect($this->helper->url->to('MyDashboardController', $ref, array('user_id' => $user['id'], 'plugin'=>'Greenwing')));
    }


    public function comments()
    {
        $user = $this->getUser();
        $project_id = $this->request->getIntegerParam("project_id");

        $projects = $this->projectUserRoleModel->getActiveProjectsByUser($user['id']);

        $projectKeys = array_keys($projects);

//         print_r($projectKeys); die();
        $commentSortingDirection = $this->userMetadataCacheDecorator->get('DashboardCommentsSortingOrder', 'ASC');

        $f = $this->db->table(TaskFileModel::TABLE)
                ->columns (
                    '"file" as type',
                    TaskFileModel::TABLE . '.id',
                    TaskFileModel::TABLE . '.date as crdate',

                    TaskModel::TABLE . '.title as task_title',
                    TaskModel::TABLE . '.is_active as task_is_active',
                    ProjectModel::TABLE . '.name as project_name',
                    ProjectModel::TABLE . '.id as project_id',
                )
                ->join(TaskModel::TABLE, 'id', 'task_id')
                ->join(ProjectModel::TABLE, 'id', 'project_id', TaskModel::TABLE)

                ->eq(ProjectModel::TABLE .'.is_active', 1 )
                ->eq(TaskModel::TABLE . '.is_active', 1 )

                ->in(ProjectModel::TABLE .'.id', $projectKeys)

                ->beginOr()
                ->eq(TaskFileModel::TABLE . '.user_id', $user['id'] )
                ->eq(TaskModel::TABLE . '.owner_id', $user['id'] )
                ->eq(TaskModel::TABLE . '.creator_id', $user['id'] )
                ->eq(ProjectModel::TABLE . '.owner_id', $user['id'] )
                ->closeOr()

                ->orderBy( 'crdate', $commentSortingDirection );

        if ( $project_id ){
            $f->eq( ProjectModel::TABLE .'.id',  $project_id );
        }

        $c = $this->db->table(CommentModel::TABLE)
                ->columns (
                    '"comment" as type',
                    CommentModel::TABLE . '.id',
                    CommentModel::TABLE . '.date_creation as crdate',

                    TaskModel::TABLE . '.title as task_title',
                    TaskModel::TABLE . '.is_active as task_is_active',
                    ProjectModel::TABLE . '.name as project_name',
                    ProjectModel::TABLE . '.id as project_id',
                )
                ->join(TaskModel::TABLE, 'id', 'task_id')
                ->join(ProjectModel::TABLE, 'id', 'project_id', TaskModel::TABLE)

                ->eq(ProjectModel::TABLE .'.is_active', 1 )
                ->eq(TaskModel::TABLE . '.is_active', 1 )

                ->in(ProjectModel::TABLE .'.id', $projectKeys)

                ->beginOr()
                ->eq(CommentModel::TABLE . '.user_id', $user['id'] )
                ->eq(TaskModel::TABLE . '.owner_id', $user['id'] )
                ->eq(TaskModel::TABLE . '.creator_id', $user['id'] )
                ->eq(ProjectModel::TABLE . '.owner_id', $user['id'] )
                ->closeOr();

        if ( $project_id ){
            $c->eq( ProjectModel::TABLE.'.id', $project_id );
        }

        $c->union( $f );
//         echo $c->buildSelectQuery();die();

        $result = $c->findAll();

        foreach ( (array) $result as $idx => $entry )
        {
            if ( $entry["type"] === "comment" ) {
                $relTable = CommentModel::TABLE;
            }
            elseif ( $entry["type"] === "file" ) {
                $relTable = TaskFileModel::TABLE;
            } else {
                // skip
                continue;
            }

            $r = $this->db->table($relTable)
                    ->eq("$relTable.id", $entry["id"] )

                    ->join(UserModel::TABLE, 'id', 'user_id')
                    ->columns (
                        "$relTable.*",
                        UserModel::TABLE . '.username',
                        UserModel::TABLE . '.name as user_name',
                        UserModel::TABLE . '.email',
                        UserModel::TABLE . '.avatar_path',
                    )
                    ->findOne();

            $result[$idx] = array_merge($result[$idx], $r);
        }

        $this->response->html($this->helper->layout->dashboard('Greenwing:dashboard/comments', array(
            'title' => t('Comments for %s', $this->helper->user->getFullname($user)),
            'user' => $user,
            'result' => $result,
            'sorting_dir' => $commentSortingDirection,
            'projects' => $projects,
            'project_id' => $project_id
        )));

    }

    public function dashboard_comments__toggle_sorting()
    {
        $user = $this->getUser();

        $this->helper->dashboardHelper->commentToggleSorting();
        $this->response->redirect($this->helper->url->to('MyDashboardController', 'comments', array('user_id' => $user['id'], 'plugin' => 'Greenwing')));
    }

//     /**
//      * My projects
//      *
//      * @access public
//      */
//     public function projects()
//     {
//         $user = $this->getUser();

// //         print_r($user); die();


//         $this->response->html($this->helper->layout->dashboard('dashboard/projects', array(
//             'title' => t('Projects overview for %s', $this->helper->user->getFullname($user)),
//             'paginator' => $this->projectPagination->getDashboardPaginator($user['id'], 'projects', 100),
//             'user' => $user
//         )));
//     }
}
