@props(['chartId', 'chartOptions', 'contentHeight', 'pollingInterval', 'loadingIndicator', 'deferLoading', 'readyToLoad', 'darkMode'])

<div {!! $deferLoading ? ' wire:init="loadWidget" ' : '' !!} class="flex items-center justify-center filament-apex-charts-chart"
    style="{{ $contentHeight ? 'height: ' . $contentHeight . 'px;' : '' }}">
    @if ($readyToLoad)
        <div wire:ignore class="w-full filament-apex-charts-chart-container">

            <div class="filament-apex-charts-chart-object" x-ref="{{ $chartId }}" id="{{ $chartId }}">
            </div>

            <div {!! $pollingInterval ? 'wire:poll.' . $pollingInterval . '="updateOptions"' : '' !!} x-data="{
                chart: null,
                options: @js($chartOptions),
                theme: {{ $darkMode ? "document.querySelector('html').matches('.dark') ? 'dark' : 'light'" : "'light'" }},
                init() {

                    $wire.on('updateOptions', async ({ options }) => {
                        this.chart.updateOptions(options, false, true, true);
                    });

                    options = this.options


                    if (options.dataLabels && options.dataLabels.formatter) {
                        options.dataLabels.formatter = eval(options.dataLabels.formatter)
                    }

                    if (options.tooltip && options.tooltip.custom) {
                        options.tooltip.custom = eval(options.tooltip.custom)
                    }

                    if (options.plotOptions?.pie?.donut?.labels.value && options.plotOptions?.pie?.donut?.labels?.value.formatter) {
                        options.plotOptions.pie.donut.labels.value.formatter = eval(options.plotOptions.pie.donut.labels.value.formatter)
                    }

                    if (options.plotOptions?.pie?.donut?.labels.total && options.plotOptions?.pie?.donut?.labels?.total.formatter) {
                        options.plotOptions.pie.donut.labels.total.formatter = eval(options.plotOptions.pie.donut.labels.total.formatter)
                    }

                    if (options.yaxis && options.yaxis.labels && options.yaxis.labels.formatter) {
                        options.yaxis.labels.formatter = eval(options.yaxis.labels.formatter)
                    }


                    if (options.xaxis && options.xaxis.labels && options.xaxis.labels.formatter) {
                        options.xaxis.labels.formatter = eval(options.xaxis.labels.formatter)
                    }

                    this.options.theme = { mode: this.theme };
                    this.options.chart.background = 'inherit';

                    this.chart = new ApexCharts($refs.{{ $chartId }}, this.options);
                    this.chart.render();
                }
            }"
                @dark-mode-toggled.window="chart.updateOptions( { theme: { mode: {{ $darkMode ? '$event.detail' : "'light'" }} } } )"
                x-init="$watch('dropdownOpen', value => $wire.dropdownOpen = value)">
            </div>

        </div>
    @else
        <div class="filament-apex-charts-chart-loading-indicator m-auto">
            @if ($loadingIndicator)
                {!! $loadingIndicator !!}
            @else
                <x-filament-support::loading-indicator x-cloak wire:loading.delay class="w-7 h-7" />
            @endif
        </div>
    @endif
</div>
