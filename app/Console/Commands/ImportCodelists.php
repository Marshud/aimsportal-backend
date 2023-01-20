<?php

namespace App\Console\Commands;

use App\Models\Codelist;
use Illuminate\Console\Command;
use function Termwind\{render};
use Throwable;

class ImportCodelists extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'iati:import-codelists';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import Codelists from github xml';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $lists = config('iati-codelists.203');
        $context  = stream_context_create(array('http' => array('header' => 'Accept: application/xml')));
        $total_lists = count($lists);
        $imported = 0;
        $translations = ['en'];
        foreach($lists as $list) {

            try {

                $data = file_get_contents($list['url'], false, $context);
                $xml = simplexml_load_string($data);
                $metadata = $xml->metadata;
                $codelists = $xml->{'codelist-items'};
                $long_name = $metadata->name->narrative;
                $description = $metadata->description->narrative;
                $codelist = Codelist::firstOrCreate(['name'=>$list['name']],[ 'description'=>$description]);

                foreach($codelists->{'codelist-item'} as $codelist_option) 
                {
                    $name_narratives = $codelist_option->name->narrative;
                    $description_narratives = $codelist_option->description->narrative;
                    $url = $codelist_option->url;
                    $related_codelist = null;

                    if ($url) {
                        $sort_url = (string)$url;
                        if (str_contains($sort_url, 'codelists')) {
                            $related_codelist = array_slice(explode('/', rtrim($sort_url, '/')), -1)[0];
                            
                        }
                        
                    }
                  
                    $option = $codelist->options()->firstOrCreate(['code' => $codelist_option->code],['name' => $name_narratives[0], 'description' => ($description_narratives) ? $description_narratives[0] : null, 'related_codelist'=>$related_codelist]);
                    
                    foreach($name_narratives as $index => $narrative)
                    {
                        if ($narrative->attributes('xml', true)->lang) {

                            $lang = $narrative->attributes('xml', true)->lang;
                            $name = $narrative;
                            $description = ($description_narratives) ? $description_narratives[$index] : null;

                            $option->translations()->firstOrCreate(['lang'=>$lang, 'name'=>$name],['description'=>$description]);

                            if(!in_array((string)$lang, $translations))  array_push($translations, (string)$lang);
                            
                        }
                    }
                }
                $imported++;

            }catch(Throwable $e) {
                $message = $list['name']." -> ".$e->getMessage();
                $this->error($message);
            }
        }

        render(view('terminal.iati-import', ['total_lists' => $total_lists, 'imported' => $imported, 'translations' => $translations])->render());


        return Command::SUCCESS;
    }
}
