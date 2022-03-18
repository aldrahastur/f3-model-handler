<?php
/**
 * Created by PhpStorm.
 * User: willi
 * Date: 18.03.2022
 * Time: 15:33
 */


use DB\SQL\Mapper;

class AbstractModels
{

    public $f3;

    public function __construct($f3)
    {
        $this->f3 = $f3;
    }

    function getSqlTable($table, $fields = null){
        if ($fields != null) {
            return new DB\SQL\Mapper(Registry::get('db'),$table, $fields);
        }
        else {
            return new DB\SQL\Mapper(Registry::get('db'),$table);
        }

    }

    function getEntity($table, $index, $id){
        $invoice = $this->getSqlTable($table);
        $invoice->load(array(
            $index.' LIKE :id',
            ':id' => $id
        ));
        return $invoice;
    }

    function findEntity($table, $findArray)
    {
        $invoice = $this->getSqlTable($table);

        $item = $invoice->findone($findArray);
        return $item;
    }

    function listEntities($table, $filter = null, $sort = null){
        $entities = $this->getSqlTable($table);
        return $entities->find($filter, $sort);
    }

    function countEntities($table, $filter = null, $sort = null){
        $entities = $this->getSqlTable($table);
        return $entities->count($filter, $sort);
    }


    function listArrayEntities($table, $filter = null, $options = null){
        $entities = $this->getSqlTable($table);
        $entities = $entities->find($filter, $options);
        $array = array();
        foreach ($entities as $entity) {
            $array[] = $entity->cast();
        }
        return $array;
    }

    function selectArrayEntities($table, $fields= null, $filter = null){
        $entities = $this->getSqlTable($table, $fields);
        $entities = $entities->find($filter);
        $array = array();
        foreach ($entities as $entity) {
            $array[] = $entity->cast();
        }
        return $array;
    }


    function selectEntities($table, $fields= null, $filter = null, $sort = null){
        $entities = $this->getSqlTable($table, $fields);
        return $entities->find($filter, $sort);
    }

    public function paginateEntities($table, $filter = null){
        $entities = $this->getSqlTable($table);

        $page = ($this->f3->get('GET.page') != null) ? $this->f3->get('GET.page') : 0;
        $limit = ($this->f3->get('GET.limit') != null) ? $this->f3->get('GET.limit') : 15;

        return $entities->paginate($page, $limit, $filter);
    }


    public function paginateEntitiesWithOptions($f3, $table, $options = null)
    {
        if (isset($options['fields'])) {
            $entities = $this->getSqlTable($table, $options['fields']);
        } else {
            $entities = $this->getSqlTable($table);
        }

        $page = $f3->exists('GET.page') ? $f3->get('GET.page') : 0;
        $limit = $f3->exists('GET.limit') ? $f3->get('GET.limit') : 15;

        if (isset($options['sort'])) {
            return $entities->paginate($page, $limit, $options['filter'], $options['sort']);
        } else {
            return $entities->paginate($page, $limit, $options['filter']);
        }

    }


    function apiPaginate($table, $options = null) {
        $entities = $this->paginateEntitiesWithOptions($this->f3, $table, $options);

        $result = [
            'page' => $entities['pos'],
            'limit' => $entities['limit'],
            'count' => $entities['count'],
            'total' => $entities['total'],
        ];

        foreach ($entities['subset'] as $entity) {
            $result['items'][] = $entity->cast();
        }

        return $result;
    }

    function createEntity($table, $postdata, $log =false){

        $entity = $this->getSqlTable($table);
        foreach ($postdata as $key => $value) {
            if ($table == 'Orders' && $key == 'OrderIncrementId' && $value == null) {
                $entity->$key = $postdata['InstanceId'].'BE-'.uniqid();
            }
            $entity->$key = $value;
        }

        return $entity->insert();
    }

    function updateEntity($f3, $table, $index, $id, $putdata, $user = null){
        $entity = $this->getSqlTable($table);
        $entity->load(array(
            $index.' LIKE :id',
            ':id' => $id
        ));

        var_dump($putdata);

        foreach ($putdata as $key => $value) {

            if ($entity->$key != $value) {
                $entity->$key = $value;
            }

        }

        return $entity->update();
    }

    function deleteEntity($f3){

    }
}
