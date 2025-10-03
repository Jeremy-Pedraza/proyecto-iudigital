<?php

namespace App\Http\Controllers\Sigeruta\Comerciales;

use App\Http\Controllers\Controller;
use App\Application\Comerciales\CreateComercialesUseCase;
use App\Application\Comerciales\DeleteComercialesUseCase;
use App\Application\Comerciales\GetComercialByIdUseCase;
use App\Application\Comerciales\ListComercialesUseCase;
use App\Application\Comerciales\UpdateComercialesUseCase;
use Illuminate\Http\Request;

class ComercialesController extends Controller
{
    private $createComercialesUseCase;
    private $deleteComercialesUseCase;
    private $getComercialByIdUseCase;
    private $listComercialesUseCase;
    private $updateComercialesUseCase;

    public function __construct(
        CreateComercialesUseCase $createComercialesUseCase,
        DeleteComercialesUseCase $deleteComercialesUseCase,
        GetComercialByIdUseCase $getComercialByIdUseCase,
        ListComercialesUseCase $listComercialesUseCase,
        UpdateComercialesUseCase $updateComercialesUseCase
    ) {
        $this->createComercialesUseCase = $createComercialesUseCase;
        $this->deleteComercialesUseCase = $deleteComercialesUseCase;
        $this->getComercialByIdUseCase = $getComercialByIdUseCase;
        $this->listComercialesUseCase = $listComercialesUseCase;
        $this->updateComercialesUseCase = $updateComercialesUseCase;
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $comerciales = $this->listComercialesUseCase->execute();
            return view('planificacion.comerciales.index', compact('comerciales'));
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Error al listar los comerciales: ' . $e->getMessage()]);
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('planificacion.comerciales.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            // Campos obligatorios
            'nombre' => [
                'required',
                'string',
                'min:2',
                'max:100',
                'regex:/^[a-záéíóúñA-ZÁÉÍÓÚÑ\s]+$/' // Solo letras y espacios
            ],
            'apellido' => [
                'required',
                'string',
                'min:2',
                'max:100',
                'regex:/^[a-záéíóúñA-ZÁÉÍÓÚÑ\s]+$/'
            ],
            'email' => [
                'required',
                'email:rfc,dns',
                'max:150',
                'unique:comerciales,email'
            ],

            // Campos opcionales
            'telefono' => [
                'nullable',
                'string',
                'min:10',
                'max:20',
                'regex:/^[\d\s\+\-\(\)]+$/' // Números, espacios, +, -, (), espacios
            ],
            'codigo_empleado' => [
                'nullable',
                'string',
                'max:50',
                'unique:comerciales,codigo_empleado',
                'regex:/^[A-Z0-9\-]+$/' // Letras mayúsculas, números y guiones
            ],
            'departamento' => [
                'nullable',
                'string',
                'in:ventas,marketing,atencion_cliente,desarrollo_negocio'
            ],
            'direccion' => [
                'nullable',
                'string',
                'max:500'
            ],
            'fecha_ingreso' => [
                'nullable',
                'date',
                'before_or_equal:today'
            ],
            'salario_base' => [
                'nullable',
                'numeric',
                'min:0',
                'max:9999999.99'
            ],
            'activo' => [
                'nullable',
                'boolean'
            ]
        ], [
            // Mensajes personalizados
            'nombre.required' => 'El nombre es obligatorio.',
            'nombre.min' => 'El nombre debe tener al menos :min caracteres.',
            'nombre.max' => 'El nombre no puede exceder :max caracteres.',
            'nombre.regex' => 'El nombre solo puede contener letras y espacios.',

            'apellido.required' => 'El apellido es obligatorio.',
            'apellido.min' => 'El apellido debe tener al menos :min caracteres.',
            'apellido.max' => 'El apellido no puede exceder :max caracteres.',
            'apellido.regex' => 'El apellido solo puede contener letras y espacios.',

            'email.required' => 'El email es obligatorio.',
            'email.email' => 'El formato del email no es válido.',
            'email.unique' => 'Este email ya está registrado.',
            'email.max' => 'El email no puede exceder :max caracteres.',

            'telefono.min' => 'El teléfono debe tener al menos :min caracteres.',
            'telefono.max' => 'El teléfono no puede exceder :max caracteres.',
            'telefono.regex' => 'El formato del teléfono no es válido.',

            'codigo_empleado.unique' => 'Este código de empleado ya está en uso.',
            'codigo_empleado.max' => 'El código de empleado no puede exceder :max caracteres.',
            'codigo_empleado.regex' => 'El código debe contener solo letras mayúsculas, números y guiones.',

            'departamento.in' => 'El departamento seleccionado no es válido.',

            'direccion.max' => 'La dirección no puede exceder :max caracteres.',

            'fecha_ingreso.date' => 'La fecha de ingreso debe ser una fecha válida.',
            'fecha_ingreso.before_or_equal' => 'La fecha de ingreso no puede ser futura.',

            'salario_base.numeric' => 'El salario base debe ser un número válido.',
            'salario_base.min' => 'El salario base debe ser mayor o igual a 0.',
            'salario_base.max' => 'El salario base no puede exceder :max.',

            'activo.boolean' => 'El estado activo debe ser verdadero o falso.'
        ]);

        try {
            $comercial = $this->createComercialesUseCase->execute($request->all());
            return redirect()->route('planificacion.comerciales.index')
                ->with('success', 'Comercial creado exitosamente.');
        } catch (\Exception $e) {
            return back()->withInput()
                ->withErrors(['error' => 'Error al crear el comercial: ' . $e->getMessage()]);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        try {
            $comercial = $this->getComercialByIdUseCase->execute($id);
            return view('planificacion.comerciales.show', compact('comercial'));
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Error al mostrar el comercial: ' . $e->getMessage()]);
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        try {
            $comercial = $this->getComercialByIdUseCase->execute($id);
            return view('planificacion.comerciales.edit', compact('comercial'));
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Error al editar el comercial: ' . $e->getMessage()]);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $request->validate([
            // Campos obligatorios
            'nombre' => [
                'required',
                'string',
                'min:2',
                'max:100',
                'regex:/^[a-záéíóúñA-ZÁÉÍÓÚÑ\s]+$/' // Solo letras y espacios
            ],
            'apellido' => [
                'required',
                'string',
                'min:2',
                'max:100',
                'regex:/^[a-záéíóúñA-ZÁÉÍÓÚÑ\s]+$/'
            ],
            'email' => [
                'required',
                'email:rfc,dns',
                'max:150',
                'unique:comerciales,email'
            ],

            // Campos opcionales
            'telefono' => [
                'nullable',
                'string',
                'min:10',
                'max:20',
                'regex:/^[\d\s\+\-\(\)]+$/' // Números, espacios, +, -, (), espacios
            ],
            'codigo_empleado' => [
                'nullable',
                'string',
                'max:50',
                'unique:comerciales,codigo_empleado',
                'regex:/^[A-Z0-9\-]+$/' // Letras mayúsculas, números y guiones
            ],
            'departamento' => [
                'nullable',
                'string',
                'in:ventas,marketing,atencion_cliente,desarrollo_negocio'
            ],
            'direccion' => [
                'nullable',
                'string',
                'max:500'
            ],
            'fecha_ingreso' => [
                'nullable',
                'date',
                'before_or_equal:today'
            ],
            'salario_base' => [
                'nullable',
                'numeric',
                'min:0',
                'max:9999999.99'
            ],
            'activo' => [
                'nullable',
                'boolean'
            ]
        ], [
            // Mensajes personalizados
            'nombre.required' => 'El nombre es obligatorio.',
            'nombre.min' => 'El nombre debe tener al menos :min caracteres.',
            'nombre.max' => 'El nombre no puede exceder :max caracteres.',
            'nombre.regex' => 'El nombre solo puede contener letras y espacios.',

            'apellido.required' => 'El apellido es obligatorio.',
            'apellido.min' => 'El apellido debe tener al menos :min caracteres.',
            'apellido.max' => 'El apellido no puede exceder :max caracteres.',
            'apellido.regex' => 'El apellido solo puede contener letras y espacios.',

            'email.required' => 'El email es obligatorio.',
            'email.email' => 'El formato del email no es válido.',
            'email.unique' => 'Este email ya está registrado.',
            'email.max' => 'El email no puede exceder :max caracteres.',

            'telefono.min' => 'El teléfono debe tener al menos :min caracteres.',
            'telefono.max' => 'El teléfono no puede exceder :max caracteres.',
            'telefono.regex' => 'El formato del teléfono no es válido.',

            'codigo_empleado.unique' => 'Este código de empleado ya está en uso.',
            'codigo_empleado.max' => 'El código de empleado no puede exceder :max caracteres.',
            'codigo_empleado.regex' => 'El código debe contener solo letras mayúsculas, números y guiones.',

            'departamento.in' => 'El departamento seleccionado no es válido.',

            'direccion.max' => 'La dirección no puede exceder :max caracteres.',

            'fecha_ingreso.date' => 'La fecha de ingreso debe ser una fecha válida.',
            'fecha_ingreso.before_or_equal' => 'La fecha de ingreso no puede ser futura.',

            'salario_base.numeric' => 'El salario base debe ser un número válido.',
            'salario_base.min' => 'El salario base debe ser mayor o igual a 0.',
            'salario_base.max' => 'El salario base no puede exceder :max.',

            'activo.boolean' => 'El estado activo debe ser verdadero o falso.'
        ]);

        try {
            $comercial = $this->updateComercialesUseCase->execute($id, $request->all());
            return redirect()->route('comerciales.index')
                ->with('success', 'Comercial actualizado exitosamente.');
        } catch (\Exception $e) {
            return back()->withInput()
                ->withErrors(['error' => 'Error al actualizar el comercial: ' . $e->getMessage()]);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $this->deleteComercialesUseCase->execute($id);
            return redirect()->route('comerciales.index')
                ->with('success', 'Comercial eliminado exitosamente.');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Error al eliminar el comercial: ' . $e->getMessage()]);
        }
    }
}
