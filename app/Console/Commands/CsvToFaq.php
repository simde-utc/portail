<?php
/**
 * File generating the command: portail:csv-to-faq.
 * Add CSV's categories and questions to FAQ.
 *
 * @author Samy Nastuzzi <samy@nastuzzi.fr>
 * @author R01 <contact@r01.li>
 *
 * @copyright Copyright (c) 2019, SiMDE-UTC
 * @copyright Copyright (c) 2022 all contributors of this file as listed by the git log
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
     * Command execution.
     *
     * @return mixed
     */
    public function handle()
    {
        $file = fopen($this->argument('file'), 'r');
        $lang = $this->option('lang');
        $defaultVisibility = Visibility::findByType('active');

        // Ignoring index.
        fgetcsv($file);

        while (($columns = fgetcsv($file, null, ';')) != false) {
            $answer = array_shift($columns);
            $question = array_shift($columns);
            $category = null;

            foreach ($columns as $categoryName) {
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
                'answer' => $answer,
                'category_id' => $category->id,
                'visibility_id' => $defaultVisibility->id,
            ]);
	}

	return 0; #laravel 7
    }
}
