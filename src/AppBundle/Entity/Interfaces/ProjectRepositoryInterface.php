<?php

namespace AppBundle\Entity\Interfaces;

interface ProjectRepositoryInterface
{
    /**
     * @return array
     */
    public function getProjectStat();

    /**
     * @return array
     */
    public function getProjectShow();

    /**
     * @param integer $id
     * @return array
     */
    public function getOneProjectShow($id);
}
