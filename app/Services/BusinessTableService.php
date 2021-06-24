<?php

namespace App\Services;

use App\Repositories\Eloquent\Business\BusinessInterface;
use App\Repositories\Eloquent\Business\TableInterface;
use App\Telegram\TelegramBot;
use App\Traits\SetRepositories;
use App\Utils\FileUploader;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\HtmlString;
use SimpleSoftwareIO\QrCode\Generator;
use Vinkla\Hashids\Facades\Hashids;

/**
 * Class BusinessService
 * @package App\Services
 */
class BusinessTableService extends CoreService
{
    use SetRepositories;

    /**
     * @var array
     */
    private array $repositories = [];

    /**
     * @var string
     */
    private string $directory = 'business/table';

    /**
     * @var string
     */
    private string $key = 'qr_table';

    /**
     * @var FileUploader
     */
    private FileUploader $media;

    /**
     * @var Generator
     */
    protected Generator $qr;

    /**
     * @var TelegramBot
     */
    protected TelegramBot $bot;

    /**
     * UserService constructor.
     *
     * @param TableInterface $table
     * @param BusinessInterface $business
     * @param FileUploader $media
     * @param TelegramBot $telegramBot
     * @param Generator $qr
     */
    public function __construct(
        TableInterface $table,
        BusinessInterface $business,
        FileUploader $media,
        TelegramBot $telegramBot,
        Generator $qr
    ) {
        $this->bot = $telegramBot;
        $this->media = $media;
        $this->qr = $qr;
        $this->setRepositories(func_get_args());
    }

    /**
     * @return TableInterface
     */
    public function getRepo(): TableInterface
    {
        return $this->repositories['TableRepository'];
    }

    /**
     * @return BusinessInterface
     */
    public function business(): BusinessInterface
    {
        return $this->repositories['BusinessRepository'];
    }

    /**
     * @return TelegramBot
     */
    public function bot(): TelegramBot
    {
        return $this->bot;
    }

    /**
     * @param array $data
     * @return Builder|Model
     */
    public function createTable(array $data)
    {
        $table = $this->getRepo()->create($data);

        $qrcode = $this->qrGenerator("tables/" . Hashids::encode($table->id));

        $path = $this->media->qrCodeUpload(
            $qrcode, $this->directory, $this->key
        );

        $table->qrcode()->create(['url' => $path]);

        return $table;
    }

    /**
     * TODO make event and observer
     * @param $table
     * @param false $forceDelete
     */
    public function deleteTable($table, bool $forceDelete = false): void
    {
        if ($table instanceof Collection) {
            $table->map(function ($item) use ($forceDelete) {
                if ($forceDelete) {
                    $this->media->deleteFile($item->qrcode->url);
                }
            });
        }

        if ($table instanceof Model) {
            if ($forceDelete) {
                $this->media->deleteFile($table->qrcode->url);
            }
        }

        $this->getRepo()->deleteModel($table, $forceDelete);
    }

    /**
     * @param string $path
     * @return mixed
     */
    public function qrGenerator(string $path)
    {
        return $this->qr->size('180')->errorCorrection('H')->generate(asset($path));
    }
}
