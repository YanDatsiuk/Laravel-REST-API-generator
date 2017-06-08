<?php

namespace TMPHP\RestApiGenerators\Compilers\Scopes\Support;

/**
 * Created by PhpStorm.
 * User: datsyuk
 * Date: 08.06.17
 * Time: 9:29
 */

/**
 * Class RelationTableModelParamBag
 *
 * This is container for parameters:
 * relation's name, table's name and model's name.
 *
 * @package TMPHP\RestApiGenerators\Compilers\Scopes\Support
 */
class RelationTableModelParamBag
{
    /** @var  string $relation */
    private $relation;

    /** @var  string $table */
    private $table;

    /** @var  string $model */
    private $model;

    /**
     * RelationTableModelParamBag constructor.
     * @param string $relation
     * @param string $table
     * @param string $model
     */
    public function __construct(string $relation, string $table, string $model)
    {
        $this->relation = $relation;
        $this->table = $table;
        $this->model = studly_case($model);
    }

    /**
     * @return string
     */
    public function getRelation(): string
    {
        return $this->relation;
    }

    /**
     * @param string $relation
     */
    public function setRelation(string $relation)
    {
        $this->relation = $relation;
    }

    /**
     * @return string
     */
    public function getTable(): string
    {
        return $this->table;
    }

    /**
     * @param string $table
     */
    public function setTable(string $table)
    {
        $this->table = $table;
    }

    /**
     * @return string
     */
    public function getModel(): string
    {
        return $this->model;
    }

    /**
     * @param string $model
     */
    public function setModel(string $model)
    {
        $this->model = studly_case($model);
    }


}