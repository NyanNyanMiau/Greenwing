<?php
namespace Kanboard\Plugin\Greenwing\Pagination;

use Kanboard\Core\Base;
use Kanboard\Core\Paginator;
use Kanboard\Model\ProjectModel;
use Kanboard\Model\TaskModel;
use Kanboard\Model\TaskFileModel;
use Kanboard\Model\CommentModel;

/**
 * Class ProjectPagination
 *
 * @package Kanboard\Pagination
 * @author Frederic Guillot
 */
class MyProjectPagination extends Base
{

    /**
     * Get dashboard pagination
     *
     * @access public
     * @param integer $user_id
     * @param string $method
     * @param integer $max
     * @return Paginator
     */
    public function getDashboardPaginator($user_id, $method, $max, $adminOverview=false)
    {
        if ($adminOverview && $this->userSession->isAdmin()){
            $p = $this->projectModel->getAllIds();
        }else{
            $p = $this->projectPermissionModel->getActiveProjectIds($user_id);
        }

        $query = $this->projectModel->getQueryColumnStats($p)
            ->
        // add projects comments count
        subquery('SELECT COUNT(*) FROM ' . CommentModel::TABLE . ' inner join ' . TaskModel::TABLE . ' WHERE task_id=tasks.id and tasks.project_id=projects.id', 'nb_comments')
            ->
        // add project files count
        subquery('SELECT COUNT(*) FROM ' . TaskFileModel::TABLE . ' inner join ' . TaskModel::TABLE . ' WHERE task_id=tasks.id and tasks.project_id=projects.id', 'nb_files');


        $this->hook->reference('pagination:dashboard:project:query', $query);

        return $this->paginator->setUrl('MyDashboardController', $method, array(
            'pagination' => 'projects',
            'user_id' => $user_id,
            'plugin' => 'Greenwing'
        ))
            ->setMax($max)
            ->setOrder(ProjectModel::TABLE . '.name')
            ->setQuery($query)
            ->calculateOnlyIf($this->request->getStringParam('pagination') === 'projects');
    }
}
