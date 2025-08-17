

<x-mail::message>


    <x-mail::table>
        | Laravel       | Table         | Example       |
        | ------------- | :-----------: | ------------: |
        | Col 2 is      | Centered      | $10           |
        | Col 3 is      | Right-Aligned | $20           |
    </x-mail::table>
    <x-mail::button :url="''">
        Button Text
    </x-mail::button>



    Thanks,
    {{ config('app.name') }}
</x-mail::message>
