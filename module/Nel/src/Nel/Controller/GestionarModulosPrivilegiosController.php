<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Nel\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\View\Model\JsonModel;
use Nel\Metodos\Metodos;
use Nel\Metodos\MetodosControladores;
use Nel\Modelo\Entity\AsignarModulo;
use Nel\Modelo\Entity\AsignarPrivilegio;
use Nel\Modelo\Entity\Privilegios;
use Nel\Modelo\Entity\Usuario;
use Nel\Modelo\Entity\Modulos;
use Zend\Session\Container;
use Zend\Crypt\Password\Bcrypt;
use Zend\Db\Adapter\Adapter;

class GestionarModulosPrivilegiosController extends AbstractActionController
{
    public $dbAdapter;
    
    public function obtenerformularioadministrarmodulosAction()
    {
        $this->layout("layout/administrador");
        $mensaje = '<div class="alert alert-danger text-center" role="alert">OCURRIÓ UN ERROR INESPERADO</div>';
        $validar = false;
        $sesionUsuario = new Container('sesionparroquia');
        if(!$sesionUsuario->offsetExists('idUsuario')){
            $mensaje = '<div class="alert alert-danger text-center" role="alert">NO HA INICIADO SESIÓN POR FAVOR RECARGUE LA PÁGINA</div>';
        }else{
            $this->dbAdapter=$this->getServiceLocator()->get('Zend\Db\Adapter');
            $idUsuario = $sesionUsuario->offsetGet('idUsuario');
            $objAsignarModulo = new AsignarModulo($this->dbAdapter);
            $AsignarModulo = $objAsignarModulo->FiltrarModuloPorIdentificadorYUsuario($idUsuario, 7);
            if (count($AsignarModulo)==0)
                $mensaje = '<div class="alert alert-danger text-center" role="alert">USTED NO TIENE PERMISOS PARA ESTE MÓDULO</div>';
            else{
                $objMetodosControlador =  new MetodosControladores();
                
                $request=$this->getRequest();
                if(!$request->isPost()){
                    $this->redirect()->toUrl($this->getRequest()->getBaseUrl().'/inicio/inicio');
                }else{               
                    $objMetodos = new Metodos();
                    $objUsuario = new Usuario($this->dbAdapter);
                    $objModulo = new Modulos($this->dbAdapter);
                    $post = array_merge_recursive(
                        $request->getPost()->toArray(),
                        $request->getFiles()->toArray()
                    );


                    $idUsuarioEncriptado = $post['id'];
                    $i = $post['i'];
                    $j = $post['j'];
                    if($idUsuarioEncriptado == NULL || $idUsuarioEncriptado == "" ){
                        $mensaje = '<div class="alert alert-danger text-center" role="alert">NO SE ENCUENTRA EL ÍNDICE DE LA PERSONA</div>';

                    }else if(!is_numeric($i)){
                        $mensaje = '<div class="alert alert-danger text-center" role="alert">EL IDENTIFICADOR DE LA FILA DEBE SER UN NÚMERO</div>';
                    }else if(!is_numeric($j)){
                        $mensaje = '<div class="alert alert-danger text-center" role="alert">EL IDENTIFICADOR DE LA FILA DEBE SER UN NÚMERO</div>';
                    }else{
                        $idUsuarioM = $objMetodos->desencriptar($idUsuarioEncriptado); 
                        $listaUsuarios = $objUsuario->FiltrarUsuario($idUsuario);
                        if(count($listaUsuarios) == 0){
                            $mensaje = '<div class="alert alert-danger text-center" role="alert">EL USUARIO SELECCIONADO NO EXISTE EN NUESTRA BASE DE DATOS</div>';
                        }else{
                            
                            $listaModulos = $objModulo->ObtenerModulos();
                            $cuerpoTabla  = '';
                            $optionModulos = '<option value="0">SELECCIONE LOS MÓDULOS A ASIGNAR</option>';
                            $iContador = 0;
                            foreach ($listaModulos as $valueModulos) {
                                $listaAsignarModulos = $objAsignarModulo->FiltrarAsignarModuloPorUsuarioYModulo($idUsuarioM, $valueModulos['idModulo'], 1);
                                $idModuloEncriptado = $objMetodos->encriptar($valueModulos['idModulo']);
                                if(count($listaAsignarModulos)==0){
                                    $optionModulos = $optionModulos.'<option value="'.$idModuloEncriptado.'">'.$valueModulos['nombreModulo'].'</option>';
                                    
                                }else{
                                    $idAsignarModuloEncriptado = $objMetodos->encriptar($listaAsignarModulos[0]['idAsignarModulo']);
                                    $botonEliminarModulo = '<button id="btnEliminarAsignarModulo'.$iContador.'" title="ELIMINAR MÓDULO '.$valueModulos['nombreModulo'].'" onclick="EliminarModulo(\''.$idUsuarioEncriptado.'\','.$i.','.$j.',\''.$idAsignarModuloEncriptado.'\','.$iContador.')" class="btn btn-danger btn-sm btn-flat"><i class="fa fa-times"></i></button>';
            
                                    $cuerpoTabla =$cuerpoTabla. '<tr>
                                            <td>'.$valueModulos['nombreModulo'].'</td>
                                            <td>'.$botonEliminarModulo.'</td>
                                            </tr>';

                                } 
                                $iContador++;
                            }
                           
                           $selectModulo = '<div class="col-lg-9">
                                   <input type="hidden" id="usuarioEncriptadoMO" name ="usuarioEncriptadoMO" value="'.$idUsuarioEncriptado.'">
                                       <input type="hidden" value="'.$i.'" id="imm" name="imm">
                                    <input type="hidden" value="'.$j.'" id="jmm" name="jmm">
                                        <select class="form-control" id="selectModulos" name="selectModulos">
                                        '.$optionModulos.'
                                    </select> 
                                    <br><br></div><div class="col-lg-3"><button type="submit" data-loading-text="GUARDANDO..." class="btn btn-primary btn-sm btn-flat" id="btnGuardarAsignarModulos" ><i class="fa fa-save"></i>GUARDAR</button></div>';
                            
                            $tabla = '';
                            if(!empty($cuerpoTabla)){
                            $tabla =$tabla. '<div class="col-lg-12"><label>MÓDULOS QUE YA HAN SIDO ASIGNADOS</label>                                                  
                                                    <table class="table table-bordered table-hover dataTable">
                                                    <thead>
                                                        <tr>
                                                            <td><b>NOMBRE MÓDULO</b></td>
                                                            <td><b>OPCIONES</b></td>
                                                        </tr>
                                                        </thead>
                                                        <tbody>
                                                            '.$cuerpoTabla.'
                                                        </tbody>
                                                    </table>
                                                    </div>';
                            }
                           
                            
                            
                            
                            $mensaje = '';
                            $validar = TRUE;
                            return new JsonModel(array('select'=>$selectModulo,'mensaje'=>$mensaje,'validar'=>$validar,'tabla'=>$tabla));
                        }
                    }

                }  
            }
        }
        
        return new JsonModel(array('mensaje'=>$mensaje,'validar'=>$validar));
    }
    
    
    public function eliminarmoduloAction()
    {
        $this->layout("layout/administrador");
        $mensaje = '<div class="alert alert-danger text-center" role="alert">OCURRIÓ UN ERROR INESPERADO</div>';
        $validar = false;
        $sesionUsuario = new Container('sesionparroquia');
        if(!$sesionUsuario->offsetExists('idUsuario')){
            $mensaje = '<div class="alert alert-danger text-center" role="alert">NO HA INICIADO SESIÓN POR FAVOR RECARGUE LA PÁGINA</div>';
        }else{
            $this->dbAdapter=$this->getServiceLocator()->get('Zend\Db\Adapter');
            $idUsuario = $sesionUsuario->offsetGet('idUsuario');
            $objAsignarModulo = new AsignarModulo($this->dbAdapter);
            $AsignarModulo = $objAsignarModulo->FiltrarModuloPorIdentificadorYUsuario($idUsuario, 7);
            if (count($AsignarModulo)==0)
                $mensaje = '<div class="alert alert-danger text-center" role="alert">USTED NO TIENE PERMISOS PARA ESTE MÓDULO</div>';
            else {
                $objMetodosC = new MetodosControladores();
                $validarprivilegio = $objMetodosC->ValidarPrivilegioAction($this->dbAdapter,$idUsuario, 7, 1);
                if ($validarprivilegio==false)
                    $mensaje = '<div class="alert alert-danger text-center" role="alert">USTED NO TIENE PRIVILEGIOS DE ELIMINAR DATOS PARA ESTE MÓDULO</div>';
                else{
                    $request=$this->getRequest();
                    if(!$request->isPost()){
                        $this->redirect()->toUrl($this->getRequest()->getBaseUrl().'/inicio/inicio');
                    }else{               
                        $objMetodos = new Metodos();
                        $objUsuario = new Usuario($this->dbAdapter);
                        $post = array_merge_recursive(
                            $request->getPost()->toArray(),
                            $request->getFiles()->toArray()
                        );
                        
                        $idUsuarioEncriptado = $post['usuario'];
                        $idAsignarModuloEncriptado = $post['idAsignarModulo'];
                        $fila = $post['fila'];
                        $fila2 = $post['fila2'];
                        
                        if($idAsignarModuloEncriptado == NULL || $idAsignarModuloEncriptado == ""){
                            $mensaje = '<div class="alert alert-danger text-center" role="alert">NO SE ENCUENTRA EL ÍNDICE DEL MÓDULO</div>';
                        }else if(!is_numeric($fila)){
                            $mensaje = '<div class="alert alert-danger text-center" role="alert">NO SE ENCUENTRA EL NÚMERO DE LA FILA</div>';
                        }else if(!is_numeric($fila2)){
                            $mensaje = '<div class="alert alert-danger text-center" role="alert">NO SE ENCUENTRA EL NÚMERO DE LA FILA</div>';
                        }else{
                            $idAsignarModulo = $objMetodos->desencriptar($idAsignarModuloEncriptado);
                            $listaAsignarModulo = $objAsignarModulo->FiltrarAsignarModulo($idAsignarModulo);
                            if(count($listaAsignarModulo) == 0){
                                $mensaje = '<div class="alert alert-danger text-center" role="alert">EL MÓDULO NO EXISTE</div>';
                            }else{
                                $resultado = $objAsignarModulo->ModificarEstadoEnAsginarModulo($idAsignarModulo, $listaAsignarModulo[0]['estadoAsignarModulo'], 0);
                                if(count($resultado) < 0){
                                    $mensaje = '<div class="alert alert-danger text-center" role="alert">NO SE DESHABILITÓ EL MÓDULO</div>';
                                }else{
                                    $mensaje = '';
                                    $validar = TRUE;
                                    return new JsonModel(array('idUsuarioEncriptado'=>$idUsuarioEncriptado, 'fila2'=>$fila2, 'fila'=>$fila,'mensaje'=>$mensaje,'validar'=>$validar));
                                }
                            }
                 
                        }   
                    }
                }
            }
        }
        return new JsonModel(array('mensaje'=>$mensaje,'validar'=>$validar));
    }
    
    
    public function administrarmodulosAction()
    {
        $this->layout("layout/administrador");
        $mensaje = '<div class="alert alert-danger text-center" role="alert">OCURRIÓ UN ERROR INESPERADO</div>';
        $validar = false;
        $sesionUsuario = new Container('sesionparroquia');
        if(!$sesionUsuario->offsetExists('idUsuario')){
            $mensaje = '<div class="alert alert-danger text-center" role="alert">NO HA INICIADO SESIÓN POR FAVOR RECARGUE LA PÁGINA</div>';
        }else{
            $this->dbAdapter=$this->getServiceLocator()->get('Zend\Db\Adapter');
            $idUsuario = $sesionUsuario->offsetGet('idUsuario');
            $objAsignarModulo = new AsignarModulo($this->dbAdapter);
            $objAsignarPrivilegio = new AsignarPrivilegio($this->dbAdapter);
            $objPrivilegios = new Privilegios($this->dbAdapter);
            $AsignarModulo = $objAsignarModulo->FiltrarModuloPorIdentificadorYUsuario($idUsuario, 7);
            if (count($AsignarModulo)==0)
                $mensaje = '<div class="alert alert-danger text-center" role="alert">USTED NO TIENE PERMISOS PARA ESTE MÓDULO</div>';
            else {
                $objMetodosC = new MetodosControladores();
                $validarprivilegio = $objMetodosC->ValidarPrivilegioAction($this->dbAdapter,$idUsuario, 7, 2);
                if ($validarprivilegio==false)
                    $mensaje = '<div class="alert alert-danger text-center" role="alert">USTED NO TIENE PRIVILEGIOS PARA MODIFICAR EN ESTE MÓDULO</div>';
                else{
                    $request=$this->getRequest();
                    if(!$request->isPost()){
                        $this->redirect()->toUrl($this->getRequest()->getBaseUrl().'/inicio/inicio');
                    }else{               
                        $objMetodos = new Metodos(); 
                        $objModulo = new  Modulos($this->dbAdapter);
                        $post = array_merge_recursive(
                            $request->getPost()->toArray(),
                            $request->getFiles()->toArray()
                        );
                        $idUsuarioEncriptadoMO = $post['usuarioEncriptadoMO'];
                        $imm = $post['imm'];
                        $jmm = $post['jmm'];
                        $idselectModulosEncriptado = $post['selectModulos'];
                               
                        $idModulo = $objMetodos->desencriptar($idselectModulosEncriptado);
                        $listaModulo = $objModulo->FiltrarModulo($idModulo);
                        
                        ini_set('date.timezone','America/Bogota'); 
                        $hoy = getdate();
                        $fechaSubida = $hoy['year']."-".$hoy['mon']."-".$hoy['mday']." ".$hoy['hours'].":".$hoy['minutes'].":".$hoy['seconds'];

                        if(count($listaModulo)==0)
                            $mensaje = '<div class="alert alert-danger text-center" role="alert">NO SE ENCUENTRA EL MÓDULO REGISTRADO EN LA BASE DE DATOS DEL SISTEMA</div>';
                        else{
                            $idUsuarioM= $objMetodos->desencriptar($idUsuarioEncriptadoMO);
                            $listaAsignarModulos = $objAsignarModulo->FiltrarAsignarModuloPorUsuarioYModulo($idUsuarioM, $idModulo, 0);
                            $listaPrivilegios = $objPrivilegios->ObtenerPrivilegios();
                            
                            if(count($listaAsignarModulos)>0){
                                $elemento= $objAsignarModulo->ModificarEstadoEnAsginarModulo ($listaAsignarModulos[0]['idAsignarModulo'], $listaAsignarModulos[0]['estadoAsignarModulo'], 1);
                                foreach($listaPrivilegios as $valuePrivilegio){
                                    $res = $objAsignarPrivilegio->FiltrarAsignarPrivilegio($valuePrivilegio['idPrivilegio'], $listaAsignarModulos[0]['idAsignarModulo']);
                                    if(count($res)==0){
                                        $elementoAsignarP = $objAsignarPrivilegio->IngresarAsignarPrivilegio($valuePrivilegio['idPrivilegio'], $listaAsignarModulos[0]['idAsignarModulo'], $fechaSubida, 0);
                                        $res=0;                                        
                                    }
                                }
                                $mensaje = '<div class="alert alert-success text-center" role="alert">MODULO ASIGNADO CORRECTAMENTE</div>';
                                $validar = TRUE;

                                return new JsonModel(array('idUsuarioEncriptado'=>$idUsuarioEncriptadoMO,'jmm'=>$jmm,'imm'=>$imm,'mensaje'=>$mensaje,'validar'=>$validar));

                                
                            }                            
                            else
                            {
                                $elementoAsignarModulo = $objAsignarModulo->IngresarAsignarModulo($idUsuarioM, $idModulo, $fechaSubida, 1);

                                foreach ($listaPrivilegios as $valuePrivilegio) {
                                    $elementoAsignarPrivilegio = $objAsignarPrivilegio->IngresarAsignarPrivilegio($valuePrivilegio['idPrivilegio'], $elementoAsignarModulo[0]['idAsignarModulo'], $fechaSubida, 0);                                    
                                }

                                $mensaje = '<div class="alert alert-success text-center" role="alert">MODULO ASIGNADO CORRECTAMENTE</div>';
                                $validar = TRUE;

                                return new JsonModel(array('idUsuarioEncriptado'=>$idUsuarioEncriptadoMO,'jmm'=>$jmm,'imm'=>$imm,'mensaje'=>$mensaje,'validar'=>$validar));

                            }             
                            }
                    }
                }
            }
        }
        return new JsonModel(array('mensaje'=>$mensaje,'validar'=>$validar));
    }
    
    
    
    public function cargarprivilegiospormoduloAction()
    {
        $this->layout("layout/administrador");
        $mensaje = '<div class="alert alert-danger text-center" role="alert">OCURRIÓ UN ERROR INESPERADO</div>';
        $validar = false;
        $sesionUsuario = new Container('sesionparroquia');
        if(!$sesionUsuario->offsetExists('idUsuario')){
            $mensaje = '<div class="alert alert-danger text-center" role="alert">NO HA INICIADO SESIÓN POR FAVOR RECARGUE LA PÁGINA</div>';
        }else{
            $this->dbAdapter=$this->getServiceLocator()->get('Zend\Db\Adapter');
            $idUsuario = $sesionUsuario->offsetGet('idUsuario');
            $objAsignarModulo = new AsignarModulo($this->dbAdapter);
            $objAsignarPrivilegio = new AsignarPrivilegio($this->dbAdapter);
            $objPrivilegios = new Privilegios($this->dbAdapter);
            $AsignarModulo = $objAsignarModulo->FiltrarModuloPorIdentificadorYUsuario($idUsuario, 7);
            if (count($AsignarModulo)==0)
                $mensaje = '<div class="alert alert-danger text-center" role="alert">USTED NO TIENE PERMISOS PARA ESTE MÓDULO</div>';
            else {
                $objMetodosC = new MetodosControladores();
                $validarprivilegio = $objMetodosC->ValidarPrivilegioAction($this->dbAdapter,$idUsuario, 7, 2);
                if ($validarprivilegio==false)
                    $mensaje = '<div class="alert alert-danger text-center" role="alert">USTED NO TIENE PRIVILEGIOS PARA MODIFICAR EN ESTE MÓDULO</div>';
                else{
                    $request=$this->getRequest();
                    if(!$request->isPost()){
                        $this->redirect()->toUrl($this->getRequest()->getBaseUrl().'/inicio/inicio');
                    }else{               
                        $objMetodos = new Metodos(); 
                        $objModulo = new  Modulos($this->dbAdapter);
                        $objAsignarPrivilegios = new AsignarPrivilegio($this->dbAdapter);
                        $post = array_merge_recursive(
                            $request->getPost()->toArray(),
                            $request->getFiles()->toArray()
                        );
//                        $idUsuarioEncriptadoP = $post['usuarioEncriptadoP'];
                        $imm = $post['i'];
                        $jmm = $post['j'];
                        $idselectModulosEncriptado = $post['id'];
                               
                        $idAsignarModulo = $objMetodos->desencriptar($idselectModulosEncriptado);
                        $listaAsignarModulos = $objAsignarModulo->FiltrarAsignarModulo($idAsignarModulo);

                        if(count($listaAsignarModulos)>0){
                            $cuerpoTabla='';
                                
                                $listaAsignarPrivilegios = $objAsignarPrivilegios->FiltrarAsignarPrivilegioPorIdAsignarModulo($idAsignarModulo); 
                                foreach ($listaAsignarPrivilegios as $valueAsignarPrivilegios) {
                                    $idAsignarPrivilegioEncriptado = $objMetodos->encriptar($valueAsignarPrivilegios['idAsignarPrivilegios']);
                                    if($valueAsignarPrivilegios['estadoAsignacion']==0)
                                      $botonAsignarPrivilegio = '<button id="btnHabilitarAsignarPrivilegio'.$imm.'" title="HABILITAR PRIVILEGIO '.$valueAsignarPrivilegios['nombrePrivilegio'].'" onclick="CambiarEstadoPrivilegio(\''.$idAsignarPrivilegioEncriptado.'\','.$imm.','.$jmm.')" class="btn btn-danger btn-sm btn-flat"><i class="fa fa-times"></i></button>';
                                    else 
                                      $botonAsignarPrivilegio = '<button id="btnHabilitartAsignarPrivilegio'.$imm.'" title="DESHABILITAR PRIVILEGIO '.$valueAsignarPrivilegios['nombrePrivilegio'].'" onclick="CambiarEstadoPrivilegio(\''.$idAsignarPrivilegioEncriptado.'\','.$imm.','.$jmm.')" class="btn btn-success btn-sm btn-flat"><i class="fa fa-check"></i></button>';

                                     $cuerpoTabla =$cuerpoTabla. '<tr>
                                            <td>'.$valueAsignarPrivilegios['nombrePrivilegio'].'</td>
                                            <td>'.$botonAsignarPrivilegio.'</td>
                                            </tr>';
                                }                                
                                
                                $tabla = '';
                                if(!empty($cuerpoTabla)){
                                $tabla =$tabla. '<div class="col-lg-12"><label>PRIVILEGIOS DEL MÓDULO SELECCIONADO</label>                                                  
                                                    <table class="table table-bordered table-hover dataTable">
                                                    <thead>
                                                        <tr>
                                                            <td><b>NOMBRE PRIVILEGIO</b></td>
                                                            <td><b>OPCIONES</b></td>
                                                        </tr>
                                                        </thead>
                                                        <tbody>
                                                            '.$cuerpoTabla.'
                                                        </tbody>
                                                    </table>
                                                    </div>';
                                }
                                $mensaje = '<div class="alert alert-success text-center" role="alert">PRIVILEGIOS CARGADOS CORRECTAMENTE</div>';
                                $validar = TRUE;

                            return new JsonModel(array('tabla'=>$tabla,'jmm'=>$jmm,'imm'=>$imm,'mensaje'=>$mensaje,'validar'=>$validar));

                               
                        }                            
                        else
                        {      
                            $mensaje = '<div class="alert alert-danger text-center" role="alert">NO CONTIENE MODULOS ASIGNADOS</div>';
                        }                       
                    }
                }
            }
        }
        return new JsonModel(array('mensaje'=>$mensaje,'validar'=>$validar));
    }
 
    
    public function administrarprivilegioAction()
    {
        $this->layout("layout/administrador");
        $mensaje = '<div class="alert alert-danger text-center" role="alert">OCURRIÓ UN ERROR INESPERADO</div>';
        $validar = false;
        $sesionUsuario = new Container('sesionparroquia');
        if(!$sesionUsuario->offsetExists('idUsuario')){
            $mensaje = '<div class="alert alert-danger text-center" role="alert">NO HA INICIADO SESIÓN POR FAVOR RECARGUE LA PÁGINA</div>';
        }else{
            $this->dbAdapter=$this->getServiceLocator()->get('Zend\Db\Adapter');
            $idUsuario = $sesionUsuario->offsetGet('idUsuario');
            $objAsignarModulo = new AsignarModulo($this->dbAdapter);
            $objAsignarPrivilegios = new AsignarPrivilegio($this->dbAdapter);
            $AsignarModulo = $objAsignarModulo->FiltrarModuloPorIdentificadorYUsuario($idUsuario, 7);
            if (count($AsignarModulo)==0)
                $mensaje = '<div class="alert alert-danger text-center" role="alert">USTED NO TIENE PERMISOS PARA ESTE MÓDULO</div>';
            else{
                
                $request=$this->getRequest();
                if(!$request->isPost()){
                    $this->redirect()->toUrl($this->getRequest()->getBaseUrl().'/inicio/inicio');
                }else{               
                    $objMetodos = new Metodos();
                    $post = array_merge_recursive(
                        $request->getPost()->toArray(),
                        $request->getFiles()->toArray()
                    );


                    $idAsignarPrivilegioEncriptado = $post['id'];
                    $i = $post['i'];
                    $j = $post['j'];
                    if($idAsignarPrivilegioEncriptado == NULL || $idAsignarPrivilegioEncriptado == "" ){
                        $mensaje = '<div class="alert alert-danger text-center" role="alert">NO SE ENCUENTRA EL ÍNDICE DEL ASIGNAR PRIVILEGIO</div>';
                    }else if(!is_numeric($i)){
                        $mensaje = '<div class="alert alert-danger text-center" role="alert">EL IDENTIFICADOR DE LA FILA DEBE SER UN NÚMERO</div>';
                    }else if(!is_numeric($j)){
                        $mensaje = '<div class="alert alert-danger text-center" role="alert">EL IDENTIFICADOR DE LA FILA DEBE SER UN NÚMERO</div>';
                    }else{
                        $idAsignarPrivilegio = $objMetodos->desencriptar($idAsignarPrivilegioEncriptado); 
                        $listaAsignarPrivilegio = $objAsignarPrivilegios->FiltrarAsignarPrivilegioPorId($idAsignarPrivilegio);
                        if(count($listaAsignarPrivilegio) == 0){
                            $mensaje = '<div class="alert alert-danger text-center" role="alert">EL ELEMENTO ASIGNAR PRIVILEGIO NO EXISTE EN NUESTRA BASE DE DATOS</div>';
                        }else{
                            $idAsignarModuloEncriptado = $objMetodos->encriptar($listaAsignarPrivilegio[0]['idAsignarModulo']);
                            ini_set('date.timezone','America/Bogota'); 
                            $hoy = getdate();
                            $fechaActualizacion = $hoy['year']."-".$hoy['mon']."-".$hoy['mday']." ".$hoy['hours'].":".$hoy['minutes'].":".$hoy['seconds'];
                            $estadoActual = $listaAsignarPrivilegio[0]['estadoAsignacion'];
                            $nuevoEstado =0;
                            $mensaje = '<div class="alert alert-success text-center" role="alert">SE DESHABILITÓ DE FORMA EXITOSA EL PRIVILEGIO</div>';                         
                            if($estadoActual==0)
                            {
                                $mensaje = '<div class="alert alert-success text-center" role="alert">SE HABILITÓ DE FORMA EXITOSA EL PRIVILEGIO</div>';                         
                                $nuevoEstado=1;    
                            }
                                
                            $resultado = $objAsignarPrivilegios->ModificarEstadoAsignarPrivilegio($idAsignarPrivilegio, $fechaActualizacion, $nuevoEstado);
                            if(count($resultado)==0)
                              $mensaje = '<div class="alert alert-danger text-center" role="alert">NO SE PUDO ACUTALIZAR EL ESTADO DEL PRIVILEGIO</div>';                         
                            
                            $div = '<div class="col-lg-9">
                                   <input type="hidden" id="selectModulosE" name ="selectModulosE" value="'.$idAsignarModuloEncriptado.'">
                                   <input type="hidden" value="'.$i.'" id="ip" name="ip">
                                    <input type="hidden" value="'.$j.'" id="jp" name="jp">'
                                    . '</div>';
                            
                            
                            $validar = TRUE;
                            return new JsonModel(array('mensaje'=>$mensaje,'validar'=>$validar));
                            }
                        }
                    }

                }  
            }
        
        return new JsonModel(array('mensaje'=>$mensaje,'validar'=>$validar));
    }
    
     public function obtenerformularioadministrarprivilegiosAction()
    {
        $this->layout("layout/administrador");
        $mensaje = '<div class="alert alert-danger text-center" role="alert">OCURRIÓ UN ERROR INESPERADO</div>';
        $validar = false;
        $sesionUsuario = new Container('sesionparroquia');
        if(!$sesionUsuario->offsetExists('idUsuario')){
            $mensaje = '<div class="alert alert-danger text-center" role="alert">NO HA INICIADO SESIÓN POR FAVOR RECARGUE LA PÁGINA</div>';
        }else{
            $this->dbAdapter=$this->getServiceLocator()->get('Zend\Db\Adapter');
            $idUsuario = $sesionUsuario->offsetGet('idUsuario');
            $objAsignarModulo = new AsignarModulo($this->dbAdapter);
            $objAsignarPrivilegios = new AsignarPrivilegio($this->dbAdapter);
            $AsignarModulo = $objAsignarModulo->FiltrarModuloPorIdentificadorYUsuario($idUsuario, 7);
            if (count($AsignarModulo)==0)
                $mensaje = '<div class="alert alert-danger text-center" role="alert">USTED NO TIENE PERMISOS PARA ESTE MÓDULO</div>';
            else{
                $objMetodosControlador =  new MetodosControladores();
                
                $request=$this->getRequest();
                if(!$request->isPost()){
                    $this->redirect()->toUrl($this->getRequest()->getBaseUrl().'/inicio/inicio');
                }else{               
                    $objMetodos = new Metodos();
                    $objUsuario = new Usuario($this->dbAdapter);
                    $objModulo = new Modulos($this->dbAdapter);
                    $post = array_merge_recursive(
                        $request->getPost()->toArray(),
                        $request->getFiles()->toArray()
                    );
                    $idUsuarioEncriptado = $post['id'];
                    $i = $post['i'];
                    $j = $post['j'];
                    if($idUsuarioEncriptado == NULL || $idUsuarioEncriptado == "" ){
                        $mensaje = '<div class="alert alert-danger text-center" role="alert">NO SE ENCUENTRA EL ÍNDICE DE LA PERSONA</div>';
                    }else if(!is_numeric($i)){
                        $mensaje = '<div class="alert alert-danger text-center" role="alert">EL IDENTIFICADOR DE LA FILA DEBE SER UN NÚMERO</div>';
                    }else if(!is_numeric($j)){
                        $mensaje = '<div class="alert alert-danger text-center" role="alert">EL IDENTIFICADOR DE LA FILA DEBE SER UN NÚMERO</div>';
                    }else{
                        $idUsuarioM = $objMetodos->desencriptar($idUsuarioEncriptado); 
                        $listaUsuarios = $objUsuario->FiltrarUsuario($idUsuario);
                        if(count($listaUsuarios) == 0){
                            $mensaje = '<div class="alert alert-danger text-center" role="alert">EL USUARIO SELECCIONADO NO EXISTE EN NUESTRA BASE DE DATOS</div>';
                        }else{
                            
                            $listaAsignarModulos = $objAsignarModulo->FiltrarModulosPorUsuario($idUsuarioM);
                           
                            $optionModulos = '<option value="0">SELECCIONE UN MÓDULO</option>';
                            foreach ($listaAsignarModulos as $valueAsignarModulos) {
                                $idAsignarModuloEncriptado = $objMetodos->encriptar($valueAsignarModulos['idAsignarModulo']);
                                $optionModulos = $optionModulos.'<option value="'.$idAsignarModuloEncriptado.'">'.$valueAsignarModulos['nombreModulo'].'</option>'; 
                                
                            }
                           
                           $selectModulo = '<div class="col-lg-12">
                                   <input type="hidden" id="usuarioEncriptadoP" name ="usuarioEncriptadoP" value="'.$idUsuarioEncriptado.'">
                                       <input type="hidden" value="'.$i.'" id="ip" name="ip">
                                    <input type="hidden" value="'.$j.'" id="jp" name="jp">
                                        <select onchange="cargandoPrivilegios(\'#contenedorTablaPrivilegios\');CargarPrivilegiosPorModulo();" class="form-control" id="selectModulosE" name="selectModulosE">
                                        '.$optionModulos.'
                                    </select> 
                                    <br><br></div>';
                            
                           
                            }
                            
                            $mensaje = '';
                            $validar = TRUE;
                            return new JsonModel(array('select'=>$selectModulo,'mensaje'=>$mensaje,'validar'=>$validar));
                        }
                    }
                }  
            }
        
        return new JsonModel(array('mensaje'=>$mensaje,'validar'=>$validar));
    }
      


}