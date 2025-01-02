<x-filament-widgets::widget>
    <x-filament::section>
        <x-filament::card>
            <h2 class="text-lg font-bold">Assigned Doctors</h2>
            <ul>
                @foreach ($this->getDoctors() as $doctor)
                    <li>{{ $doctor->name }}</li>
                @endforeach
            </ul>
        </x-filament::card>
    </x-filament::section>
</x-filament-widgets::widget>
