<?php

namespace AppBundle\Entity\Interfaces;

use Symfony\Component\HttpFoundation\ParameterBag;

interface ProjectRepositoryInterface
{
    /**
     * @return array
     */
    public function getProjectStat();

    /**
     * @param ParameterBag $parameterBag
     * @return array
     */
    public function getProjectShow(
        ParameterBag $parameterBag
    );

    /**
     * @param integer $id
     * @return array
     */
    public function getOneProjectShow($id);
}
