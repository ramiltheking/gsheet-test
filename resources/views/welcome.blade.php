<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Данные</title>
    <meta name="csrf_token" content="{{ csrf_token() }}" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
</head>
<body class="bg-dark">
    <div class="container-fluid py-4">
        <div class="d-flex my-2">
            <button class="btn btn-primary" id="generate">
                Создать 1000 записей
            </button>
            <button class="btn btn-danger mx-3" id="truncate">
                Очистить таблицу
            </button>
            <input type="text" class="form-control" id="gsheet_url" placeholder="Введите URL" value="{{ env('GOOGLE_SHEET_URL') }}">
        </div>
        @if(count($products) > 0)
            <table class="table table-bordered table-dark">
                <thead>
                    <tr>
                    <th scope="col">#</th>
                    <th scope="col">Alias</th>
                    <th scope="col">Info</th>
                    <th scope="col">Status</th>
                    <th scope="col">Created at</th>
                    <th scope="col">Updated at</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($products as $product)
                        <tr>
                            <td>{{ $product->id }}</td>
                            <td>{{ $product->alias }}</td>
                            <td>{{ $product->info }}</td>
                            <td>{{ $product->status }}</td>
                            <td>{{ $product->created_at }}</td>
                            <td>{{ $product->updated_at }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            {{ $products->links() }}
        @else
            <div class="alert alert-dark">
                <h5>Список пуст</h5>
            </div>
        @endif
    </div>
    <div class="loader">
        <div class="spinner-grow text-light" role="status">
            <span class="visually-hidden">Loading...</span>
        </div>
    </div>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js" ></script>
    <script src="{{ asset('js/main.js') }}"></script>
</body>
</html>