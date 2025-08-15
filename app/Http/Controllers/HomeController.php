<?php

namespace App\Http\Controllers;

use App\Services\HomeService;
use Inertia\Inertia;
use Inertia\Response;

class HomeController extends Controller
{
    public function __construct(private HomeService $service) {}

    public function index(): Response
    {
        $component = $this->service->getComponent();

        $currentHour = (object) $this->service->getCurrentHour();

        $data = Inertia::optional(fn () => $this->service->getUserHomeData());

        return Inertia::render($component, [
            'dayPart'      => $currentHour->dayPart,
            'illustration' => $currentHour->illustration,
            'data'         => $data,
        ]);
    }
}
