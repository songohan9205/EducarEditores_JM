<?php

namespace App\Http\Controllers;

use App\Models\Cargo;
use App\Models\Empleado;
use App\Models\Gasto;
use App\Models\Recarga;
use App\Models\Transferencia;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class educarController extends Controller
{

    /**
     * Lista de usuarios
     *
     * @return array Usuarios
     */
    public function index()
    {
        $lista = Empleado::select('empleado.id', 'documento', 'nombre_cargo', 'primer_nombre', 'segundo_nombre', 'primer_apellido', 'segundo_apellido', 'saldo')
            ->join('cargo', 'empleado.cargo_id', '=', 'cargo.id')
            ->get();

        $usuario = $this->properties($lista);
        return response()->json([$usuario]);
    }

    /**
     * Lista de usuarios filtrado por número de documento
     *
     * @param  string  $documento
     * @return array   datos del Usuario
     */
    public function show(Request $request)
    {
        $documento = $request->post('documento');
        $lista = Empleado::select('empleado.id', 'documento', 'nombre_cargo', 'primer_nombre', 'segundo_nombre', 'primer_apellido', 'segundo_apellido', 'saldo')
            ->join('cargo', 'empleado.cargo_id', '=', 'cargo.id')
            ->where('documento', $documento)
            ->get();

        if (count($lista) < 1) {
            return 'No se encuentra un usuario registrado con ese número de documento. Valide el número ingresado y el nombre del campo en la API';
        }
        $usuario = $this->properties($lista);
        return response()->json([$usuario]);
    }

    /**
     * Lista de usuarios filtrado por número de documento
     *
     * @param  string  $campo
     * @param  string  $dato
     * @return array   datos del Usuario
     */
    public function showTwoParameters(Request $request)
    {
        $dato  = $request->post('dato');
        $campo = $request->post('campo');

        try {
            $lista = Empleado::select('empleado.id', 'documento', 'nombre_cargo', 'primer_nombre', 'segundo_nombre', 'primer_apellido', 'segundo_apellido', 'saldo')
                ->join('cargo', 'empleado.cargo_id', '=', 'cargo.id')
                ->where($campo, 'like', '%' . $dato . '%')
                ->get();

            $usuario = $this->properties($lista);
            return response()->json([$usuario]);
        } catch (\Throwable $e) {
            return 'Se ha presentado un error en el ingreso de los parámetros. Valide que los nombres de los campos en la API sean correctos. Excepción generada: ' . $e->getMessage();
        }
    }

    /**
     * Complemento de las funciones de búsqueda de información para los usuarios
     *
     * @param  array $data
     * @return array Información completa del usuario
     */
    private function properties($data)
    {
        $i = 0;
        foreach ($data as $usu) {
            $usuario[$i]['General'] = $usu;

            $usuario[$i]['Transferencias_recibidas'] = Transferencia::select(
                'transferencia.created_at as fecha',
                'transferencia',
                'primer_nombre as nombre transfiere',
                'primer_apellido as apellido transfiere'
            )
                ->join('empleado', 'empleado.id', '=', 'transferencia.empleado_recibe')
                ->where('empleado_recibe', $usu['id'])
                ->get();

            $usuario[$i]['Transferencias_realizadas'] = Transferencia::select('transferencia.created_at as fecha', 'transferencia', 'documento as Recibido por')
                ->join('empleado', 'empleado.id', '=', 'transferencia.empleado_recibe')
                ->where('empleado_transfiere',  $usu['id'])
                ->get();

            $usuario[$i]['Recargas'] = Recarga::select('created_at as fecha', 'recarga')->where('empleado_id', $usu['id'])->get();
            $i++;
        }
        return $usuario;
    }

    /**
     * Realizar recarga al saldo de la cuenta del usuario
     *
     * @param  string  $documento
     * @param  int     $recarga
     * @return string  Proceso confirmado
     */
    public function reloadCount(Request $request)
    {
        $documento = $request->post('documento');
        $recarga   = $request->post('recarga');

        $validator = \Validator::make($request->all(), [
            'documento' => 'required|max:20|min:5',
            'recarga'   => 'required|numeric|gt:0',
        ]);

        if ($validator->fails()) {
            return $validator->errors();
            die;
        } else {
            /** Buscar que el documento esté registrado */
            $employer = $this->saldoUsuario($documento);
            if (count($employer) < 1) {
                return 'El número de documento no está registrado. Valide el número ingresado y el nombre del campo en la API';
                die;
            }

            /** Registrar evento de la recarga */
            Recarga::create([
                'empleado_id' => $employer[0]['id'],
                'recarga'     => $recarga
            ]);

            /** Actualizar dato del usuario */
            $saldo = $employer[0]['saldo'] + $recarga;
            Empleado::where('id', $employer[0]['id'])
                ->update(['saldo' => $saldo]);

            return 'La recarga ha sido exitosa: el nuevo saldo del  usuario ' . $employer[0]['primer_nombre'] . ' ' . $employer[0]['primer_apellido'] . ' es de ' . $saldo;
        }
    }

    /**
     * Transferir saldo de un usuario a otro
     *
     * @param  string  $documento
     * @param  int     $recarga
     * @return string  Proceso confirmado
     */
    public function transfer(Request $request)
    {
        $transfiere = $request->post('usuarioTransfiere');
        $recibe     = $request->post('usuarioRecibe');
        $total      = $request->post('transferencia');

        $validator = \Validator::make($request->all(), [
            'usuarioTransfiere' => 'required|max:20|min:5',
            'usuarioRecibe'     => 'required|max:20|min:5',
            'transferencia'     => 'required|numeric|gt:0'
        ]);

        if ($validator->fails()) {
            return $validator->errors();
            die;
        } else {
            if ($transfiere === $recibe) {
                return 'El número de documento del usuario que transfiere y que recibe no puede ser el mismo';
                die;
            }

            /** Buscar que el documento esté registrado y tenga saldo de quien transfiere */
            $usTransf = $this->saldoUsuario($transfiere);
            if (count($usTransf) < 1) {
                return 'El número de documento del usuario que transfiere no está registrado. Valide el número ingresado y el nombre del campo en la API';
                die;
            } else if ($usTransf[0]['saldo'] < $total) {
                return 'El usuario que va a transferir el dinero no cuenta con suficiente saldo (saldo actual: ' . $usTransf[0]['saldo'] . ')';
                die;
            }

            /** Validación del usuario que recibe */
            $usRecibe = $this->saldoUsuario($recibe);
            if (count($usRecibe) < 1) {
                return 'El número de documento del usuario que recibe no está registrado. Valide el número ingresado y el nombre del campo en la API';
                die;
            }

            /** Registrar evento de la transferencia */
            Transferencia::create([
                'empleado_transfiere' => $usTransf[0]['id'],
                'empleado_recibe'     => $usRecibe[0]['id'],
                'transferencia'       => $total
            ]);

            /** Actualizar dato del usuario que recibe*/
            $totalRecibe = $usRecibe[0]['saldo'] + $total;
            Empleado::where('documento', $recibe)->update(['saldo' => $totalRecibe]);

            /** Actualizar dato del usuario que transfiere*/
            $totalTransf = $usTransf[0]['saldo'] - $total;
            Empleado::where('documento', $transfiere)->update(['saldo' => $totalTransf]);

            return 'Transferencia exitosa. 
            El saldo actual de la persona que recibe (' . $usRecibe[0]['primer_nombre'] . ' ' . $usRecibe[0]['primer_apellido'] . ' es de ' . $totalRecibe . ') 
            y de la persona que transfiere (' . $usTransf[0]['primer_nombre'] . ' ' . $usTransf[0]['primer_apellido'] . ' es de ' . $totalTransf . ')';
        }
    }

    /**
     * Consultar saldo de un empleado
     *
     * @param  string  $documento     
     * @return array   Saldo
     */
    private function saldoUsuario($documento)
    {
        $data = Empleado::select('*')->where('documento', $documento)->get();
        return $data;
    }

    /**
     * Registro de un nuevo usuario
     *
     * @param  string  $documento
     * @param  string  $primerNombre
     * @param  string  $segundoNombre
     * @param  string  $primerApellido
     * @param  string  $segundoApellido
     * @param  int     $saldo
     * @param  int     $cargoId
     * @return string  Proceso confirmado
     */
    public function create(Request $request)
    {
        $documento       = $request->post('documento');
        $primerNombre    = $request->post('primer_nombre');
        $segundoNombre   = $request->post('segundo_nombre');
        $primerApellido  = $request->post('primer_apellido');
        $segundoApellido = $request->post('segundo_apellido');
        $saldo           = $request->post('saldo');
        $cargoId         = $request->post('cargo_id');

        $cargos = $this->validatePosition();
        $validator = \Validator::make($request->all(), [
            'documento'        => 'required|max:20|min:5|string|unique:empleado',
            'primer_nombre'    => 'required|string|max:60',
            'segundo_nombre'   => 'string|max:60|nullable',
            'primer_apellido'  => 'required|string|max:60',
            'segundo_apellido' => 'string|max:60|nullable',
            'saldo'            => 'required|numeric|gt:-1',
            'cargo_id'         => ['required', 'numeric', Rule::in($cargos)]
        ]);

        if ($validator->fails()) {
            return $validator->errors();
            die;
        } else {

            try {
                /** Registrar usuario */
                Empleado::create([
                    'documento'        => $documento,
                    'primer_nombre'    => $primerNombre,
                    'segundo_nombre'   => $segundoNombre,
                    'primer_apellido'  => $primerApellido,
                    'segundo_apellido' => $segundoApellido,
                    'saldo'            => $saldo,
                    'cargo_id'         => $cargoId                    
                ]);
                return 'Se ha creado el usuario exitosamente';
                
            } catch (\Throwable $e) {
                return 'Se ha presentado un error en el registro del usuario. Valide que los nombres de los campos en la API sean correctos. Excepción generada: ' . $e->getMessage();
            }
        }
    }

    /**
     * Listar los cargos para validar el registro de un nuevo usuario
     *     
     * @return array  Lista de cargos
     */
    private function validatePosition()
    {
        $car = [];
        $response = Cargo::all();
        foreach ($response as $type) {
            array_push($car, $type['id']);
        }
        return $car;
    }

    /**
     * Listar los cargos para mostrarlos en API
     *     
     * @return array  Lista de cargos
     */
    public function position()
    {
        $response = Cargo::select('id', 'nombre_cargo')->get();
        return response()->json([$response]);
    }

    /**
     * Función para registrar los gastos realizados por un usuario
     *     
     * @return array  Lista de cargos
     */
    public function spending(Request $request)
    {

        $documento   = $request->post('documento');
        $gasto       = $request->post('gasto');
        $descripcion = $request->post('descripcion');

        $validator = \Validator::make($request->all(), [
            'documento'   => 'required|max:20|min:5|string',
            'gasto'       => 'required|numeric|gt:0',
            'descripcion' => 'string|max:500',
        ]);

        if ($validator->fails()) {
            return $validator->errors();
            die;
        } else {

            /** Validación del usuario que realiza el gasto */
            $usuario = $this->saldoUsuario($documento);
            if (count($usuario) < 1) {
                return 'El número de documento del usuario que recibe no está registrado. Valide el número ingresado y el nombre del campo en la API';
                die;
            } else if ($usuario[0]['saldo'] < $gasto) {
                return 'El usuario que va a realizar el gasto no cuenta con suficiente saldo (saldo actual: ' . $usuario[0]['saldo'] . ')';
                die;
            }

            try {
                /** Registrar gasto */
                Gasto::create([
                    'empleado_id' => $usuario[0]['id'],
                    'gasto'       => $gasto,
                    'descripcion' => $descripcion,
                ]);

                /** Actualizar dato del usuario */
                $saldo = $usuario[0]['saldo'] - $gasto;
                Empleado::where('id', $usuario[0]['id'])->update(['saldo' => $saldo]);
                return 'El gasto se ha registrado exitosamente: el nuevo saldo del  usuario ' . $usuario[0]['primer_nombre'] . ' ' . $usuario[0]['primer_apellido'] . ' es de ' . $saldo;

            } catch (\Throwable $e) {
                return 'Se ha presentado un error en el registro del gasto. Valide que los nombres de los campos en la API sean correctos. Excepción generada: ' . $e->getMessage();
            }
        }
    }
}
