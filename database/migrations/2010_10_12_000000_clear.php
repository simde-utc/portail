<?php
use Illuminate\Database\Migrations\Migration;

class Clear extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $this->removeDir(public_path('/images/assos'));
        $this->removeDir(public_path('/images/articles'));
        $this->removeDir(public_path('/images/events'));
    }

    /**
     * Supprime un dossier et tout son contenu.
     *
     * @param  string $path
     * @return void
     */
    protected function removeDir(string $path): void
    {
        if (file_exists($path)) {
            foreach (array_diff(scandir($path), ['..', '.']) as $file) {
                $subPath = $path.DIRECTORY_SEPARATOR.$file;
                if (is_dir($subPath)) {
                    $this->removeDir($subPath);
                } else {
                    unlink($subPath);
                }
            }

            rmdir($path);
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
    }
}
