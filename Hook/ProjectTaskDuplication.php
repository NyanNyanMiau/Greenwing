<?php

Namespace Kanboard\Plugin\Greenwing\Hook;

use Kanboard\Core\Base;

class ProjectTaskDuplication extends Base
{
    // after duplication, tasks linked togehter

    public function duplicateLinks($hook_values)
    {
        if (! (is_array($hook_values) && isset($hook_values['duplicated_tasks']) && count($hook_values['duplicated_tasks']))) {
            return false;
        }
        // [source_task_id => dest_task_id , ...]
        $duplicated_tasks = $hook_values['duplicated_tasks'];

        // track processed tasks
        $tasks_done = [];
        foreach ($duplicated_tasks as $source_task_id => $target_task_id) {
            // get all links from source task
            $aSourceLinks = $this->taskLinkModel->db
                                ->table($this->taskLinkModel::TABLE)
                                ->eq('task_id', $source_task_id)
                                ->findAll();

            $newlinks = 0;
            foreach ($aSourceLinks as $aLink) {
                if (! isset($tasks_done[$aLink['opposite_task_id']]) && isset($duplicated_tasks[$aLink['opposite_task_id']])) {
                    $newlinks++;
                    // creates link and opposite link
                    $this->taskLinkModel->create($target_task_id, $duplicated_tasks[$aLink['opposite_task_id']], $aLink['link_id']);
                }
            }

            $tasks_done[$source_task_id] = $newlinks;
        }
        return true;
    }

}