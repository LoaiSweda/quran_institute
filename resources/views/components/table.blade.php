<div class="table-responsive">
    <table>
        <thead>
        <tr>
            @foreach($columns as $col)
                <th>{{ $col }}</th>
            @endforeach
        </tr>
        </thead>
        <tbody>
        {{ $body ?? $slot }}
        </tbody>
    </table>
</div>
