<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Nel\Modelo\Entity;

use Zend\Db\TableGateway\TableGateway;
use Zend\Db\Adapter\Adapter;

class Sacerdotes extends TableGateway
{
    public function __construct(Adapter $adapter = null, $databaseSchema = null, ResultSet $selectResultPrototype = null)
    {
        return parent::__construct('sacerdotes', $adapter, $databaseSchema, $selectResultPrototype);
    }
    
    public function ObtenerSacerdotes(){
        $resultado = $this->getAdapter()->query("CALL Sp_ObtenerSacerdotes()", Adapter::QUERY_MODE_EXECUTE)->toArray();
        return $resultado;
    }
    public function FiltrarSacerdote($idSacerdote){
        $resultado = $this->getAdapter()->query("CALL Sp_FiltrarSacerdote('{$idSacerdote}')", Adapter::QUERY_MODE_EXECUTE)->toArray();
        return $resultado;
    }
    
    public function FiltrarSacerdotePorPersona($idPersona){
        $resultado = $this->getAdapter()->query("CALL Sp_FiltrarSacerdotePorPersona('{$idPersona}')", Adapter::QUERY_MODE_EXECUTE)->toArray();
        return $resultado;
    }
    
    public function IngresarSacerdote($idPersona,$fechaIngresoSacerdote, $estado){
        $resultado = $this->getAdapter()->query("CALL Sp_IngresarSacerdote('{$idPersona}','{$fechaIngresoSacerdote}','{$estado}')", Adapter::QUERY_MODE_EXECUTE)->toArray();
        return $resultado;
    }
    public function EliminarSacerdote($idSacerdote){
        $resultado = $this->getAdapter()->query("CALL Sp_EliminarSacerdote('{$idSacerdote}')", Adapter::QUERY_MODE_EXECUTE)->toArray();
        return $resultado;
    }
//    
    public function ObtenerSacerdotesEstado($estadoSacerdote){
        $resultado = $this->getAdapter()->query("CALL Sp_ObtenerSacerdotesEstado('{$estadoSacerdote}')", Adapter::QUERY_MODE_EXECUTE)->toArray();
        return $resultado;
    }
//    
//    public function IngresarPersona($array)
//    {
//        $idIglesia = $array['idIglesia'];
//        $identificacion = $array['identificacion'];
//        $primerNombre = $array['primerNombre'];
//        $segundoNombre = $array['segundoNombre'];
//        $primerApellido = $array['primerApellido'];
//        $segundoApellido = $array['segundoApellido'];
//        $fechaNacimiento = $array['fechaNacimiento'];
//        $fechaRegistro = $array['fechaRegistro'];
//        $estadoPersona = $array['estadoPersona'];
//        $resultado =  $this->getAdapter()->query("CALL Sp_IngresarPersona('{$idIglesia}','{$identificacion}','{$primerNombre}','{$segundoNombre}','{$primerApellido}','{$segundoApellido}','{$fechaNacimiento}','{$fechaRegistro}','{$estadoPersona}')", Adapter::QUERY_MODE_EXECUTE)->toArray();
//        return $resultado;
//    }
//    
//    public function EliminarPersona($idPersona){
//        $resultado = $this->getAdapter()->query("CALL Sp_EliminarPersona('{$idPersona}')", Adapter::QUERY_MODE_EXECUTE)->toArray();
//        return $resultado;
//    }
//    
    
    
//    public function obtenerPersonas(){
////        $sql="CALL Sp_ObtenerPersonas()";
////        $statement = $this->getAdapter()->createStatement($sql);
//        
//        $resultado = $this->getAdapter()->query("CALL Sp_ObtenerPersonas()", Adapter::QUERY_MODE_EXECUTE)->toArray();
////        $fila = $statement->execute();
////        $resultado = $fila->current();
//        return $resultado;
//        
//    }









    
//    public  function obtenerUsuarios()
//    {
//        return  $this->select()->toArray();
//    }
//  
//    public function LoginUsuario($correo,$contrasena)
//    {
//        return $this->select(array('correo=?'=>$correo,'contrasena=?'=>$contrasena))->toArray();
//    }
    
//    public function filtrarUsuarioPorCorreo($correo)
//    {
//        return $this->select(array('correo=?'=>$correo))->toArray();
//    }
    
//    public function filtrarPersona($idPersona)
//    {
//        return $this->select(array('idPersona=?'=>$idPersona))->toArray();
//    }
    
//    public function filtrarTiendaActivo($idTienda)
//    {
//        return $this->select(array('idTienda=?'=>$idTienda,'estado=?'=>true))->toArray();
//    }
//    
//    
//    public function filtrarTiendaPorNombreUsuarioActivo($nombreUsuario)
//    {
//        return $this->select(array('nombreUsuario=?'=>$nombreUsuario,'estado=?'=>true))->toArray();
//    }
   
    
    
//    public function filtrarUsuarioPorUsuario($nombreUsuario)
//    {
//        return $this->select(array('nombreUsuario=?'=>$nombreUsuario))->toArray();
//    }
    
//    public function login($nombreUsuario)
//    {
//        return $this->select(array('nombreUsuario=?'=>$nombreUsuario))->toArray();
//    }
    

//    
//    public function filtrarUsuarioPorNombreUsuario($nombreUsuario)
//    {
//        return $this->select(array('nombreUsuario=?'=>$nombreUsuario))->toArray();
//    }
//    
//    public function filtrarUsuarioPorTipo($idTipoUSuario,$idUsuario)
//    {
//        return $this->select(array('idTipoUsuario=?'=>$idTipoUSuario,'idUsuario !=?'=>$idUsuario))->toArray();
//    }
    
//    public function ingresarTienda($array)
//    {
//        $inserted = $this->insert($array);
//        if($inserted)
//        {
//            return  $this->getLastInsertValue();
//        }  else {
//            return 0;
//        }
//    }
//    
//    public function actualizarUsuario($idUsuario, $array)
//    {
//        return (bool) $this->update($array,array('idUsuario=?'=>$idUsuario));
//    }

//    public function eliminarUsuario($idUsuario)
//    {
//        return $this->delete(array('idUsuario=?'=>$idUsuario));
//    }
   
}