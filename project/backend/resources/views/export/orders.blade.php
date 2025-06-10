<table class="table table-sm">
    <thead>
        <tr>
            <th scope="col">#id</th>
            <th scope="col">Тип номера</th>
            <th scope="col">Статус</th>
            <th scope="col">Занятый номер</th>
            <th scope="col">Дата заезда</th>
            <th scope="col">Длительность</th>
            <th scope="col">Общее количесво: {{ $orders->count() }}</th>
            <th scope="col">Стоимость всех заказов: {{ $full_price }}р.</th>
        </tr>

    </thead>
    <tbody class="table-group-divider">
        @foreach ($orders as $order)
            <tr>
                <th scope="row"> {{ $order->id }}</th>
                <td>{{ $order->checkType()->typeRoom }}</td>
                <td>{{ $order->status }}</td>
                <td>
                    @if ($room = $order->checkRoom())
                        {{ $room->number }}
                    @endif
                </td>
                <td>{{ $order->onDate }}</td>
                <td>{{ $order->duration }}</td>
            </tr>
        @endforeach
    </tbody>
</table>
