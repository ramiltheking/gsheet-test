<?php

namespace App\Services;

use Google_Client;
use Google_Service_Sheets;
use Google_Service_Sheets_ValueRange;
use Google_Service_Sheets_BatchUpdateSpreadsheetRequest;
use Google_Service_Sheets_Request;

class GoogleSheetService
{
    protected $client;
    protected $service;
    protected $spreadsheetId; 

    public function __construct()
    {
        $sheetUrl = env('GOOGLE_SHEET_URL'); 
        $this->spreadsheetId = $this->extractSheetId($sheetUrl);
        \Log::info("Spreadsheet ID: " . $this->spreadsheetId);

        putenv('GOOGLE_APPLICATION_CREDENTIALS=' . base_path('credentials.json'));

        $this->client = new Google_Client();
        $this->client->setApplicationName('Laravel Google Sheets');
        $this->client->setScopes([Google_Service_Sheets::SPREADSHEETS]);
        $this->client->setAuthConfig(base_path('credentials.json'));
        $this->client->useApplicationDefaultCredentials();
        $this->client->setAccessType('offline');
        
        $this->service = new Google_Service_Sheets($this->client);

    }

    public function getSheetData($range)
    {
        $response = $this->service->spreadsheets_values->get($this->spreadsheetId, $range);
        return $response->getValues() ?? [];
    }

    public function deleteRows($sheetName, $indexes)
    {
        rsort($indexes); // Удаляем с конца, чтобы индексы не смещались

        $requests = [];
        foreach ($indexes as $index) {
            $requests[] = new Google_Service_Sheets_Request([
                'deleteDimension' => [
                    'range' => [
                        'sheetId' => 0, // ID листа (обычно 0)
                        'dimension' => 'ROWS',
                        'startIndex' => $index - 1, // API использует 0-индексацию
                        'endIndex' => $index+1,
                    ],
                ],
            ]);
        }

        $batchUpdateRequest = new Google_Service_Sheets_BatchUpdateSpreadsheetRequest([
            'requests' => $requests,
        ]);

        $this->service->spreadsheets->batchUpdate($this->spreadsheetId, $batchUpdateRequest);
    }

    public function updateRows($updates)
    {
        foreach ($updates as $update) {
            $body = new Google_Service_Sheets_ValueRange([
                'values' => [$update['values']],
            ]);

            $params = ['valueInputOption' => 'RAW'];
            $this->service->spreadsheets_values->update($this->spreadsheetId, $update['range'], $body, $params);
        }
    }

    public function addRows($range, $rows)
    {
        $body = new Google_Service_Sheets_ValueRange([
            'values' => $rows,
        ]);

        $params = ['valueInputOption' => 'RAW', 'insertDataOption' => 'INSERT_ROWS'];

        $this->service->spreadsheets_values->append($this->spreadsheetId, $range, $body, $params);
    }
    
    /**
     * Извлекает ID из ссылки Google Sheets
     */
    private function extractSheetId($url)
    {
        preg_match('/\/d\/([a-zA-Z0-9-_]+)/', $url, $matches);
        return $matches[1] ?? null;
    }
    

    public function updateSheet($range, $values)
    {
        $body = new Google_Service_Sheets_ValueRange([
            'values' => $values,
        ]);

        $params = ['valueInputOption' => 'RAW'];

        return $this->service->spreadsheets_values->update(
            $this->spreadsheetId,
            $range,
            $body,
            $params
        );
    }
}
