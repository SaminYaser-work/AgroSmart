<x-filament::page>
    <style>
        th, td {
            text-align: left;
            padding-right: 10px;
        }
    </style>

    <form wire:submit.prevent="submit">
        {{ $this->form }}

        <button type="submit"
                class="mt-4 filament-button filament-button-size-md inline-flex items-center justify-center py-1 gap-1 font-medium rounded-lg border transition-colors outline-none focus:ring-offset-2 focus:ring-2 focus:ring-inset min-h-[2.25rem] px-4 text-sm text-white shadow focus:ring-white border-transparent bg-primary-600 hover:bg-primary-500 focus:bg-primary-700 focus:ring-offset-primary-700 filament-page-button-action">
            Analyze
        </button>
        <button type="button"
                wire:click="reloadPage()"
                class="mt-4 filament-button filament-button-size-md inline-flex items-center justify-center py-1 gap-1 font-medium rounded-lg border transition-colors outline-none focus:ring-offset-2 focus:ring-2 focus:ring-inset min-h-[2.25rem] px-4 text-sm text-white shadow focus:ring-white border-transparent bg-secondary-600 hover:bg-secondary-500 focus:bg-secondary-700 focus:ring-offset-secondary-700 filament-page-button-action">
            Reset
        </button>
    </form>

    {{--    <div wire:loading wire:target="submit">--}}
    {{--        Analyzing...--}}
    {{--    </div>--}}

    @if($hasError)
        <p class="text-danger-500">
            An error occurred while analyzing the image. Please try again.
        </p>
    @endif

    @if(!empty($res) && $res != "" && !$hasError)
        <div class="filament-forms-card-component rounded-xl border border-gray-300 bg-white p-6">
            <h3 class="text-2xl mb-3 font-bold">
                Crop Doctor <span
                    style="font-weight: bolder; background: linear-gradient(to right, blue, violet); -webkit-background-clip: text; color: transparent;">(AI)</span>
            </h3>
            <div class="flex justify-start align-start">
                <table style="min-width: 500px; height: fit-content; margin-top: 10px;">
                    @foreach($res as $r)
                        <tr>
                            <th>{{ $r['name'] }}</th>
                            <td>
                                <div class="flex items-center space-x-4 px-4">
                                    <div class="bg-gray-200 rounded-full h-2.5 dark:bg-gray-600" style="width: 100px;">
                                        <div class="h-2.5 rounded-full bg-success-500"
                                             style="width: {{ round($r['confidence']) }}px"></div>
                                    </div>
                                    <span
                                        class="text-sm text-gray-700 dark:text-gray-200">{{ $r['confidence'] }}%</span>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </table>
                <div>
                    <h5 class="text-xl">{{ $res[0]['name'] }}</h5>
                    <p>
                        {{ $res[0]['description'] }}
                    </p>
                </div>
            </div>
        </div>
    @endif
</x-filament::page>
