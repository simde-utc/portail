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
        $this->removeDir(public_path('/images/assos/'), true);
        $this->removeDir(public_path('/images/articles'));
        $this->removeDir(public_path('/images/events'));
    }

    /**
     * Delete a folder and all its content.
     *
     * @param string  $path
     * @param boolean $contentOnly
     * @return void
     */
    protected function removeDir(string $path, bool $contentOnly=false): void
    {
        if (file_exists($path)) {
            foreach (array_diff(scandir($path), ['..', '.', '.gitignore']) as $file) {
                $subPath = $path.DIRECTORY_SEPARATOR.$file;
                if (is_dir($subPath)) {
                    $this->removeDir($subPath, $contentOnly);
                } else {
                    unlink($subPath);
                }
            }

            if (!$contentOnly) {
                rmdir($path);
            }
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
