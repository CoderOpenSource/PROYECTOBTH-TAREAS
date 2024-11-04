@foreach($comportamientos as $comportamiento)
    <tr>
        <td>{{ $comportamiento->descripcion }}</td>
        <td>{{ $comportamiento->fecha }}</td>
        <td>{{ $comportamiento->tipo }}</td>
        <td>{{ $comportamiento->estudiante->nombre }}</td>
        <td>{{ $comportamiento->profesor }}</td>
        <td>
            <button class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#editModal-{{ $comportamiento->estudiante_id }}">Editar</button>

            <form action="{{ route('comportamientos.destroy', [$acta->id, $comportamiento->estudiante_id]) }}" method="POST" style="display:inline;">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-danger btn-sm">Eliminar</button>
            </form>
        </td>
    </tr>
@endforeach
