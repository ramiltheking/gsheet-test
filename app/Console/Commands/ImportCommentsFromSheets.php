<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\GoogleSheetService;

class ImportCommentsFromSheets extends Command
{
    protected $signature = 'import:comments {count?} ';
    protected $description = 'Импорт комментариев из Google Sheets в консоль';

    public function __construct(private GoogleSheetService $googleSheetService)
    {
        parent::__construct();
    }

    public function handle()
    {
        $this->info("Получение данных из Google Sheets...");

        $sheetData = $this->googleSheetService->getSheetData('Лист1!A:G');

        array_shift($sheetData);

        $count = (int) $this->argument('count') ?: count($sheetData);
        $sheetData = array_slice($sheetData, 0, $count);

        if (empty($sheetData)) {
            $this->warn("Нет данных для вывода.");
            return;
        }

        // Создаём progress bar
        $bar = $this->output->createProgressBar(count($sheetData));
        $bar->start();

        foreach ($sheetData as $row) {
            $id = $row[0] ?? 'N/A';
            $comment = $row[6] ?? 'Отсутствует комментарий';
            $bar->advance();
            usleep(50000); 
            $this->line("\nID: $id | Комментарий: $comment");
            $bar->advance();
        }

        $bar->finish();
        $this->newLine();
        $this->info("Импорт успешно завершён.");
    }
}
