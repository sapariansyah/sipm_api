<?php

namespace App\Controllers;

use App\Helpers;
use App\Dao;

class ReportCtrl {

    private $dao;

    public function __construct($container) {
        $this->container = $container;
        $this->dao = new Dao($container);
    }

    public function monitoringTanggapan($request, $response, $args) {
        try {
            $report = $this->dao->getMonitoringTanggapan($request);

            return $response->withJson(
                Helpers::createResponse(
                    ERR_SERVER_SUCCESS, 
                    "Success", 
                    $report
                )
            );
        } catch (Exception $e) {
            return $response->withJson(
                Helpers::createResponse(
                    ERR_SERVER_ERROR, 
                    $e, 
                    []
                )
            );
        }
    }

    public function pelanggaranPerPerusahaan($request, $response, $args) {
        try {
            $report = $this->dao->getPelanggaranPerPerusahaan($request);

            return $response->withJson(
                Helpers::createResponse(
                    ERR_SERVER_SUCCESS, 
                    "Success", 
                    $report
                )
            );
        } catch (Exception $e) {
            return $response->withJson(
                Helpers::createResponse(
                    ERR_SERVER_ERROR, 
                    $e, 
                    []
                )
            );
        }
    }

    public function getCurrentLogin($request, $response, $args) {
        $db = new Database($this->container->get('settings')['database']);
        $user = $db->getUserDetail($args['id']);
        return $response->withJson(Helpers::createResponse(
                                ERR_SERVER_SUCCESS, "Success.", $user
        ));
    }

    public function getPeraturan($request, $response, $args) {
        try {
            $report = $this->dao->getPeraturan($request);

            return $response->withJson(
                Helpers::createResponse(
                    ERR_SERVER_SUCCESS, 
                    "Success", 
                    $report
                )
            );
        } catch (Exception $e) {
            return $response->withJson(
                Helpers::createResponse(
                    ERR_SERVER_ERROR, 
                    $e, 
                    []
                )
            );
        }
    }

    public function getPihak($request, $response, $args) {
        try {
            $report = $this->dao->getPihak($request);

            return $response->withJson(
                Helpers::createResponse(
                    ERR_SERVER_SUCCESS, 
                    "Success", 
                    $report
                )
            );
        } catch (Exception $e) {
            return $response->withJson(
                Helpers::createResponse(
                    ERR_SERVER_ERROR, 
                    $e, 
                    []
                )
            );
        }
    }

    public function getPihakIndividu($request, $response, $args) {
        try {
            $report = $this->dao->getPihakIndividu($request);

            return $response->withJson(
                Helpers::createResponse(
                    ERR_SERVER_SUCCESS, 
                    "Success", 
                    $report
                )
            );
        } catch (Exception $e) {
            return $response->withJson(
                Helpers::createResponse(
                    ERR_SERVER_ERROR, 
                    $e, 
                    []
                )
            );
        }
    }

    public function getAlamatIndividu($request, $response, $args) {
        try {
            $report = $this->dao->getAlamatIndividu($request);

            return $response->withJson(
                Helpers::createResponse(
                    ERR_SERVER_SUCCESS, 
                    "Success", 
                    $report
                )
            );
        } catch (Exception $e) {
            return $response->withJson(
                Helpers::createResponse(
                    ERR_SERVER_ERROR, 
                    $e, 
                    []
                )
            );
        }
    }

    public function getIdentitasIndividu($request, $response, $args) {
        try {
            $report = $this->dao->getIdentitasIndividu($request);

            return $response->withJson(
                Helpers::createResponse(
                    ERR_SERVER_SUCCESS, 
                    "Success", 
                    $report
                )
            );
        } catch (Exception $e) {
            return $response->withJson(
                Helpers::createResponse(
                    ERR_SERVER_ERROR, 
                    $e, 
                    []
                )
            );
        }
    }

    public function getPihakInstitusi($request, $response, $args) {
        try {
            $report = $this->dao->getPihakInstitusi($request);

            return $response->withJson(
                Helpers::createResponse(
                    ERR_SERVER_SUCCESS, 
                    "Success", 
                    $report
                )
            );
        } catch (Exception $e) {
            return $response->withJson(
                Helpers::createResponse(
                    ERR_SERVER_ERROR, 
                    $e, 
                    []
                )
            );
        }
    }

    public function getShp($request, $response, $args) {
        try {
            $report = $this->dao->getShp($request);

            return $response->withJson(
                Helpers::createResponse(
                    ERR_SERVER_SUCCESS, 
                    "Success", 
                    $report
                )
            );
        } catch (Exception $e) {
            return $response->withJson(
                Helpers::createResponse(
                    ERR_SERVER_ERROR, 
                    $e, 
                    []
                )
            );
        }
    }

    public function getShpKesimpulanPihak($request, $response, $args) {
        try {
            $report = $this->dao->getShpKesimpulanPihak($request);

            return $response->withJson(
                Helpers::createResponse(
                    ERR_SERVER_SUCCESS, 
                    "Success", 
                    $report
                )
            );
        } catch (Exception $e) {
            return $response->withJson(
                Helpers::createResponse(
                    ERR_SERVER_ERROR, 
                    $e, 
                    []
                )
            );
        }
    }

    public function getShpPeraturan($request, $response, $args) {
        try {
            $report = $this->dao->getShpPeraturan($request);

            return $response->withJson(
                Helpers::createResponse(
                    ERR_SERVER_SUCCESS, 
                    "Success", 
                    $report
                )
            );
        } catch (Exception $e) {
            return $response->withJson(
                Helpers::createResponse(
                    ERR_SERVER_ERROR, 
                    $e, 
                    []
                )
            );
        }
    }

    public function getShpPihak($request, $response, $args) {
        try {
            $report = $this->dao->getShpPihak($request);

            return $response->withJson(
                Helpers::createResponse(
                    ERR_SERVER_SUCCESS, 
                    "Success", 
                    $report
                )
            );
        } catch (Exception $e) {
            return $response->withJson(
                Helpers::createResponse(
                    ERR_SERVER_ERROR, 
                    $e, 
                    []
                )
            );
        }
    }

    public function getSuratTugas($request, $response, $args) {
        try {
            $report = $this->dao->getSuratTugas($request);

            return $response->withJson(
                Helpers::createResponse(
                    ERR_SERVER_SUCCESS, 
                    "Success", 
                    $report
                )
            );
        } catch (Exception $e) {
            return $response->withJson(
                Helpers::createResponse(
                    ERR_SERVER_ERROR, 
                    $e, 
                    []
                )
            );
        }
    }

    public function getTimSuratTugas($request, $response, $args) {
        try {
            $report = $this->dao->getTimSuratTugas($request);

            return $response->withJson(
                Helpers::createResponse(
                    ERR_SERVER_SUCCESS, 
                    "Success", 
                    $report
                )
            );
        } catch (Exception $e) {
            return $response->withJson(
                Helpers::createResponse(
                    ERR_SERVER_ERROR, 
                    $e, 
                    []
                )
            );
        }
    }

    public function getUser($request, $response, $args) {
        try {
            $report = $this->dao->getUser($request);

            return $response->withJson(
                Helpers::createResponse(
                    ERR_SERVER_SUCCESS, 
                    "Success", 
                    $report
                )
            );
        } catch (Exception $e) {
            return $response->withJson(
                Helpers::createResponse(
                    ERR_SERVER_ERROR, 
                    $e, 
                    []
                )
            );
        }
    }
}
