<?php

namespace F3ModelHandler;

class BaseController extends AbstractModels
{

    function getEntity($table, $index, $id){
        parent::getEntity($table, $index, $id);
    }
    function findEntity($table, $findArray) {
        parent::findEntity($table, $findArray);
    }

}