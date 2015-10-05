<?php

namespace AppBundle\Helper;

use AppBundle\Entity\User;
use Doctrine\ORM\EntityManager;

class UserManager
{
    protected $_templateEngine;
    protected $em;

    private $rootDir;

    public function setRootDir($rootDir)
    {
        $this->rootDir = $rootDir;
    }

    public function __construct($_templateEngine, EntityManager $em)
    {
        $this->templateEngine = $_templateEngine;
        $this->em = $em;
    }

    public function isUniqueUser($clid)
    {
        $user = $this->em->getRepository('AppBundle:User')->findByClid($clid);
        if (empty($user)) {
                //ToDo create new User
//                $this->em->flush();
//                $this->mail->toUser_registration($user);//congratulation for registration
        }
        else
        {
            //ToDo add user in security context
        }
        return $user;
    }

//    public function getInfoIpCountry($ip)
//    {
//        try {
//           $reader = new Reader($this->rootDir.'/data/GeoLite2-Country.mmdb');
//           $data = $reader->country($ip);
//        } catch (\Exception $e) {
//            $data = null;
//        }
//
//        return $data;
//    }
//
//    public function getInfoIpCity($ip)
//    {
//        try {
//            $reader = new Reader($this->rootDir.'/data/GeoLite2-City.mmdb');
//            $data_geo = $reader->city($ip);
//
//            $name = $data_geo->country->name;
//            $isoCode = $data_geo->country->isoCode;
//            $mostSpecificSubdivision = $data_geo->mostSpecificSubdivision->name;
//            $data = $city = $data_geo->city->name;
//            $postal = $data_geo->postal->code; // '55455'
//            $latitude = $data_geo->location->latitude; // 50.45
//            $longitude = $data_geo->location->longitude;// 30.5233
//
//            if (is_null($data)){
//                $url = 'http://maps.googleapis.com/maps/api/geocode/json?latlng='.$latitude.','.$longitude.'&sensor=false';
//
//                $data = @file_get_contents($url);
//                $jsondata = json_decode($data,true);
//
//                if(is_array($jsondata )&& $jsondata ['status'] == "OK")
//                {
//                    $addr = $jsondata ['results'][0]['address_components'][4]['long_name'];
//                    $addr2 = $jsondata ['results'][0]['address_components'][4]['short_name'];
//                    $addr3 = $jsondata ['results'][0]['address_components'][3]['long_name'];
//                }
//                $info = "Country: " . $addr . " | Region: " . $addr2 . " | City: " . $addr3;
//                $data = $addr3;
//
//            }
//
//        } catch (\Exception $e) {
//            $data = null;
//        }
//
//        return $data;
//    }
}
