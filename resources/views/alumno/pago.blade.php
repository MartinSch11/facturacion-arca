@extends('layouts.app')

@section('content')
<div class="container">

    <form action="{{ route('procesar.pago.boleta', $boleta) }}" method="POST">
        @csrf
        <div class="mb-3">
            <label for="forma_pago" class="form-label">Forma de Pago</label>
            <select name="forma_pago" id="forma_pago" class="form-control" required>
                <option value="">Seleccione</option>
                <option value="contado">Contado</option>
                <option value="tarjeta_credito">Tarjeta de Crédito</option>
                <option value="tarjeta_debito">Tarjeta de Débito</option>
            </select>
        </div>

        <button type="submit" class="btn btn-primary">Confirmar y Emitir Factura</button>
    </form>
</div>
@endsection
