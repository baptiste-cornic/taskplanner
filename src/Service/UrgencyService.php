<?php

namespace App\Service;

class UrgencyService{
    public function __construct( ){
    }

    public function getUrgency($tasks){
        foreach ($tasks as $task){
            if ($task->getEndDate() < (new \DateTime())->modify('+1 days')){
                $task->setUrgency([
                    'color' => 'danger',
                    'msg' => 'Urgent'
                ]);
            }
            elseif ($task->getEndDate() < (new \DateTime())->modify('+7 days')){
                $task->setUrgency([
                    'color' => 'warning',
                    'msg' => 'Rappel'
                ]);
            }
            else{
                $task->setUrgency([
                    'color' => 'success',
                    'msg' => 'To do'
                ]);
            }
        }
        return $tasks;
    }
}
