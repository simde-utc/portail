<?php
/**
 * Fichier générant la commande portail:csv-to-faq.
 * Ajout aux FAQs les questions et catégories du CSV.
 *
 * @author Samy Nastuzzi <samy@nastuzzi.fr>
 *
 * @copyright Copyright (c) 2019, SiMDE-UTC
 * @license GNU GPL-3.0
 */

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\{
    Faq, FaqCategory, Visibility
};

class CsvToFaq extends Command
{
    /**
     * @var string
     */
    protected $signature = 'portail:csv-to-faq {file} {--lang=fr}';

    /**
     * @var string
     */
    protected $description = 'Download all FAQs from a csv file';

    /**
     * Exécution de la commande.
     *
     * @return mixed
     */
    public function handle()
    {
        $file = fopen($this->argument('file'), 'r');
        $lang = $this->option('lang');
        $defaultVisibility = Visibility::findByType('active');

        // On ignore l'index.
        fgetcsv($file);

        while (($columns = fgetcsv($file)) != false) {
            $answer = array_shift($columns);
            $question = array_shift($columns);
            $category = null;

            foreach (array_reverse($columns) as $categoryName) {
                if (!empty($categoryName)) {
                    $category = FaqCategory::firstOrCreate([
                        'name' => $categoryName,
                    ], [
                        'description' => '',
                        'lang' => $lang,
                        'parent_id' => is_null($category) ? null : $category->id,
                        'visibility_id' => $defaultVisibility->id,
                    ]);
                }
            }

            Faq::create([
                'question' => $question,
                'answser' => $answer,
                'category_id' => $category->id,
                'visibility_id' => $defaultVisibility->id,
            ]);
        }
    }
}
