<div class="mt-2">
    @if ($rows->hasPages())
        <div class="">
            <div class="flex justify-between">
                <div class="text-xs xs:text-sm {{ $themeConfig['pagination']['text'] }}">
                    {{ucfirst(__('yat::yat.showing'))}} {{ $rows->firstItem() }} {{ucfirst(__('yat::yat.to'))}} {{ $rows->lastItem() }} {{ucfirst(__('yat::yat.of'))}} {{ $rows->total() }} {{ucfirst(__('yat::yat.entries'))}}
                </div>
                <div class="inline-flex">
                    @php
                        $paginationThemeConfig = $themeConfig;
                        if (!empty($buttonThemeOverride) && $buttonThemeOverride !== $theme) {
                            $paginationThemeConfig = $this->getThemeConfig($buttonThemeOverride);
                        }
                    @endphp
                    {{ $rows->links('yat::livewire.parts.pagination-actions', ['themeConfig' => $paginationThemeConfig]) }}
                </div>
            </div>
        </div>
    @endif
</div>