<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Product;
use App\Services\GoogleSheetService;

class ExportProductsToSheets extends Command
{
    protected $signature = 'export:products';
    protected $description = 'Синхронизация данных из БД в Google Sheets (обновление, добавление, удаление записей)';

    public function __construct(private GoogleSheetService $googleSheetService)
    {
        parent::__construct();
    }

    public function handle()
    {
        $products = Product::select('id', 'alias', 'info', 'status', 'created_at', 'updated_at')
            ->get()
            ->keyBy('id')
            ->toArray();

        // 1. Получаем текущие данные из Google Sheets
        $sheetData = $this->googleSheetService->getSheetData('Лист1!A:G');

        // 2. Парсим данные из Google Sheets в массив с индексами
        $sheetRows = [];
        foreach ($sheetData as $index => $row) {
            if (!empty($row[0]) && is_numeric($row[0])) {
                $sheetRows[$row[0]] = [
                    'index' => $index + 1, // Строки начинаются с 1
                    'id' => $row[0],
                    'alias' => $row[1] ?? '',
                    'info' => $row[2] ?? '',
                    'status' => $row[3] ?? '',
                    'created_at' => $row[4] ?? '',
                    'updated_at' => $row[5] ?? '',
                    'comment' => $row[6] ?? '',
                ];
            }
        }

        $toAdd = [];
        $toUpdate = [];
        $toDelete = [];

        // 3. Определяем, какие записи обновлять, добавлять, удалять
        foreach ($products as $id => $product) {
            if (!isset($sheetRows[$id]) && $product['status'] === 'Allowed') {
                // Добавить новую запись
                $toAdd[] = [$product['id'], $product['alias'], $product['info'], $product['status'], $product['created_at'], $product['updated_at'], ''];
            } elseif (isset($sheetRows[$id])) {
                // Проверяем, нужно ли обновлять данные
                $row = $sheetRows[$id];
                if (
                    $product['alias'] !== $row['alias'] ||
                    $product['info'] !== $row['info'] ||
                    $product['status'] !== $row['status'] ||
                    $product['created_at'] !== $row['created_at'] ||
                    $product['updated_at'] !== $row['updated_at']
                ) {
                    // Обновляем данные, оставляя комментарий
                    $toUpdate[] = [
                        'range' => "Лист1!A{$row['index']}:G{$row['index']}",
                        'values' => [
                            $product['id'], $product['alias'], $product['info'], $product['status'], $product['created_at'], $product['updated_at'], $row['comment']
                        ],
                    ];
                }

                // Если статус стал `Prohibited`, добавляем в список на удаление
                if ($product['status'] === 'Prohibited') {
                    $toDelete[] = $row['index'];
                }
            }
        }

        // 4. Удаляем записи, которых больше нет в БД
        foreach ($sheetRows as $id => $row) {
            if (!isset($products[$id])) {
                $toDelete[] = $row['index'];
            }
        }

        // 5. Удаляем ненужные записи
        if (!empty($toDelete)) {
            $this->googleSheetService->deleteRows('Лист1', array_unique($toDelete));
        }

        // 6. Обновляем изменённые строки
        if (!empty($toUpdate)) {
            $this->googleSheetService->updateRows($toUpdate);
        }

        // 7. Добавляем новые записи
        if (!empty($toAdd)) {
            $this->googleSheetService->addRows('Лист1!A1', $toAdd);
        }

        $this->info('Синхронизация с Google Sheets завершена.');
    }
}
