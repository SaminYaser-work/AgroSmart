<?php

namespace App\Filament\Resources\CropProjectResource\Pages;

use App\Filament\Resources\CropProjectResource;
use App\Models\CropProject;
use App\Models\Farm;
use App\Models\Field;
use Closure;
use Filament\Forms\Components\Card;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Select;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Resources\Pages\Page;
use Illuminate\Support\HtmlString;

class DiseaseDetection extends Page implements HasForms
{
    use InteractsWithForms;

    protected static string $resource = CropProjectResource::class;

    protected static string $view = 'filament.resources.crop-project-resource.pages.disease-detection';

    public array $image;
    public array $res;
    public $farm;
    public $field;
    public $dd;
    public bool $hasError = false;

    public function mount(): void
    {
        $this->image = [];
        $this->res = [];
    }

    public function submit(): void
    {
        if (!is_array($this->image)) {
            return;
        }
        foreach ($this->image as $key=>$image) {
            $file_path = $image->getRealPath();
            $response = \Http::attach(
                'file', file_get_contents($file_path), $image->getFilename()
            )->post(env('AI_API') . '/dd');
            $res = $response->json();
            foreach ($res as $r) {
                if(!array_key_exists('confidence', $r)){
                    $this->hasError = true;
                    return;
                }
                if ($r['confidence'] > 0) {
                    $this->res[] = $r;
                }
            }
            break;
        }
    }

    public function reloadPage() {
        return redirect(request()->header('Referer'));
    }

    protected function getFormSchema(): array
    {
        return [
            Card::make()
                ->schema([
                    Grid::make(1)
                        ->schema([
                            Select::make('farm')
                                ->options(function () {
                                    return Farm::all()->pluck('name', 'id');
                                })
                                ->reactive()
                                ->label('Farm')
                                ->required(),
                            Select::make('field')
                                ->reactive()
                                ->options(function (Closure $get) {
                                    return Field::query()
                                        ->where('farm_id', $get('farm'))
                                        ->where('status', '=', false)
                                        ->orderBy('name')
                                        ->pluck('name', 'id');
                                })
                                ->required()
                                ->label('Field'),
                            Placeholder::make('info')
                                ->label('Project Details')
                                ->content(
                                    function (Closure $get) {
                                        $field = $get('field');
                                        if ($field === null) {
                                            return new HtmlString(
                                                '<p class="text-sm text-gray-500">None</p>'
                                            );
                                        }
                                        $data = CropProject::with('field')
                                            ->where('field_id', $field)
                                            ->first();
                                        return new HtmlString("
                                            <table>
                                                <tr>
                                                    <th>
                                                        Crop
                                                    </th>
                                                    <td>
                                                        : {$data->crop_name}
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <th>
                                                        Status
                                                    </th>
                                                    <td>
                                                        : {$data->status}
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <th>
                                                        Field Area
                                                    </th>
                                                    <td>
                                                        : {$data->field->area} ha
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <th>
                                                        Soil Type
                                                    </th>
                                                    <td>
                                                        : {$data->field->soil_type}
                                                    </td>
                                                </tr>
                                            </table>
                                        ");
                                    }
                                )
                        ])
                        ->columnSpan(8),
                    Grid::make(1)
                        ->schema([
                            FileUpload::make('image')
                                ->image()
                                ->label('Upload Image')
                                ->disk('storage')
                                ->directory('dd')
                                ->imagePreviewHeight('250')
                                ->loadingIndicatorPosition('left')
                                ->panelAspectRatio('1.5:1')
                                ->panelLayout('integrated')
                                ->removeUploadedFileButtonPosition('right')
                                ->uploadButtonPosition('left')
                                ->uploadProgressIndicatorPosition('left')
                                ->required(),
                        ])
                        ->columnSpan(4)
                ])
                ->columns(12),
        ];
    }
}
