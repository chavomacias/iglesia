<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Nel\Modelo\Entity;

use Zend\Db\TableGateway\TableGateway;
use Zend\Db\Adapter\Adapter;

class HoraHorario extends TableGateway
{
    public function __construct(Adapter $adapter = null, $databaseSchema = null, ResultSet $selectResultPrototype = null)
    {
        return parent::__construct('horahorario', $adapter, $databaseSchema, $selectResultPrototype);
    }
    
//    
//    public function ObtenerDias(){
//        $resultado = $this->getAdapter()->query("CALL Sp_ObtenerDias()", Adapter::QUERY_MODE_EXECUTE)->toArray();
//        return $resultado;
//    }
//    
//    public function ObtenerCursosEstado($estado){
//        $resultado = $this->getAdapter()->query("CALL Sp_ObtenerCursosEstado('{$estado}')", Adapter::QUERY_MODE_EXECUTE)->toArray();
//        return $resultado;
//    }
//    public function IngresarHorario($idCurso,$idDia,$estadoHorario){
//        $resultado = $this->getAdapter()->query("CALL Sp_IngresarHorario('{$idCurso}','{$idDia}','{$estadoHorario}')", Adapter::QUERY_MODE_EXECUTE)->toArray();
//        return $resultado;
//    }
    public function FiltrarHoraHorarioPorHorario($idHorario){
        $resultado = $this->getAdapter()->query("CALL Sp_FiltrarHoraHorarioPorHorario('{$idHorario}')", Adapter::QUERY_MODE_EXECUTE)->toArray();
        return $resultado;
    }
////    
//    public function FiltrarCurso($idCurso){
//        $resultado = $this->getAdapter()->query("CALL Sp_FiltrarCurso('{$idCurso}')", Adapter::QUERY_MODE_EXECUTE)->toArray();
//        return $resultado;
//    }
//    
//    public function FiltrarCursoEstado($idCurso,$estado){
//        $resultado = $this->getAdapter()->query("CALL Sp_FiltrarCursoEstado('{$idCurso}','{$estado}')", Adapter::QUERY_MODE_EXECUTE)->toArray();
//        return $resultado;
//    }
//    
////    
//    public function EliminarCurso($idCurso){
//        $resultado = $this->getAdapter()->query("CALL Sp_EliminarCurso('{$idCurso}')", Adapter::QUERY_MODE_EXECUTE)->toArray();
//        return $resultado;
//    }
//    
//    public function ModificarEstadoCurso($idCurso,$estado){
//        $resultado = $this->getAdapter()->query("CALL Sp_ModificarEstadoCurso('{$idCurso}','{$estado}')", Adapter::QUERY_MODE_EXECUTE)->toArray();
//        return $resultado;
//    }
    
//    
//    
//    
//    public function FiltrarPersona($idPersona){
//        $resultado = $this->getAdapter()->query("CALL Sp_FiltrarPersona('{$idPersona}')", Adapter::QUERY_MODE_EXECUTE)->toArray();
//        return $resultado;
//    }
//    
//    public function FiltrarPersonaPorIdentificacion($identificacion){
//        $resultado = $this->getAdapter()->query("CALL Sp_FiltrarPersonaPorIdentificacion('{$identificacion}')", Adapter::QUERY_MODE_EXECUTE)->toArray();
//        return $resultado;
//    }
//    
//
//    
//    public function EliminarLugarMisa($idLugarMisa){
//        $resultado = $this->getAdapter()->query("CALL Sp_EliminarLugarMisa('{$idLugarMisa}')", Adapter::QUERY_MODE_EXECUTE)->toArray();
//        return $resultado;
//    }
    
    
   



}