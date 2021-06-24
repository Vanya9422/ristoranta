<?php

namespace App\Services;

use App\Repositories\Eloquent\Business\BusinessInterface;
use App\Repositories\Eloquent\Business\CategoryInterface;
use App\Repositories\Eloquent\Business\DishInterface;
use App\Traits\SetRepositories;
use App\Utils\FileUploader;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class BusinessService
 * @package App\Services
 */
class BusinessMenuService extends CoreService
{
    use SetRepositories;

    /**
     * @var array
     */
    private array $repositories = [];

    /**
     * @var string
     */
    private string $directory = 'business/menu';

    /**
     * @var string
     */
    private string $key = 'dish';

    /**
     * @var FileUploader
     */
    private FileUploader $media;

    /**
     * UserService constructor.
     *
     * @param BusinessInterface $business
     * @param DishInterface $menu
     * @param FileUploader $media
     */
    public function __construct(
        BusinessInterface $business,
        DishInterface $menu,
        FileUploader $media
    ) {
        $this->media = $media;
        $this->setRepositories(func_get_args());
    }

    /**
     * @return DishInterface
     */
    public function getRepo(): DishInterface
    {
        return $this->repositories['DishRepository'];
    }

    /**
     * @return CategoryInterface
     */
    public function category(): CategoryInterface
    {
        return $this->repositories['CategoryRepository'];
    }

    /**
     * @return CategoryInterface
     */
    public function tag(): CategoryInterface
    {
        return $this->repositories['TagRepository'];
    }

    /**
     * TODO further clarify the logic of this section
     * @param $request
     * @return Builder|Model
     */
    public function createDish($request)
    {
        $dish = $this->getRepo()->create($request->except('file'));

        $path = $this->media->fileUpload(
            $request->file('file'), $this->directory, $this->key
        );

        $dish->image()->create(['url' => $path]);

        if ($request->has('selected')) {
            $dish->tags()->attach($request->get('selected'));
        }

        return $dish;
    }

    /**
     * @param $request
     * @return bool|int
     */
    public function updateDish($request)
    {
        $dish = $this->getRepo()->find($request->get('id'));

        if ($request->file('file') && $request->file('file')->isValid()) {
            $path = $this->media->fileUpload(
                $request->file('file'), $this->directory, $this->key, $dish->image->url
            );

            $dish->image()->where('url', $dish->image->url)->update(['url' => $path]);
        }

        if ($request->has('selected')) {
            $dish->tags()->sync($request->get('selected'));
        }

        return $dish->update($request->except('file'));
    }

    /**
     * @param $dishID
     * @param false $forceDelete
     */
    public function deleteDish($dishID, bool $forceDelete = false): void
    {
        $dish = $this->getRepo()->find($dishID);

        if ($forceDelete) $this->media->deleteFile($dish->image->url);

        $this->getRepo()->deleteModel($dish, $forceDelete);
    }

    /**
     * @param $data
     * @return mixed
     */
    public function blockDish($data)
    {
        $dish = $this->getRepo()->find($data['dish_id']);

        if ($data['action'] === 'block') {
            $dish->block()->create(['business_id' => $data['business_id']]);
        } else {
            $dish->block()->where([
                'business_id' => $data['business_id'],
                'dish_id' => $data['dish_id'],
            ])->delete();
        }

        return $dish;
    }
}
