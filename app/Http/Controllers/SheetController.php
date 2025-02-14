<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Symfony\Component\Console\Output\BufferedOutput;

class SheetController extends Controller
{
    /**
     * Меняет URL
     * 
     * @var Request $request
     * 
     * @return response()
     */
    public function changeURL(Request $request)
    {
        if($request->has("url")){
            $path = base_path('.env'); 
            $key = 'GOOGLE_SHEET_URL'; 
            $newValue = $request->url;
            $content = file_get_contents($path);
            $pattern = "/^{$key}=.*/m"; 
            $replacement = "{$key}={$newValue}"; 
            if (preg_match($pattern, $content)) {
                $content = preg_replace($pattern, $replacement, $content);
            } else {
                $content .= "\n{$replacement}";
            }
            file_put_contents($path, $content);
            \Illuminate\Support\Facades\Artisan::call('config:clear');
        }
        return response()->json([
            "success" => true
        ]);
            
    }

    /**
     * Пушит данные в Google Sheet
     * 
     * @return response()
     */
    public function pullData()
    {
        $link = env("GOOGLE_SHEET_URL");
    }

    public function sandbox()
    {
        dd(require base_path('credentials.json'));
    }

    public function displaySheetData($count = null)
    {
        $output = new BufferedOutput(); 

        Artisan::call("import:comments ".($count ?? ''), [], $output);

        echo str_replace("\n", "<br>", $output->fetch());
    }
}
