@extends('loyauts.formsite')
@section('title', 'Главная')
@section('content')
    <div class="container-fluid border" style="background-color:  #ffcc99; height: 350px">
        <h2 class="text-center"
            style="padding-top: 135px; font-style:italic; font-family:Brush Script MT; font-weight:bold; font-size:50px">
            Добро пожаловать в
            нашу гостиницу!</h2>
    </div>
    <div class="container-fluid pt-5 pb-5" style="background-color: #ffe6cc;">
        <!-- Content Area -->
        <form action="https://yoomoney.ru/quickpay/confirm" method="post">
            <input type="hidden" name="reciever" value="4100118951994986">
            <input type="hidden" name="quickpay-form" value="button">
            <input type="hidden" name="targets" value="Тест">
            <input type="hidden" name="paymentType" value="AC">
            <input type="hidden" name="sum" value="2">
            <button type="submit">Оплатить</button>
        </form>
        <!-- Options Section -->
        <div class="row">
            <h1 class="text-center mb-5">Наши номера</h1>
            @foreach ($typesRoom as $typeroom)
                <div class="col-md-4">
                    <div class="card">
                        <h3 class="card-header text-center">"{{ $typeroom->typeRoom }}"</h3>
                        <div class="card-body text-center">
                            <img src="{{ asset('images/' . $typeroom->image) }}" alt="" width="250"
                                height="300">
                            <p class="card-text">Описание: {{ $typeroom->description }}</p>
                            <p class="card-text">Цена: {{ $typeroom->price }}р.</p>
                            @auth
                                @if (Auth::user()->isUser())
                                    @if (Auth::user()->currentRoom() == null)
                                        <a href="{{ route('order.create') }}" class="btn btn-success" disabled>Перейти к
                                            бронированию</a>
                                    @else
                                        <a href="{{ route('order.create') }}" class="btn btn-success">Перейти к
                                            бронированию</a>
                                    @endif
                                @endif
                            @endauth

                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>




    </div>
    <script>
        document.getElementById('formId').addEventListener('submit', function(event) {
            event.preventDefault(); // Прерываем стандартную отправку формы.

            var formData = new FormData(this); // Собираем данные формы

            // Отправляем запрос с использованием fetch
            fetch('https://yoomoney.ru/api/request-payment', {
                    method: 'POST', // Устанавливаем метод
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                        'authorization': "Bearer 4100118951994986.63015AB5655EC7F9E19CF00F1B4C9AFABE506B6E4706618F1CE9B17FB3BB117DB5119BFD22000DFD7476FA706CCE47E0B7BAE106C74A24A40D5C53D5381B3CB94582ABAA83A4F7CE70F8976698065721511BE24C5920AC96BB905518FF1372B55A8F66BC71D3AA4DC2BCEB890E28717F56847E4AF17E2D77437CD3EEE03CA7F0" // Указываем заголовок
                    },

                    body: new URLSearchParams(formData),
                    referrer: ""
                })
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Network response was not ok');
                    }
                    return response.json(); // Парсим ответ в формате JSON
                })
                .then(data => {
                    console.log('Успех:', data); // Обрабатываем данные
                })
                .catch(error => {
                    console.error('Ошибка:', error); // Обрабатываем ошибку
                });
        });
    </script>
@endsection('content')
