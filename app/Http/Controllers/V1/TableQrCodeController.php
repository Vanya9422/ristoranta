<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Response;
use Laravel\Lumen\Http\ResponseFactory;
use SimpleSoftwareIO\QrCode\Generator;

/**
 * Class TableQrCodeController
 * @property Generator generator
 * @package App\Http\Controllers\V1
 */
class TableQrCodeController extends Controller
{

    /**
     * @var Generator
     */
    protected Generator $generator;


    /**
     * TableQrCodeController constructor.
     * @param Generator $generator
     */
    public function __construct(Generator $generator)
    {
        $this->generator = $generator;
    }

    /**
     * @return Response|ResponseFactory
     */
    public function qrGenerator()
    {
        $qr = $this->generator
            ->format('svg')
            ->eye('circle')
            ->style('round')
            ->size(200)
            ->errorCorrection('H')
            ->generate(asset('tables/15'));

        return response($qr);
    }
}
